<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use Config\Services;

/**
 * Enhanced Base Controller with advanced features
 * - Comprehensive error handling
 * - Standardized API responses
 * - Performance monitoring
 * - Security enhancements
 * - Request validation
 */
class EnhancedBaseController extends BaseController
{
    /**
     * Request start time for performance monitoring
     */
    protected $requestStartTime;

    /**
     * Current user session
     */
    protected $currentUser;

    /**
     * Constructor
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \CodeIgniter\Log\Logger $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->requestStartTime = microtime(true);
        $this->currentUser = session()->get('user');
        
        // Log request start
        $this->logRequest();
    }

    /**
     * Send standardized success response
     */
    protected function sendSuccess($data = null, string $message = 'Success', int $statusCode = 200): ResponseInterface
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => Time::now()->toISOString(),
            'request_id' => $this->generateRequestId(),
            'execution_time' => $this->getExecutionTime()
        ];

        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($response);
    }

    /**
     * Send standardized error response
     */
    protected function sendError(string $message, $errors = null, int $statusCode = 400): ResponseInterface
    {
        $response = [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => Time::now()->toISOString(),
            'request_id' => $this->generateRequestId(),
            'execution_time' => $this->getExecutionTime()
        ];

        // Log error
        $this->logError($message, $errors, $statusCode);

        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($response);
    }

    /**
     * Send validation error response
     */
    protected function sendValidationError($errors): ResponseInterface
    {
        return $this->sendError('Validation failed', $errors, 422);
    }

    /**
     * Send unauthorized response
     */
    protected function sendUnauthorized(string $message = 'Unauthorized access'): ResponseInterface
    {
        return $this->sendError($message, null, 401);
    }

    /**
     * Send forbidden response
     */
    protected function sendForbidden(string $message = 'Access denied'): ResponseInterface
    {
        return $this->sendError($message, null, 403);
    }

    /**
     * Send not found response
     */
    protected function sendNotFound(string $message = 'Resource not found'): ResponseInterface
    {
        return $this->sendError($message, null, 404);
    }

    /**
     * Send server error response
     */
    protected function sendServerError(string $message = 'Internal server error'): ResponseInterface
    {
        return $this->sendError($message, null, 500);
    }

    /**
     * Validate request data
     */
    protected function validateRequest(array $rules, array $customMessages = []): bool
    {
        $validation = Services::validation();
        $validation->setRules($rules, $customMessages);

        if (!$validation->withRequest($this->request)->run()) {
            $this->sendValidationError($validation->getErrors());
            return false;
        }

        return true;
    }

    /**
     * Get JSON request data
     */
    protected function getJsonData(): array
    {
        $data = $this->request->getJSON(true);
        
        if (empty($data)) {
            $this->sendError('Invalid JSON data or empty request body');
            exit();
        }

        return $data;
    }

    /**
     * Check if user is authenticated
     */
    protected function requireAuth(): bool
    {
        if (!$this->currentUser) {
            $this->sendUnauthorized('Authentication required');
            return false;
        }

        return true;
    }

    /**
     * Check user role permissions
     */
    protected function requireRole(array $allowedRoles): bool
    {
        if (!$this->requireAuth()) {
            return false;
        }

        $userRole = $this->currentUser['role'] ?? null;
        
        if (!in_array($userRole, $allowedRoles)) {
            $this->sendForbidden('Insufficient permissions');
            return false;
        }

        return true;
    }

    /**
     * Check branch access permissions
     */
    protected function requireBranchAccess(?int $branchId = null): bool
    {
        if (!$this->requireAuth()) {
            return false;
        }

        $userBranchId = $this->currentUser['branch_id'] ?? null;
        $userRole = $this->currentUser['role'] ?? null;

        // Admin can access all branches
        if ($userRole === 'admin') {
            return true;
        }

        // If no specific branch required, user can access their own branch
        if ($branchId === null) {
            return true;
        }

        // Check if user can access the requested branch
        if ($userBranchId != $branchId) {
            $this->sendForbidden('Cannot access this branch');
            return false;
        }

        return true;
    }

    /**
     * Sanitize input data
     */
    protected function sanitizeInput(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeInput($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Generate unique request ID
     */
    protected function generateRequestId(): string
    {
        return uniqid('req_', true);
    }

    /**
     * Get request execution time
     */
    protected function getExecutionTime(): float
    {
        return round((microtime(true) - $this->requestStartTime) * 1000, 2);
    }

    /**
     * Log request information
     */
    protected function logRequest(): void
    {
        $logData = [
            'method' => $this->request->getMethod(),
            'uri' => $this->request->getUri()->getPath(),
            'ip' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent(),
            'user_id' => $this->currentUser['user_id'] ?? null,
            'request_id' => $this->generateRequestId()
        ];

        log_message('info', 'API Request: ' . json_encode($logData));
    }

    /**
     * Log error information
     */
    protected function logError(string $message, $errors = null, int $statusCode = 500): void
    {
        $logData = [
            'message' => $message,
            'errors' => $errors,
            'status_code' => $statusCode,
            'method' => $this->request->getMethod(),
            'uri' => $this->request->getUri()->getPath(),
            'ip' => $this->request->getIPAddress(),
            'user_id' => $this->currentUser['user_id'] ?? null,
            'request_id' => $this->generateRequestId()
        ];

        log_message('error', 'API Error: ' . json_encode($logData));
    }

    /**
     * Paginate results
     */
    protected function paginate($query, int $defaultLimit = 20, int $maxLimit = 100): array
    {
        $page = $this->request->getGet('page') ?? 1;
        $limit = min($this->request->getGet('limit') ?? $defaultLimit, $maxLimit);
        $offset = ($page - 1) * $limit;

        $total = $query->countAllResults(false);
        $data = $query->limit($limit, $offset)->get()->getResult();

        return [
            'data' => $data,
            'pagination' => [
                'current_page' => (int)$page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit),
                'has_next' => $page < ceil($total / $limit),
                'has_prev' => $page > 1
            ]
        ];
    }

    /**
     * Get cache service with enhanced features
     */
    protected function getCache(): \CodeIgniter\Cache\CacheInterface
    {
        return Services::cache();
    }

    /**
     * Cache data with automatic key generation
     */
    protected function cacheData(string $key, $data, int $ttl = 3600): bool
    {
        $cacheKey = 'hms_' . $key . '_' . md5(json_encode([
            'user_id' => $this->currentUser['user_id'] ?? null,
            'branch_id' => $this->currentUser['branch_id'] ?? null,
            'role' => $this->currentUser['role'] ?? null
        ]));

        return $this->getCache()->save($cacheKey, $data, $ttl);
    }

    /**
     * Get cached data
     */
    protected function getCachedData(string $key)
    {
        $cacheKey = 'hms_' . $key . '_' . md5(json_encode([
            'user_id' => $this->currentUser['user_id'] ?? null,
            'branch_id' => $this->currentUser['branch_id'] ?? null,
            'role' => $this->currentUser['role'] ?? null
        ]));

        return $this->getCache()->get($cacheKey);
    }

    /**
     * Clear cache for specific key
     */
    protected function clearCache(string $key): bool
    {
        $cacheKey = 'hms_' . $key . '_' . md5(json_encode([
            'user_id' => $this->currentUser['user_id'] ?? null,
            'branch_id' => $this->currentUser['branch_id'] ?? null,
            'role' => $this->currentUser['role'] ?? null
        ]));

        return $this->getCache()->delete($cacheKey);
    }
}
