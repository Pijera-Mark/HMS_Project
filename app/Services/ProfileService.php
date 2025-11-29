<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\UserProfileModel;
use App\Libraries\SecurityLibrary;
use App\Services\ValidationService;

/**
 * Profile Service - Shared business logic for profile management
 * Eliminates redundancy between API and Web controllers
 */
class ProfileService
{
    protected UserModel $userModel;
    protected UserProfileModel $profileModel;
    protected SecurityLibrary $security;
    protected ValidationService $validationService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->profileModel = new UserProfileModel();
        $this->security = new SecurityLibrary(service('request'));
        $this->validationService = new ValidationService();
    }

    /**
     * Get user profile by ID
     */
    public function getProfile(int $userId): ?array
    {
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return null;
        }

        // Remove sensitive data
        unset($user['password'], $user['reset_token'], $user['reset_expires']);

        // Get profile data
        $profileData = $this->profileModel->getProfileByUserId($userId);
        
        return array_merge($user, $profileData ?? []);
    }

    /**
     * Update basic user information
     */
    public function updateBasicInfo(int $userId, array $data): array
    {
        // Validate and sanitize data
        $result = $this->validationService->validateAndSanitize($data, 'profile');
        
        if (!$result['success']) {
            return ['success' => false, 'errors' => $result['errors']];
        }

        $sanitizedData = $result['data'];

        // Update user basic info
        $userData = [
            'name' => $sanitizedData['name'],
            'phone' => $sanitizedData['phone'] ?? null,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (!$this->userModel->update($userId, $userData)) {
            return ['success' => false, 'errors' => ['Failed to update profile']];
        }

        // Update profile data
        $profileData = $this->extractProfileData($sanitizedData);
        $this->profileModel->updateProfile($userId, $profileData);

        return ['success' => true, 'data' => $this->getProfile($userId)];
    }

    /**
     * Upload profile picture
     */
    public function uploadProfilePicture(int $userId, $file): array
    {
        // Validate file
        $errors = $this->validationService->validateFileUpload($file);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Generate unique filename
        $newName = 'profile_' . $userId . '_' . time() . '.' . $file->getExtension();

        // Create uploads directory if it doesn't exist
        $uploadPath = WRITEPATH . 'uploads/profiles/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Move file
        if (!$file->move($uploadPath, $newName)) {
            return ['success' => false, 'errors' => ['Failed to upload profile picture']];
        }

        // Delete old profile picture if exists
        $this->deleteOldProfilePicture($userId);

        // Update profile picture path
        $this->profileModel->updateProfilePicture($userId, 'uploads/profiles/' . $newName);

        return [
            'success' => true,
            'data' => ['profile_picture' => base_url() . 'uploads/profiles/' . $newName]
        ];
    }

    /**
     * Change password
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword, string $confirmPassword): array
    {
        // Validate password change
        $errors = $this->validationService->validatePasswordChange([
            'current_password' => $currentPassword,
            'new_password' => $newPassword,
            'confirm_password' => $confirmPassword
        ]);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Get user
        $user = $this->userModel->find($userId);

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'errors' => ['current_password' => 'Current password is incorrect']];
        }

        // Update password
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($this->userModel->update($userId, ['password' => $newPasswordHash])) {
            return ['success' => true, 'message' => 'Password changed successfully'];
        }

        return ['success' => false, 'errors' => ['Failed to change password']];
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(int $userId, array $preferences): array
    {
        // Validate preferences
        $errors = $this->validationService->validateNotificationPreferences($preferences);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $this->profileModel->updateNotificationPreferences($userId, $preferences);

        return ['success' => true, 'message' => 'Notification preferences updated'];
    }

    /**
     * Get profile statistics
     */
    public function getStatistics(int $userId): array
    {
        $user = $this->userModel->find($userId);
        
        $stats = [
            'profile_completion' => $this->profileModel->getProfileCompletion($userId),
            'login_count' => $this->getLoginCount($userId),
            'last_login' => $this->getLastLogin($userId),
            'account_created' => $user['created_at']
        ];

        // Role-specific statistics
        switch ($user['role']) {
            case 'doctor':
                $stats['appointments_today'] = $this->getDoctorAppointmentsToday($userId);
                $stats['total_patients'] = $this->getDoctorPatientCount($userId);
                break;

            case 'patient':
                $stats['upcoming_appointments'] = $this->getPatientUpcomingAppointments($userId);
                $stats['medical_records'] = $this->getPatientMedicalRecordCount($userId);
                break;

            case 'receptionist':
                $stats['appointments_today'] = $this->getTodayAppointmentCount();
                $stats['pending_registrations'] = $this->getPendingRegistrations();
                break;
        }

        return $stats;
    }

    /**
     * Update two-factor authentication
     */
    public function updateTwoFactorAuth(int $userId, bool $enabled): array
    {
        // For now, just store the preference
        // In a real implementation, you'd integrate with 2FA service
        $this->profileModel->updateProfile($userId, [
            'two_factor_auth_enabled' => $enabled,
            'two_factor_secret' => $enabled ? $this->security->generateSecureToken(32) : null
        ]);

        return ['success' => true, 'message' => 'Two-factor authentication settings updated'];
    }

    /**
     * Get profile completion percentage
     */
    public function getProfileCompletion(int $userId): int
    {
        return $this->profileModel->getProfileCompletion($userId);
    }

    /**
     * Delete user account
     */
    public function deleteAccount(int $userId, string $password): array
    {
        // Validate password
        $user = $this->userModel->find($userId);
        
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'errors' => ['password' => 'Incorrect password']];
        }

        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Delete profile picture
            $this->deleteOldProfilePicture($userId);

            // Delete profile data
            $db->table('user_profiles')->where('user_id', $userId)->delete();

            // Delete user
            $this->userModel->delete($userId);

            // Complete transaction
            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return ['success' => true, 'message' => 'Account deleted successfully'];

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Account deletion failed: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['Failed to delete account']];
        }
    }

    /**
     * Helper methods
     */
    private function extractProfileData(array $data): array
    {
        $profileFields = [
            'address', 'city', 'state', 'country', 'postal_code',
            'date_of_birth', 'gender', 'blood_group',
            'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation'
        ];

        $profileData = [];
        foreach ($profileFields as $field) {
            if (isset($data[$field])) {
                $profileData[$field] = $data[$field] ?? null;
            }
        }

        return $profileData;
    }

    private function deleteOldProfilePicture(int $userId): void
    {
        $profile = $this->profileModel->getProfileByUserId($userId);

        if (isset($profile['profile_picture']) && !empty($profile['profile_picture'])) {
            $filePath = WRITEPATH . $profile['profile_picture'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    private function getLoginCount(int $userId): int
    {
        return \Config\Database::connect()->table('activity_logs')
            ->where('user_id', $userId)
            ->where('action', 'user_login')
            ->countAllResults();
    }

    private function getLastLogin(int $userId): ?string
    {
        return \Config\Database::connect()->table('activity_logs')
            ->where('user_id', $userId)
            ->where('action', 'user_login')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRowArray()['created_at'] ?? null;
    }

    private function getDoctorAppointmentsToday(int $doctorId): int
    {
        return \Config\Database::connect()->table('appointments')
            ->where('doctor_id', $doctorId)
            ->where('DATE(scheduled_at)', date('Y-m-d'))
            ->countAllResults();
    }

    private function getDoctorPatientCount(int $doctorId): int
    {
        return \Config\Database::connect()->table('appointments')
            ->where('doctor_id', $doctorId)
            ->groupBy('patient_id')
            ->countAllResults();
    }

    private function getPatientUpcomingAppointments(int $patientId): int
    {
        return \Config\Database::connect()->table('appointments')
            ->where('patient_id', $patientId)
            ->where('scheduled_at >=', date('Y-m-d H:i:s'))
            ->countAllResults();
    }

    private function getPatientMedicalRecordCount(int $patientId): int
    {
        return \Config\Database::connect()->table('medical_records')
            ->where('patient_id', $patientId)
            ->countAllResults();
    }

    private function getTodayAppointmentCount(): int
    {
        return \Config\Database::connect()->table('appointments')
            ->where('DATE(scheduled_at)', date('Y-m-d'))
            ->countAllResults();
    }

    private function getPendingRegistrations(): int
    {
        return \Config\Database::connect()->table('patients')
            ->where('status', 'pending')
            ->countAllResults();
    }
}
