<?php

namespace App\Libraries;

use CodeIgniter\HTTP\RequestInterface;

/**
 * Security Library - Enhanced security features for HMS
 * Provides input sanitization, XSS protection, and security utilities
 */
class SecurityLibrary
{
    protected $request;
    
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Sanitize input data
     */
    public function sanitizeInput(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeInput($value);
            } else {
                $sanitized[$key] = $this->sanitizeString($value);
            }
        }
        
        return $sanitized;
    }

    /**
     * Sanitize string input
     */
    protected function sanitizeString(string $value): string
    {
        // Remove HTML tags
        $value = strip_tags($value);
        
        // Remove special characters that could be dangerous
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        
        // Remove extra whitespace
        $value = trim($value);
        
        return $value;
    }

    /**
     * Validate password strength
     */
    public function validatePasswordStrength(string $password): array
    {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Validate file upload
     */
    public function validateFileUpload(string $fieldName, array $allowedTypes = [], int $maxSize = 5242880): array
    {
        $file = $this->request->getFile($fieldName);
        
        if (!$file || !$file->isValid()) {
            return [
                'valid' => false,
                'error' => 'No file uploaded or file is invalid'
            ];
        }
        
        if ($file->getSize() > $maxSize) {
            return [
                'valid' => false,
                'error' => 'File size exceeds maximum allowed size'
            ];
        }
        
        if (!empty($allowedTypes) && !in_array($file->getMimeType(), $allowedTypes)) {
            return [
                'valid' => false,
                'error' => 'File type not allowed'
            ];
        }
        
        return [
            'valid' => true,
            'file' => $file
        ];
    }

    /**
     * Generate secure token
     */
    public function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Check for suspicious activity
     */
    public function isSuspiciousActivity(array $data): bool
    {
        $suspiciousPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/eval\s*\(/i',
            '/exec\s*\(/i',
            '/system\s*\(/i'
        ];
        
        foreach ($data as $value) {
            if (is_string($value)) {
                foreach ($suspiciousPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }

    /**
     * Get client IP address
     */
    public function getClientIP(): string
    {
        return $this->request->getIPAddress();
    }

    /**
     * Get user agent
     */
    public function getUserAgent(): string
    {
        return $this->request->getUserAgent();
    }

    /**
     * Encrypt data
     */
    public function encrypt(string $data): string
    {
        $key = config('Encryption')->key;
        return openssl_encrypt($data, 'aes-256-cbc', $key, 0, substr(md5($key), 0, 16));
    }

    /**
     * Decrypt data
     */
    public function decrypt(string $encryptedData): string
    {
        $key = config('Encryption')->key;
        return openssl_decrypt($encryptedData, 'aes-256-cbc', $key, 0, substr(md5($key), 0, 16));
    }

    /**
     * Generate CSRF token
     */
    public function generateCSRFToken(): string
    {
        return $this->generateToken(32);
    }

    /**
     * Validate CSRF token
     */
    public function validateCSRFToken(string $token): bool
    {
        return session()->get('csrf_token') === $token;
    }

    /**
     * Check rate limiting
     */
    public function checkRateLimit(string $identifier, int $maxRequests = 100, int $timeWindow = 3600): bool
    {
        $cache = \Config\Services::cache();
        $key = "rate_limit_{$identifier}";
        $requests = $cache->get($key) ?: 0;
        
        if ($requests >= $maxRequests) {
            return false;
        }
        
        $cache->save($key, $requests + 1, $timeWindow);
        return true;
    }

    /**
     * Log security event
     */
    public function logSecurityEvent(string $event, array $details = []): void
    {
        $logData = [
            'event' => $event,
            'ip' => $this->getClientIP(),
            'user_agent' => $this->getUserAgent(),
            'timestamp' => date('Y-m-d H:i:s'),
            'details' => $details
        ];
        
        log_message('warning', 'Security Event: ' . json_encode($logData));
    }
}