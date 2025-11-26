<?php

if (!function_exists('check_age')) {
    /**
     * Custom validation rule for checking age
     */
    function check_age(string $date): bool
    {
        try {
            $birthDate = new DateTime($date);
            $currentDate = new DateTime();
            $age = $currentDate->diff($birthDate)->y;
            
            return $age >= 0 && $age <= 150;
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('validate_phone')) {
    /**
     * Enhanced phone validation
     */
    function validate_phone(string $phone): bool
    {
        // Remove all non-digit characters except +
        $cleanPhone = preg_replace('/[^\d+]/', '', $phone);
        
        // Check if it starts with + followed by digits or just digits
        return preg_match('/^\+?[1-9]\d{9,14}$/', $cleanPhone);
    }
}

if (!function_exists('validate_strong_password')) {
    /**
     * Strong password validation
     */
    function validate_strong_password(string $password): array
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
        
        if (!preg_match('/\d/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if (!preg_match('/[@$!%*?&]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }
        
        return $errors;
    }
}

if (!function_exists('sanitize_input')) {
    /**
     * Sanitize user input
     */
    function sanitize_input(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            } elseif (is_array($value)) {
                $sanitized[$key] = sanitize_input($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
}

if (!function_exists('generate_unique_id')) {
    /**
     * Generate unique ID for various entities
     */
    function generate_unique_id(string $prefix = 'ID'): string
    {
        return $prefix . date('Y') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}
