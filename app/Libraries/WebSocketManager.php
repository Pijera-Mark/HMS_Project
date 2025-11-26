<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;
use Config\Services;

/**
 * WebSocket Manager for real-time dashboard updates
 * - Live notifications
 * - Real-time statistics
 * - Event broadcasting
 * - Client connection management
 * - Message queuing
 * - Authentication
 */
class WebSocketManager
{
    protected array $clients = [];
    protected array $channels = [];
    protected array $messageQueue = [];
    protected array $config;
    protected bool $isRunning = false;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'host' => 'localhost',
            'port' => 8080,
            'max_connections' => 100,
            'heartbeat_interval' => 30,
            'message_queue_size' => 1000,
            'enable_authentication' => true,
            'enable_logging' => true
        ], $config);
    }

    /**
     * Start WebSocket server
     */
    public function start(): void
    {
        $this->isRunning = true;
        
        // Create socket
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        
        if (!$socket) {
            $this->logError('Failed to create socket: ' . socket_strerror(socket_last_error()));
            return;
        }

        // Set socket options
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
        
        // Bind socket
        if (!socket_bind($socket, $this->config['host'], $this->config['port'])) {
            $this->logError('Failed to bind socket: ' . socket_strerror(socket_last_error()));
            return;
        }

        // Listen for connections
        if (!socket_listen($socket, $this->config['max_connections'])) {
            $this->logError('Failed to listen on socket: ' . socket_strerror(socket_last_error()));
            return;
        }

        $this->logInfo("WebSocket server started on {$this->config['host']}:{$this->config['port']}");

        // Main server loop
        $read = [$socket];
        $write = [];
        $except = [];

        while ($this->isRunning) {
            // Select sockets
            $changed = socket_select($read, $write, $except, 0, 100000);

            if ($changed === false) {
                $this->logError('Socket select error: ' . socket_strerror(socket_last_error()));
                break;
            }

            // Handle new connections
            if (in_array($socket, $read)) {
                $newSocket = socket_accept($socket);
                
                if ($newSocket) {
                    $this->handleNewConnection($newSocket);
                }
                
                // Remove the listening socket from read array
                $key = array_search($socket, $read);
                unset($read[$key]);
            }

            // Handle client messages
            foreach ($read as $clientSocket) {
                $this->handleClientMessage($clientSocket);
            }

            // Send queued messages
            $this->processMessageQueue();

            // Send heartbeat
            $this->sendHeartbeat();
        }

        socket_close($socket);
    }

    /**
     * Stop WebSocket server
     */
    public function stop(): void
    {
        $this->isRunning = false;
        
        // Close all client connections
        foreach ($this->clients as $clientId => $client) {
            socket_close($client['socket']);
        }
        
        $this->clients = [];
        $this->channels = [];
        $this->logInfo('WebSocket server stopped');
    }

    /**
     * Handle new client connection
     */
    protected function handleNewConnection($socket): void
    {
        // Get client headers
        $headers = $this->readHeaders($socket);
        
        // Perform WebSocket handshake
        if (!$this->performHandshake($socket, $headers)) {
            socket_close($socket);
            return;
        }

        // Generate client ID
        $clientId = uniqid('client_', true);
        
        // Add client
        $this->clients[$clientId] = [
            'socket' => $socket,
            'id' => $clientId,
            'connected_at' => Time::now()->getTimestamp(),
            'last_ping' => Time::now()->getTimestamp(),
            'authenticated' => false,
            'user_id' => null,
            'channels' => []
        ];

        $this->logInfo("New client connected: {$clientId}");

        // Send welcome message
        $this->sendToClient($clientId, [
            'type' => 'welcome',
            'message' => 'Connected to HMS WebSocket server',
            'client_id' => $clientId,
            'timestamp' => Time::now()->toISOString()
        ]);
    }

    /**
     * Handle client message
     */
    protected function handleClientMessage($socket): void
    {
        $clientId = $this->getClientIdBySocket($socket);
        
        if (!$clientId) {
            return;
        }

        // Read message
        $data = socket_read($socket, 4096, PHP_BINARY_READ);
        
        if ($data === false) {
            $this->disconnectClient($clientId, 'Read error');
            return;
        }

        if (empty($data)) {
            return;
        }

        // Decode WebSocket frame
        $message = $this->decodeFrame($data);
        
        if ($message === null) {
            return;
        }

        // Update last ping
        $this->clients[$clientId]['last_ping'] = Time::now()->getTimestamp();

        // Parse message
        try {
            $data = json_decode($message, true);
            
            if ($data === null) {
                $this->sendError($clientId, 'Invalid JSON format');
                return;
            }

            $this->processMessage($clientId, $data);
            
        } catch (\Exception $e) {
            $this->logError("Error processing message from {$clientId}: " . $e->getMessage());
            $this->sendError($clientId, 'Message processing error');
        }
    }

    /**
     * Process client message
     */
    protected function processMessage(string $clientId, array $data): void
    {
        $type = $data['type'] ?? 'unknown';
        
        switch ($type) {
            case 'authenticate':
                $this->handleAuthentication($clientId, $data);
                break;
                
            case 'subscribe':
                $this->handleSubscription($clientId, $data);
                break;
                
            case 'unsubscribe':
                $this->handleUnsubscription($clientId, $data);
                break;
                
            case 'ping':
                $this->handlePing($clientId);
                break;
                
            default:
                $this->sendError($clientId, 'Unknown message type: ' . $type);
        }
    }

    /**
     * Handle client authentication
     */
    protected function handleAuthentication(string $clientId, array $data): void
    {
        if (!$this->config['enable_authentication']) {
            $this->clients[$clientId]['authenticated'] = true;
            $this->sendToClient($clientId, ['type' => 'authenticated', 'status' => 'success']);
            return;
        }

        $token = $data['token'] ?? null;
        
        if (!$token) {
            $this->sendError($clientId, 'Authentication token required');
            return;
        }

        // Validate token (this would integrate with your auth system)
        $user = $this->validateToken($token);
        
        if (!$user) {
            $this->sendError($clientId, 'Invalid authentication token');
            return;
        }

        // Update client authentication status
        $this->clients[$clientId]['authenticated'] = true;
        $this->clients[$clientId]['user_id'] = $user['user_id'];
        
        $this->sendToClient($clientId, [
            'type' => 'authenticated',
            'status' => 'success',
            'user' => $user
        ]);

        $this->logInfo("Client {$clientId} authenticated as user {$user['user_id']}");
    }

    /**
     * Handle channel subscription
     */
    protected function handleSubscription(string $clientId, array $data): void
    {
        if (!$this->clients[$clientId]['authenticated']) {
            $this->sendError($clientId, 'Authentication required');
            return;
        }

        $channel = $data['channel'] ?? null;
        
        if (!$channel) {
            $this->sendError($clientId, 'Channel name required');
            return;
        }

        // Check if user has permission to subscribe to this channel
        if (!$this->canSubscribeToChannel($clientId, $channel)) {
            $this->sendError($clientId, 'Permission denied for channel: ' . $channel);
            return;
        }

        // Add client to channel
        if (!isset($this->channels[$channel])) {
            $this->channels[$channel] = [];
        }
        
        if (!in_array($clientId, $this->channels[$channel])) {
            $this->channels[$channel][] = $clientId;
            $this->clients[$clientId]['channels'][] = $channel;
        }

        $this->sendToClient($clientId, [
            'type' => 'subscribed',
            'channel' => $channel,
            'status' => 'success'
        ]);

        $this->logInfo("Client {$clientId} subscribed to channel: {$channel}");
    }

    /**
     * Handle channel unsubscription
     */
    protected function handleUnsubscription(string $clientId, array $data): void
    {
        $channel = $data['channel'] ?? null;
        
        if (!$channel) {
            $this->sendError($clientId, 'Channel name required');
            return;
        }

        // Remove client from channel
        if (isset($this->channels[$channel])) {
            $key = array_search($clientId, $this->channels[$channel]);
            if ($key !== false) {
                unset($this->channels[$channel][$key]);
            }
        }

        // Remove channel from client
        $key = array_search($channel, $this->clients[$clientId]['channels']);
        if ($key !== false) {
            unset($this->clients[$clientId]['channels'][$key]);
        }

        $this->sendToClient($clientId, [
            'type' => 'unsubscribed',
            'channel' => $channel,
            'status' => 'success'
        ]);

        $this->logInfo("Client {$clientId} unsubscribed from channel: {$channel}");
    }

    /**
     * Handle ping message
     */
    protected function handlePing(string $clientId): void
    {
        $this->sendToClient($clientId, [
            'type' => 'pong',
            'timestamp' => Time::now()->toISOString()
        ]);
    }

    /**
     * Broadcast message to channel
     */
    public function broadcast(string $channel, array $message): bool
    {
        if (!isset($this->channels[$channel])) {
            return false;
        }

        $message['type'] = 'broadcast';
        $message['channel'] = $channel;
        $message['timestamp'] = Time::now()->toISOString();

        $success = true;
        
        foreach ($this->channels[$channel] as $clientId) {
            if (!$this->sendToClient($clientId, $message)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Send message to specific client
     */
    public function sendToClient(string $clientId, array $message): bool
    {
        if (!isset($this->clients[$clientId])) {
            return false;
        }

        $socket = $this->clients[$clientId]['socket'];
        $data = json_encode($message);
        
        if ($data === false) {
            return false;
        }

        // Encode WebSocket frame
        $frame = $this->encodeFrame($data);
        
        return socket_write($socket, $frame, strlen($frame)) !== false;
    }

    /**
     * Send error message to client
     */
    protected function sendError(string $clientId, string $error): void
    {
        $this->sendToClient($clientId, [
            'type' => 'error',
            'error' => $error,
            'timestamp' => Time::now()->toISOString()
        ]);
    }

    /**
     * Disconnect client
     */
    protected function disconnectClient(string $clientId, string $reason = ''): void
    {
        if (!isset($this->clients[$clientId])) {
            return;
        }

        // Remove from all channels
        foreach ($this->clients[$clientId]['channels'] as $channel) {
            if (isset($this->channels[$channel])) {
                $key = array_search($clientId, $this->channels[$channel]);
                if ($key !== false) {
                    unset($this->channels[$channel][$key]);
                }
            }
        }

        // Close socket
        socket_close($this->clients[$clientId]['socket']);
        
        // Remove client
        unset($this->clients[$clientId]);
        
        $this->logInfo("Client {$clientId} disconnected: {$reason}");
    }

    /**
     * Send heartbeat to all clients
     */
    protected function sendHeartbeat(): void
    {
        static $lastHeartbeat = 0;
        
        $now = Time::now()->getTimestamp();
        
        if ($now - $lastHeartbeat < $this->config['heartbeat_interval']) {
            return;
        }
        
        $lastHeartbeat = $now;
        
        foreach ($this->clients as $clientId => $client) {
            // Check if client is still alive
            if ($now - $client['last_ping'] > $this->config['heartbeat_interval'] * 2) {
                $this->disconnectClient($clientId, 'Heartbeat timeout');
                continue;
            }
            
            // Send ping
            $this->sendToClient($clientId, [
                'type' => 'ping',
                'timestamp' => Time::now()->toISOString()
            ]);
        }
    }

    /**
     * Process message queue
     */
    protected function processMessageQueue(): void
    {
        if (empty($this->messageQueue)) {
            return;
        }

        foreach ($this->messageQueue as $key => $message) {
            $this->broadcast($message['channel'], $message['data']);
            unset($this->messageQueue[$key]);
        }
    }

    /**
     * Queue message for broadcasting
     */
    public function queueMessage(string $channel, array $message): void
    {
        $this->messageQueue[] = [
            'channel' => $channel,
            'data' => $message,
            'timestamp' => Time::now()->getTimestamp()
        ];

        // Limit queue size
        if (count($this->messageQueue) > $this->config['message_queue_size']) {
            array_shift($this->messageQueue);
        }
    }

    /**
     * Send dashboard updates
     */
    public function sendDashboardUpdate(string $type, array $data, ?int $branchId = null): void
    {
        $channel = $branchId ? "dashboard_{$branchId}" : 'dashboard_global';
        
        $message = [
            'type' => 'dashboard_update',
            'update_type' => $type,
            'data' => $data,
            'branch_id' => $branchId
        ];

        $this->broadcast($channel, $message);
    }

    /**
     * Send notification
     */
    public function sendNotification(array $notification, ?int $userId = null): void
    {
        $channel = $userId ? "user_{$userId}" : 'notifications_global';
        
        $message = [
            'type' => 'notification',
            'notification' => $notification
        ];

        $this->broadcast($channel, $message);
    }

    /**
     * Get server statistics
     */
    public function getStats(): array
    {
        return [
            'connected_clients' => count($this->clients),
            'active_channels' => count($this->channels),
            'queued_messages' => count($this->messageQueue),
            'is_running' => $this->isRunning,
            'uptime' => $this->isRunning ? time() - $this->startTime : 0
        ];
    }

    /**
     * WebSocket handshake
     */
    protected function performHandshake($socket, array $headers): bool
    {
        $key = $headers['Sec-WebSocket-Key'] ?? null;
        
        if (!$key) {
            return false;
        }

        $acceptKey = base64_encode(pack('H*', sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        
        $response = "HTTP/1.1 101 Switching Protocols\r\n" .
                   "Upgrade: websocket\r\n" .
                   "Connection: Upgrade\r\n" .
                   "Sec-WebSocket-Accept: {$acceptKey}\r\n" .
                   "\r\n";

        return socket_write($socket, $response, strlen($response)) !== false;
    }

    /**
     * Read headers from socket
     */
    protected function readHeaders($socket): array
    {
        $headers = '';
        while (true) {
            $data = socket_read($socket, 4096, PHP_NORMAL_READ);
            if ($data === false || $data === '') {
                break;
            }
            $headers .= $data;
            if (strpos($headers, "\r\n\r\n") !== false) {
                break;
            }
        }

        $parsed = [];
        foreach (explode("\r\n", $headers) as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $parsed[trim($key)] = trim($value);
            }
        }

        return $parsed;
    }

    /**
     * Decode WebSocket frame
     */
    protected function decodeFrame(string $data): ?string
    {
        if (strlen($data) < 2) {
            return null;
        }

        $firstByte = ord($data[0]);
        $secondByte = ord($data[1]);

        $masked = ($secondByte & 0x80) === 0x80;
        $payloadLength = $secondByte & 0x7F;
        $offset = 2;

        // Extended payload length
        if ($payloadLength === 126) {
            $payloadLength = (ord($data[2]) << 8) | ord($data[3]);
            $offset = 4;
        } elseif ($payloadLength === 127) {
            $payloadLength = (ord($data[2]) << 56) | (ord($data[3]) << 48) | 
                           (ord($data[4]) << 40) | (ord($data[5]) << 32) | 
                           (ord($data[6]) << 24) | (ord($data[7]) << 16) | 
                           (ord($data[8]) << 8) | ord($data[9]);
            $offset = 10;
        }

        // Masking key
        if ($masked) {
            $maskingKey = substr($data, $offset, 4);
            $offset += 4;
        }

        // Payload data
        $payload = substr($data, $offset, $payloadLength);

        // Unmask payload
        if ($masked) {
            for ($i = 0; $i < $payloadLength; $i++) {
                $payload[$i] = $payload[$i] ^ $maskingKey[$i % 4];
            }
        }

        return $payload;
    }

    /**
     * Encode WebSocket frame
     */
    protected function encodeFrame(string $data): string
    {
        $length = strlen($data);
        $frame = '';

        // First byte: FIN=1, RSV=000, Opcode=0001 (text)
        $frame .= chr(0x81);

        // Payload length
        if ($length <= 125) {
            $frame .= chr($length);
        } elseif ($length <= 65535) {
            $frame .= chr(126) . chr($length >> 8) . chr($length & 0xFF);
        } else {
            $frame .= chr(127) . 
                     chr($length >> 56) . chr(($length >> 48) & 0xFF) . 
                     chr(($length >> 40) & 0xFF) . chr(($length >> 32) & 0xFF) . 
                     chr(($length >> 24) & 0xFF) . chr(($length >> 16) & 0xFF) . 
                     chr(($length >> 8) & 0xFF) . chr($length & 0xFF);
        }

        // Payload data
        $frame .= $data;

        return $frame;
    }

    /**
     * Get client ID by socket
     */
    protected function getClientIdBySocket($socket): ?string
    {
        foreach ($this->clients as $clientId => $client) {
            if ($client['socket'] === $socket) {
                return $clientId;
            }
        }
        return null;
    }

    /**
     * Validate authentication token
     */
    protected function validateToken(string $token): ?array
    {
        // This would integrate with your authentication system
        // For now, return a mock user
        if ($token === 'test-token') {
            return ['user_id' => 1, 'role' => 'admin', 'branch_id' => 1];
        }
        
        return null;
    }

    /**
     * Check if client can subscribe to channel
     */
    protected function canSubscribeToChannel(string $clientId, string $channel): bool
    {
        // Implement permission checking logic
        // For now, allow all authenticated clients
        return $this->clients[$clientId]['authenticated'];
    }

    /**
     * Log info message
     */
    protected function logInfo(string $message): void
    {
        if ($this->config['enable_logging']) {
            log_message('info', '[WebSocket] ' . $message);
        }
    }

    /**
     * Log error message
     */
    protected function logError(string $message): void
    {
        if ($this->config['enable_logging']) {
            log_message('error', '[WebSocket] ' . $message);
        }
    }
}
