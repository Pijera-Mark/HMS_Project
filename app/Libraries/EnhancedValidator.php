<?php

namespace App\Libraries;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Validation\ValidationInterface;

/**
 * Enhanced Validation and Sanitization System
 * - Advanced validation rules
 * - Input sanitization
 * - Security filtering
 * - Custom validation messages
 * - File upload validation
 * - XSS protection
 */
class EnhancedValidator
{
    protected ValidationInterface $validator;
    protected RequestInterface $request;
    protected array $errors = [];
    protected array $sanitized = [];

    /**
     * Validation rules configuration
     */
    protected array $rules = [
        'common' => [
            'required' => 'required',
            'email' => 'valid_email',
            'phone' => 'regex_match[/^[\+]?[0-9]{10,15}$/]',
            'name' => 'alpha_space|min_length[2]|max_length[50]',
            'password' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
            'date' => 'valid_date[Y-m-d]',
            'datetime' => 'valid_date[Y-m-d H:i:s]',
            'numeric' => 'numeric|greater_than[0]',
            'positive_int' => 'integer|greater_than[0]',
            'alpha_numeric' => 'alpha_numeric',
            'url' => 'valid_url'
        ],
        'medical' => [
            'blood_type' => 'in_list[A+,A-,B+,B-,AB+,AB-,O+,O-]',
            'gender' => 'in_list[Male,Female,Other]',
            'age' => 'integer|greater_than[0]|less_than[150]',
            'height' => 'numeric|greater_than[0]|less_than[300]',
            'weight' => 'numeric|greater_than[0]|less_than[500]',
            'temperature' => 'numeric|greater_than[0]|less_than[50]',
            'blood_pressure_systolic' => 'integer|greater_than[0]|less_than[300]',
            'blood_pressure_diastolic' => 'integer|greater_than[0]|less_than[200]',
            'heart_rate' => 'integer|greater_than[0]|less_than[300]',
            'oxygen_saturation' => 'numeric|greater_than_equal[0]|less_than_equal[100]'
        ],
        'hospital' => [
            'branch_id' => 'integer|is_not_unique[branches.id]',
            'department' => 'alpha_space|min_length[2]|max_length[100]',
            'room_number' => 'alpha_numeric|max_length[20]',
            'bed_number' => 'alpha_numeric|max_length[10]',
            'appointment_status' => 'in_list[scheduled,completed,cancelled,no-show]',
            'admission_status' => 'in_list[admitted,discharged,transferred]',
            'invoice_status' => 'in_list[paid,unpaid,partial,overdue]',
            'medicine_dosage' => 'regex_match[/^[0-9]+(\.[0-9]+)?(mg|ml|tablet|capsule|drops|units)?$/i]',
            'medicine_frequency' => 'in_list[once,twice,thrice,as_needed,qid,qod,prn]',
            'urgency' => 'in_list[low,medium,high,critical]'
        ]
    ];

    /**
     * Custom validation messages
     */
    protected array $messages = [
        'required' => 'The {field} field is required.',
        'valid_email' => 'The {field} field must contain a valid email address.',
        'min_length' => 'The {field} field must be at least {param} characters long.',
        'max_length' => 'The {field} field cannot exceed {param} characters.',
        'alpha_space' => 'The {field} field may only contain alphabetic characters and spaces.',
        'alpha_numeric' => 'The {field} field may only contain alphanumeric characters.',
        'numeric' => 'The {field} field must contain only numbers.',
        'integer' => 'The {field} field must be an integer.',
        'greater_than' => 'The {field} field must be greater than {param}.',
        'less_than' => 'The {field} field must be less than {param}.',
        'in_list' => 'The {field} field must be one of: {param}.',
        'valid_date' => 'The {field} field must contain a valid date.',
        'valid_url' => 'The {field} field must contain a valid URL.',
        'regex_match' => 'The {field} field format is invalid.',
        'is_not_unique' => 'The selected {field} is invalid.',
        'password' => 'The {field} must be at least 8 characters long and contain uppercase, lowercase, number, and special character.'
    ];

    /**
     * Sanitization rules
     */
    protected array $sanitizers = [
        'string' => 'trim|strip_tags',
        'email' => 'trim|strtolower',
        'phone' => 'trim|regex_replace[/[^0-9\+]/]',
        'numeric' => 'trim|regex_replace[/[^0-9.]/]',
        'integer' => 'trim|regex_replace[/[^0-9-]/]',
        'alpha' => 'trim|regex_replace[/[^A-Za-z]/]',
        'alpha_numeric' => 'trim|regex_replace[/[^A-Za-z0-9]/]',
        'alpha_space' => 'trim|regex_replace[/[^A-Za-z\s]/]',
        'url' => 'trim|esc_url',
        'html' => 'trim|esc_html',
        'filename' => 'trim|sanitize_filename'
    ];

    public function __construct(ValidationInterface $validator, RequestInterface $request)
    {
        $this->validator = $validator;
        $this->request = $request;
    }

    /**
     * Validate request data
     */
    public function validate(array $rules, array $customMessages = []): bool
    {
        // Prepare validation rules
        $validationRules = [];
        
        foreach ($rules as $field => $rule) {
            if (is_string($rule)) {
                // Use predefined rule
                $validationRules[$field] = $this->getRule($rule);
            } elseif (is_array($rule)) {
                // Custom rule
                $validationRules[$field] = $rule;
            }
        }

        // Set custom messages
        $messages = array_merge($this->messages, $customMessages);

        // Run validation
        $this->validator->setRules($validationRules, $messages);
        
        if (!$this->validator->withRequest($this->request)->run()) {
            $this->errors = $this->validator->getErrors();
            return false;
        }

        return true;
    }

    /**
     * Validate and sanitize input
     */
    public function validateAndSanitize(array $rules, array $sanitizationRules = [], array $customMessages = []): bool
    {
        if (!$this->validate($rules, $customMessages)) {
            return false;
        }

        $this->sanitize($sanitizationRules);
        return true;
    }

    /**
     * Sanitize input data
     */
    public function sanitize(array $rules = []): array
    {
        $data = $this->request->getPost() ?? [];
        $sanitized = [];

        foreach ($data as $field => $value) {
            if (!is_string($value)) {
                $sanitized[$field] = $value;
                continue;
            }

            $sanitizer = $rules[$field] ?? $this->getDefaultSanitizer($field);
            $sanitized[$field] = $this->applySanitizer($value, $sanitizer);
        }

        $this->sanitized = $sanitized;
        return $sanitized;
    }

    /**
     * Validate file upload
     */
    public function validateFile(string $fieldName, array $rules = []): bool
    {
        $file = $this->request->getFile($fieldName);
        
        if (!$file || !$file->isValid()) {
            $this->errors[$fieldName] = 'No valid file uploaded.';
            return false;
        }

        // Default file validation rules
        $defaultRules = [
            'max_size' => 5 * 1024, // 5MB
            'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],
            'max_dims' => [2000, 2000] // Max width x height
        ];

        $rules = array_merge($defaultRules, $rules);

        // Check file size
        if ($file->getSize() > $rules['max_size'] * 1024) {
            $this->errors[$fieldName] = 'File size exceeds maximum limit of ' . $rules['max_size'] . 'KB.';
            return false;
        }

        // Check file type
        $extension = strtolower($file->getExtension());
        if (!in_array($extension, $rules['allowed_types'])) {
            $this->errors[$fieldName] = 'File type not allowed. Allowed types: ' . implode(', ', $rules['allowed_types']);
            return false;
        }

        // Check dimensions for images
        if (in_array($extension, ['jpg', 'jpeg', 'png']) && isset($rules['max_dims'])) {
            $info = getimagesize($file->getTempName());
            if ($info && ($info[0] > $rules['max_dims'][0] || $info[1] > $rules['max_dims'][1])) {
                $this->errors[$fieldName] = 'Image dimensions exceed maximum size of ' . $rules['max_dims'][0] . 'x' . $rules['max_dims'][1] . ' pixels.';
                return false;
            }
        }

        return true;
    }

    /**
     * Validate JSON input
     */
    public function validateJson(string $fieldName, array $rules = []): bool
    {
        $json = $this->request->getPost($fieldName);
        
        if (empty($json)) {
            $this->errors[$fieldName] = 'JSON data is required.';
            return false;
        }

        $data = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->errors[$fieldName] = 'Invalid JSON format: ' . json_last_error_msg();
            return false;
        }

        // Validate JSON structure
        if (!empty($rules)) {
            $this->validator->setRules($rules);
            
            if (!$this->validator->setData($data)->run()) {
                $this->errors = array_merge($this->errors, $this->validator->getErrors());
                return false;
            }
        }

        return true;
    }

    /**
     * Validate date range
     */
    public function validateDateRange(string $startField, string $endField): bool
    {
        $start = $this->request->getPost($startField);
        $end = $this->request->getPost($endField);

        if (empty($start) || empty($end)) {
            $this->errors[$startField] = 'Both start and end dates are required.';
            return false;
        }

        $startDate = new \DateTime($start);
        $endDate = new \DateTime($end);

        if ($startDate >= $endDate) {
            $this->errors[$endField] = 'End date must be after start date.';
            return false;
        }

        // Check if date range is reasonable (not more than 1 year)
        $interval = $startDate->diff($endDate);
        if ($interval->days > 365) {
            $this->errors[$endField] = 'Date range cannot exceed 1 year.';
            return false;
        }

        return true;
    }

    /**
     * Validate medical data
     */
    public function validateMedicalData(array $fields): bool
    {
        $rules = [];
        
        foreach ($fields as $field => $type) {
            if (isset($this->rules['medical'][$type])) {
                $rules[$field] = $this->rules['medical'][$type];
            }
        }

        return $this->validate($rules);
    }

    /**
     * Check for XSS attempts
     */
    public function checkXSS(string $input): bool
    {
        $xssPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi',
            '/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/mi',
            '/<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/mi',
            '/javascript:/i',
            '/on\w+\s*=/i'
        ];

        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for SQL injection attempts
     */
    public function checkSQLInjection(string $input): bool
    {
        $sqlPatterns = [
            '/\b(union|select|insert|update|delete|drop|create|alter|exec|execute)\b/i',
            '/\'\s*(or|and)\s*\'.*\'/i',
            '/\".*\s*(or|and)\s*\".*\"/i',
            '/\-\-/',
            '/\/\*/',
            '/\*\/',
            '/;\s*$/
        ];

        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get sanitized data
     */
    public function getSanitized(): array
    {
        return $this->sanitized;
    }

    /**
     * Get validation rule by name
     */
    protected function getRule(string $ruleName): string
    {
        // Check common rules
        if (isset($this->rules['common'][$ruleName])) {
            return $this->rules['common'][$ruleName];
        }

        // Check medical rules
        if (isset($this->rules['medical'][$ruleName])) {
            return $this->rules['medical'][$ruleName];
        }

        // Check hospital rules
        if (isset($this->rules['hospital'][$ruleName])) {
            return $this->rules['hospital'][$ruleName];
        }

        return $ruleName;
    }

    /**
     * Get default sanitizer for field
     */
    protected function getDefaultSanitizer(string $field): string
    {
        $fieldLower = strtolower($field);
        
        if (strpos($fieldLower, 'email') !== false) {
            return 'email';
        }
        
        if (strpos($fieldLower, 'phone') !== false) {
            return 'phone';
        }
        
        if (strpos($fieldLower, 'amount') !== false || strpos($fieldLower, 'price') !== false) {
            return 'numeric';
        }
        
        if (strpos($fieldLower, 'id') !== false && strpos($fieldLower, 'user_id') === false) {
            return 'integer';
        }
        
        if (strpos($fieldLower, 'url') !== false) {
            return 'url';
        }
        
        return 'string';
    }

    /**
     * Apply sanitizer to value
     */
    protected function applySanitizer(string $value, string $sanitizer): string
    {
        $sanitizers = explode('|', $sanitizer);
        
        foreach ($sanitizers as $method) {
            $value = $this->applySanitizationMethod($value, $method);
        }
        
        return $value;
    }

    /**
     * Apply individual sanitization method
     */
    protected function applySanitizationMethod(string $value, string $method): string
    {
        switch ($method) {
            case 'trim':
                return trim($value);
            case 'strtolower':
                return strtolower($value);
            case 'strtoupper':
                return strtoupper($value);
            case 'strip_tags':
                return strip_tags($value);
            case 'htmlspecialchars':
                return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            case 'esc_url':
                return esc_url($value);
            case 'esc_html':
                return esc_html($value);
            default:
                if (strpos($method, 'regex_replace') === 0) {
                    preg_match('/regex_replace\[\/(.*)\/(.*)\]/', $method, $matches);
                    if (isset($matches[1], $matches[2])) {
                        return preg_replace('/' . $matches[1] . '/' . $matches[2], '', $value);
                    }
                }
                return $value;
        }
    }

    /**
     * Generate validation summary
     */
    public function getValidationSummary(): array
    {
        return [
            'has_errors' => !empty($this->errors),
            'error_count' => count($this->errors),
            'errors' => $this->errors,
            'sanitized_data' => $this->sanitized,
            'validation_rules' => $this->rules
        ];
    }

    /**
     * Reset validator state
     */
    public function reset(): void
    {
        $this->errors = [];
        $this->sanitized = [];
        $this->validator->reset();
    }
}
