<?php

namespace App\Services;

use App\Libraries\SecurityLibrary;

/**
 * Validation Service - Centralized validation logic
 * Eliminates redundancy across controllers
 */
class ValidationService
{
    protected SecurityLibrary $security;

    public function __construct()
    {
        $this->security = new SecurityLibrary(service('request'));
    }

    /**
     * Validate user profile data
     */
    public function validateProfileData(array $data): array
    {
        $errors = [];

        // Required fields
        if (empty($data['name']) || strlen($data['name']) < 2) {
            $errors['name'] = 'Name is required and must be at least 2 characters';
        }

        // Email validation
        if (isset($data['email']) && !empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Invalid email format';
            }
        }

        // Phone validation
        if (isset($data['phone']) && !empty($data['phone'])) {
            if (!preg_match('/^[0-9+\-\s()]+$/', $data['phone'])) {
                $errors['phone'] = 'Invalid phone number format';
            }
        }

        // Date of birth validation
        if (isset($data['date_of_birth']) && !empty($data['date_of_birth'])) {
            if (!$this->isValidDate($data['date_of_birth'])) {
                $errors['date_of_birth'] = 'Invalid date format';
            } else {
                $dob = new \DateTime($data['date_of_birth']);
                $today = new \DateTime();
                $age = $today->diff($dob)->y;

                if ($age < 0 || $age > 120) {
                    $errors['date_of_birth'] = 'Invalid date of birth';
                }
            }
        }

        // Gender validation
        if (isset($data['gender']) && !empty($data['gender'])) {
            if (!in_array($data['gender'], ['male', 'female', 'other'])) {
                $errors['gender'] = 'Invalid gender value';
            }
        }

        // Blood group validation
        if (isset($data['blood_group']) && !empty($data['blood_group'])) {
            $validGroups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
            if (!in_array($data['blood_group'], $validGroups)) {
                $errors['blood_group'] = 'Invalid blood group';
            }
        }

        // Emergency contact phone validation
        if (isset($data['emergency_contact_phone']) && !empty($data['emergency_contact_phone'])) {
            if (!preg_match('/^[0-9+\-\s()]+$/', $data['emergency_contact_phone'])) {
                $errors['emergency_contact_phone'] = 'Invalid emergency contact phone format';
            }
        }

        return $errors;
    }

    /**
     * Validate password change
     */
    public function validatePasswordChange(array $data): array
    {
        $errors = [];

        // Required fields
        if (empty($data['current_password'])) {
            $errors['current_password'] = 'Current password is required';
        }

        if (empty($data['new_password'])) {
            $errors['new_password'] = 'New password is required';
        }

        if (empty($data['confirm_password'])) {
            $errors['confirm_password'] = 'Confirm password is required';
        }

        // Password match
        if (isset($data['new_password']) && isset($data['confirm_password'])) {
            if ($data['new_password'] !== $data['confirm_password']) {
                $errors['confirm_password'] = 'Passwords do not match';
            }
        }

        // Password strength
        if (isset($data['new_password']) && !empty($data['new_password'])) {
            $passwordErrors = $this->security->validatePasswordStrength($data['new_password']);
            if (!empty($passwordErrors)) {
                $errors = array_merge($errors, $passwordErrors);
            }
        }

        return $errors;
    }

    /**
     * Validate notification preferences
     */
    public function validateNotificationPreferences(array $preferences): array
    {
        $errors = [];
        $allowedPreferences = [
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'appointment_reminders' => 'boolean',
            'medication_reminders' => 'boolean',
            'test_results' => 'boolean',
            'billing_alerts' => 'boolean',
            'system_updates' => 'boolean'
        ];

        foreach ($preferences as $key => $value) {
            if (!isset($allowedPreferences[$key])) {
                $errors[$key] = "Invalid preference: {$key}";
            }

            if ($allowedPreferences[$key] === 'boolean' && !is_bool($value)) {
                $errors[$key] = "Preference {$key} must be boolean";
            }
        }

        return $errors;
    }

    /**
     * Validate file upload
     */
    public function validateFileUpload($file): array
    {
        $errors = [];

        if (!$file || !$file->isValid()) {
            $errors['file'] = 'No file uploaded or invalid file';
            return $errors;
        }

        // File size validation (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file->getSize() > $maxSize) {
            $errors['file'] = 'File size must be less than 5MB';
        }

        // File type validation
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            $errors['file'] = 'Only JPEG, PNG, GIF, and WebP images are allowed';
        }

        // Extension validation
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array(strtolower($file->getExtension()), $allowedExtensions)) {
            $errors['file'] = 'Invalid file extension';
        }

        return $errors;
    }

    /**
     * Validate user registration data
     */
    public function validateUserRegistration(array $data): array
    {
        $errors = [];

        // Name validation
        if (empty($data['name']) || strlen($data['name']) < 2) {
            $errors['name'] = 'Name is required and must be at least 2 characters';
        }

        // Email validation
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        // Password validation
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        } else {
            $passwordErrors = $this->security->validatePasswordStrength($data['password']);
            if (!empty($passwordErrors)) {
                $errors = array_merge($errors, $passwordErrors);
            }
        }

        // Role validation
        if (empty($data['role'])) {
            $errors['role'] = 'Role is required';
        } elseif (!in_array($data['role'], ['admin', 'doctor', 'patient', 'receptionist', 'it_staff'])) {
            $errors['role'] = 'Invalid role specified';
        }

        return $errors;
    }

    /**
     * Validate appointment data
     */
    public function validateAppointmentData(array $data): array
    {
        $errors = [];

        // Patient validation
        if (empty($data['patient_id'])) {
            $errors['patient_id'] = 'Patient is required';
        }

        // Doctor validation
        if (empty($data['doctor_id'])) {
            $errors['doctor_id'] = 'Doctor is required';
        }

        // Date and time validation
        if (empty($data['scheduled_at'])) {
            $errors['scheduled_at'] = 'Scheduled date and time is required';
        } elseif (!$this->isValidDateTime($data['scheduled_at'])) {
            $errors['scheduled_at'] = 'Invalid date and time format';
        } elseif (strtotime($data['scheduled_at']) < time()) {
            $errors['scheduled_at'] = 'Appointment cannot be scheduled in the past';
        }

        // Purpose validation
        if (empty($data['purpose'])) {
            $errors['purpose'] = 'Purpose is required';
        }

        return $errors;
    }

    /**
     * Validate ward data
     */
    public function validateWardData(array $data): array
    {
        $errors = [];

        // Required fields
        if (empty($data['name']) || strlen($data['name']) < 2) {
            $errors['name'] = 'Ward name is required and must be at least 2 characters';
        }

        if (empty($data['branch_id'])) {
            $errors['branch_id'] = 'Branch is required';
        } elseif (!is_numeric($data['branch_id'])) {
            $errors['branch_id'] = 'Invalid branch selected';
        }

        if (empty($data['capacity'])) {
            $errors['capacity'] = 'Capacity is required';
        } elseif (!is_numeric($data['capacity']) || $data['capacity'] < 1) {
            $errors['capacity'] = 'Capacity must be a positive number';
        }

        // Ward type validation
        if (empty($data['ward_type'])) {
            $errors['ward_type'] = 'Ward type is required';
        } elseif (!in_array($data['ward_type'], ['general', 'icu', 'emergency', 'maternity', 'pediatric', 'surgical'])) {
            $errors['ward_type'] = 'Invalid ward type';
        }

        return $errors;
    }

    /**
     * Validate prescription data
     */
    public function validatePrescriptionData(array $data): array
    {
        $errors = [];

        // Required fields
        if (empty($data['patient_id'])) {
            $errors['patient_id'] = 'Patient is required';
        }

        if (empty($data['doctor_id'])) {
            $errors['doctor_id'] = 'Doctor is required';
        }

        if (empty($data['medicines']) || !is_array($data['medicines'])) {
            $errors['medicines'] = 'At least one medicine is required';
        } else {
            foreach ($data['medicines'] as $index => $medicine) {
                if (empty($medicine['medicine_id'])) {
                    $errors["medicines_{$index}_medicine_id"] = 'Medicine is required';
                }
                if (empty($medicine['dosage'])) {
                    $errors["medicines_{$index}_dosage"] = 'Dosage is required';
                }
                if (empty($medicine['duration'])) {
                    $errors["medicines_{$index}_duration"] = 'Duration is required';
                }
            }
        }

        return $errors;
    }

    /**
     * Validate lab test data
     */
    public function validateLabTestData(array $data): array
    {
        $errors = [];

        // Required fields
        if (empty($data['patient_id'])) {
            $errors['patient_id'] = 'Patient is required';
        }

        if (empty($data['doctor_id'])) {
            $errors['doctor_id'] = 'Doctor is required';
        }

        if (empty($data['test_type'])) {
            $errors['test_type'] = 'Test type is required';
        }

        if (empty($data['test_name'])) {
            $errors['test_name'] = 'Test name is required';
        }

        return $errors;
    }

    /**
     * Validate doctor data
     */
    public function validateDoctorData(array $data): array
    {
        $errors = [];

        // Required fields
        if (empty($data['first_name']) || strlen($data['first_name']) < 2) {
            $errors['first_name'] = 'First name is required and must be at least 2 characters';
        }

        if (empty($data['last_name']) || strlen($data['last_name']) < 2) {
            $errors['last_name'] = 'Last name is required and must be at least 2 characters';
        }

        // Email validation
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        // Phone validation
        if (empty($data['phone'])) {
            $errors['phone'] = 'Phone number is required';
        } elseif (!preg_match('/^[0-9+\-\s()]+$/', $data['phone'])) {
            $errors['phone'] = 'Invalid phone number format';
        }

        // Specialization validation
        if (empty($data['specialization'])) {
            $errors['specialization'] = 'Specialization is required';
        }

        // Branch validation
        if (empty($data['branch_id'])) {
            $errors['branch_id'] = 'Branch is required';
        } elseif (!is_numeric($data['branch_id'])) {
            $errors['branch_id'] = 'Invalid branch selected';
        }

        // License number validation
        if (empty($data['license_number'])) {
            $errors['license_number'] = 'License number is required';
        }

        // Experience validation
        if (isset($data['experience_years']) && !empty($data['experience_years'])) {
            if (!is_numeric($data['experience_years']) || $data['experience_years'] < 0 || $data['experience_years'] > 50) {
                $errors['experience_years'] = 'Experience years must be between 0 and 50';
            }
        }

        // Consultation fee validation
        if (isset($data['consultation_fee']) && !empty($data['consultation_fee'])) {
            if (!is_numeric($data['consultation_fee']) || $data['consultation_fee'] < 0) {
                $errors['consultation_fee'] = 'Consultation fee must be a positive number';
            }
        }

        return $errors;
    }

    /**
     * Validate medical record data
     */
    public function validateMedicalRecordData(array $data): array
    {
        $errors = [];

        // Patient validation
        if (empty($data['patient_id'])) {
            $errors['patient_id'] = 'Patient is required';
        }

        // Doctor validation
        if (empty($data['doctor_id'])) {
            $errors['doctor_id'] = 'Doctor is required';
        }

        // Diagnosis validation
        if (empty($data['diagnosis'])) {
            $errors['diagnosis'] = 'Diagnosis is required';
        }

        // Treatment validation
        if (empty($data['treatment'])) {
            $errors['treatment'] = 'Treatment is required';
        }

        return $errors;
    }

    /**
     * Helper methods
     */
    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    private function isValidDateTime(string $dateTime): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
        return $d && $d->format('Y-m-d H:i:s') === $dateTime;
    }

    /**
     * Sanitize input data
     */
    public function sanitizeInput(array $data): array
    {
        return $this->security->sanitizeInput($data);
    }

    /**
     * Validate and sanitize in one step
     */
    public function validateAndSanitize(array $data, string $type = 'profile'): array
    {
        // Sanitize first
        $sanitized = $this->sanitizeInput($data);

        // Then validate
        $errors = [];
        switch ($type) {
            case 'profile':
                $errors = $this->validateProfileData($sanitized);
                break;
            case 'password':
                $errors = $this->validatePasswordChange($sanitized);
                break;
            case 'registration':
                $errors = $this->validateUserRegistration($sanitized);
                break;
            case 'appointment':
                $errors = $this->validateAppointmentData($sanitized);
                break;
            case 'medical_record':
                $errors = $this->validateMedicalRecordData($sanitized);
                break;
            case 'doctor':
                $errors = $this->validateDoctorData($sanitized);
                break;
            case 'ward':
                $errors = $this->validateWardData($sanitized);
                break;
            case 'prescription':
                $errors = $this->validatePrescriptionData($sanitized);
                break;
            case 'lab_test':
                $errors = $this->validateLabTestData($sanitized);
                break;
        }

        return [
            'success' => empty($errors),
            'errors' => $errors,
            'data' => $sanitized
        ];
    }
}
