<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use Config\Services;

/**
 * API Security Filter with comprehensive protection
 * - Rate limiting
 * - Request validation
 * - Security headers
 * - IP blocking
 * - Request logging
 * - DDoS protection
 */
class ApiSecurityFilter implements FilterInterface
{
    /**
     * Rate limiting configuration
     */
    protected array $rateLimits = [
        'default' => [
            'requests' => 100,
            'window' => 3600, // 1 hour
            'burst' => 10
        ],
        'auth' => [
            'requests' => 20,
            'window' => 900, // 15 minutes
            'burst' => 5
        ],
        'dashboard' => [
            'requests' => 200,
            'window' => 3600,
            'burst' => 20
        ],
        'admin' => [
            'requests' => 500,
            'window' => 3600,
            'burst' => 50
        ]
    ];

    /**
     * Blocked IPs (can be loaded from database)
     */
    protected array $blockedIPs = [];

    /**
     * Suspicious patterns
     */
    protected array $suspiciousPatterns = [
        '/\.\./',
        '/<script[^>]*>.*?<\/script>/si',
        '/union\s+select/i',
        '/drop\s+table/i',
        '/insert\s+into/i',
        '/delete\s+from/i',
        '/update\s+.*set/i'
    ];

    /**
     * Before filter execution
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $clientIP = $request->getIPAddress();
        $uri = $request->getUri();
        $method = $request->getMethod();

        // Log incoming request
        $this->logRequest($request);

        // Check if IP is blocked
        if ($this->isIPBlocked($clientIP)) {
            return $this->blockedResponse();
        }

        // Validate request structure
        if (!$this->validateRequest($request)) {
            return $this->invalidRequestResponse();
        }

        // Check rate limiting
        if (!$this->checkRateLimit($request, $arguments)) {
            return $this->rateLimitResponse();
        }

        // Check for suspicious patterns
        if ($this->containsSuspiciousPatterns($request)) {
            return $this->suspiciousRequestResponse();
        }

        // Add security headers
        $this->addSecurityHeaders($request);

        return null;
    }

    /**
     * After filter execution
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Log response
        $this->logResponse($request, $response);

        // Add security headers to response
        $this->addResponseSecurityHeaders($response);
    }

    /**
     * Check if IP is blocked
     */
    protected function isIPBlocked(string $ip): bool
    {
        // Check hardcoded blocked IPs
        if (in_array($ip, $this->blockedIPs)) {
            return true;
        }

        // Check database for blocked IPs
        $cache = Services::cache();
        $blockedIPs = $cache->get('blocked_ips') ?? [];
        
        return in_array($ip, $blockedIPs);
    }

    /**
     * Validate request structure
     */
    protected function validateRequest(RequestInterface $request): bool
    {
        // Check Content-Type for POST/PUT requests
        $method = $request->getMethod();
        
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $contentType = $request->getHeaderLine('Content-Type');
            
            if (!in_array($contentType, [
                'application/json',
                'application/x-www-form-urlencoded',
                'multipart/form-data'
            ])) {
                return false;
            }
        }

        // Check required headers
        $requiredHeaders = ['User-Agent'];
        
        foreach ($requiredHeaders as $header) {
            if (empty($request->getHeaderLine($header))) {
                return false;
            }
        }

        // Check request size
        $contentLength = (int)$request->getHeaderLine('Content-Length');
        $maxSize = 10 * 1024 * 1024; // 10MB
        
        if ($contentLength > $maxSize) {
            return false;
        }

        return true;
    }

    /**
     * Check rate limiting
     */
    protected function checkRateLimit(RequestInterface $request, $arguments = null): bool
    {
        $clientIP = $request->getIPAddress();
        $uri = $request->getUri()->getPath();
        $method = $request->getMethod();

        // Determine rate limit based on endpoint
        $rateLimit = $this->getRateLimit($uri, $method);
        
        // Generate rate limit key
        $key = "rate_limit_{$clientIP}_{$uri}_{$method}";
        
        // Get current usage
        $cache = Services::cache();
        $usage = $cache->get($key) ?? [
            'requests' => 0,
            'window_start' => Time::now()->getTimestamp(),
            'burst_requests' => 0
        ];

        $now = Time::now()->getTimestamp();
        
        // Reset window if expired
        if ($now - $usage['window_start'] > $rateLimit['window']) {
            $usage = [
                'requests' => 0,
                'window_start' => $now,
                'burst_requests' => 0
            ];
        }

        // Check if request limit exceeded
        if ($usage['requests'] >= $rateLimit['requests']) {
            return false;
        }

        // Check burst limit
        if ($usage['burst_requests'] >= $rateLimit['burst']) {
            return false;
        }

        // Increment counters
        $usage['requests']++;
        $usage['burst_requests']++;
        
        // Update cache
        $ttl = $rateLimit['window'] - ($now - $usage['window_start']);
        $cache->save($key, $usage, $ttl);

        return true;
    }

    /**
     * Get rate limit for endpoint
     */
    protected function getRateLimit(string $uri, string $method): array
    {
        // Determine endpoint category
        if (strpos($uri, '/auth') !== false) {
            return $this->rateLimits['auth'];
        }
        
        if (strpos($uri, '/dashboard') !== false) {
            return $this->rateLimits['dashboard'];
        }
        
        if (strpos($uri, '/admin') !== false) {
            return $this->rateLimits['admin'];
        }

        return $this->rateLimits['default'];
    }

    /**
     * Check for suspicious patterns
     */
    protected function containsSuspiciousPatterns(RequestInterface $request): bool
    {
        $data = [
            $request->getUri()->getQuery(),
            $request->getBody(),
            json_encode($request->getPost()),
            json_encode($request->getGet())
        ];

        foreach ($data as $content) {
            if (empty($content)) continue;

            foreach ($this->suspiciousPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Add security headers
     */
    protected function addSecurityHeaders(RequestInterface $request): void
    {
        // Headers will be added to response in after filter
    }

    /**
     * Add security headers to response
     */
    protected function addResponseSecurityHeaders(ResponseInterface $response): void
    {
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('X-Frame-Options', 'DENY');
        $response->setHeader('X-XSS-Protection', '1; mode=block');
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->setHeader('Content-Security-Policy', "default-src 'self'");
        $response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    /**
     * Log request
     */
    protected function logRequest(RequestInterface $request): void
    {
        $logData = [
            'timestamp' => Time::now()->toISOString(),
            'ip' => $request->getIPAddress(),
            'method' => $request->getMethod(),
            'uri' => $request->getUri()->getPath(),
            'user_agent' => $request->getUserAgent(),
            'content_length' => $request->getHeaderLine('Content-Length'),
            'referer' => $request->getHeaderLine('Referer')
        ];

        log_message('info', 'API Request: ' . json_encode($logData));
    }

    /**
     * Log response
     */
    protected function logResponse(RequestInterface $request, ResponseInterface $response): void
    {
        $logData = [
            'timestamp' => Time::now()->toISOString(),
            'ip' => $request->getIPAddress(),
            'method' => $request->getMethod(),
            'uri' => $request->getUri()->getPath(),
            'status_code' => $response->getStatusCode(),
            'response_size' => strlen($response->getBody())
        ];

        log_message('info', 'API Response: ' . json_encode($logData));
    }

    /**
     * Blocked IP response
     */
    protected function blockedResponse(): ResponseInterface
    {
        $response = Services::response();
        return $response
            ->setStatusCode(403)
            ->setJSON([
                'success' => false,
                'message' => 'Access denied',
                'error_code' => 'IP_BLOCKED'
            ]);
    }

    /**
     * Invalid request response
     */
    protected function invalidRequestResponse(): ResponseInterface
    {
        $response = Services::response();
        return $response
            ->setStatusCode(400)
            ->setJSON([
                'success' => false,
                'message' => 'Invalid request structure',
                'error_code' => 'INVALID_REQUEST'
            ]);
    }

    /**
     * Rate limit response
     */
    protected function rateLimitResponse(): ResponseInterface
    {
        $response = Services::response();
        return $response
            ->setStatusCode(429)
            ->setJSON([
                'success' => false,
                'message' => 'Too many requests',
                'error_code' => 'RATE_LIMIT_EXCEEDED'
            ]);
    }

    /**
     * Suspicious request response
     */
    protected function suspiciousRequestResponse(): ResponseInterface
    {
        $response = Services::response();
        return $response
            ->setStatusCode(400)
            ->setJSON([
                'success' => false,
                'message' => 'Suspicious request detected',
                'error_code' => 'SUSPICIOUS_REQUEST'
            ]);
    }

    /**
     * Block IP temporarily
     */
    public function blockIP(string $ip, int $duration = 3600): bool
    {
        $cache = Services::cache();
        
        // Add to temporary block list
        $blockedIPs = $cache->get('temp_blocked_ips') ?? [];
        $blockedIPs[$ip] = Time::now()->getTimestamp() + $duration;
        
        return $cache->save('temp_blocked_ips', $blockedIPs, $duration);
    }

    /**
     * Get rate limit status
     */
    public function getRateLimitStatus(string $ip, string $uri, string $method): array
    {
        $key = "rate_limit_{$ip}_{$uri}_{$method}";
        $cache = Services::cache();
        $usage = $cache->get($key) ?? [
            'requests' => 0,
            'window_start' => Time::now()->getTimestamp(),
            'burst_requests' => 0
        ];

        $rateLimit = $this->getRateLimit($uri, $method);
        $now = Time::now()->getTimestamp();
        $remainingTime = $rateLimit['window'] - ($now - $usage['window_start']);

        return [
            'requests_used' => $usage['requests'],
            'requests_limit' => $rateLimit['requests'],
            'requests_remaining' => max(0, $rateLimit['requests'] - $usage['requests']),
            'window_reset' => $remainingTime > 0 ? Time::createFromTimestamp($usage['window_start'] + $rateLimit['window'])->toISOString() : null,
            'burst_used' => $usage['burst_requests'],
            'burst_limit' => $rateLimit['burst'],
            'burst_remaining' => max(0, $rateLimit['burst'] - $usage['burst_requests'])
        ];
    }
}
