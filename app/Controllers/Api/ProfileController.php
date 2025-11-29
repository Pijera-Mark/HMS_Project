<?php

namespace App\Controllers\Api;

use App\Controllers\EnhancedBaseController;
use App\Services\ProfileService;
use App\Libraries\JWTLibrary;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Profile Management Controller (API)
 * Uses shared ProfileService to reduce redundancy
 */
class ProfileController extends EnhancedBaseController
{
    protected ProfileService $profileService;
    protected JWTLibrary $jwtLibrary;

    public function __construct()
    {
        $this->profileService = new ProfileService();
        $this->jwtLibrary = new JWTLibrary();
    }

    /**
     * Get current user profile
     */
    public function index()
    {
        // Require authentication
        if (!$this->requireAuth()) return;
        
        $userId = $this->currentUser['user_id'];
        
        $profile = $this->profileService->getProfile($userId);
        
        if (!$profile) {
            return $this->sendNotFound('User not found');
        }

        return $this->sendSuccess($profile);
    }

    /**
     * Update basic profile information
     */
    public function update()
    {
        // Require authentication
        if (!$this->requireAuth()) return;
        
        $userId = $this->currentUser['user_id'];
        $data = $this->sanitizeInput($this->getJsonData());
        
        $result = $this->profileService->updateBasicInfo($userId, $data);
        
        if (!$result['success']) {
            return $this->sendValidationError($result['errors']);
        }

        // Clear cache
        $this->clearCache('user_' . $userId);
        
        // Log activity
        $this->logActivity('profile_updated', [
            'entity_type' => 'user',
            'entity_id' => $userId,
            'details' => 'Profile information updated'
        ]);
        
        return $this->sendSuccess($result['data'], 'Profile updated successfully');
    }

    /**
     * Upload profile picture
     */
    public function uploadPicture()
    {
        // Require authentication
        if (!$this->requireAuth()) return;
        
        $userId = $this->currentUser['user_id'];
        $file = $this->request->getFile('profile_picture');
        
        $result = $this->profileService->uploadProfilePicture($userId, $file);
        
        if (!$result['success']) {
            return $this->sendValidationError($result['errors']);
        }

        // Log activity
        $this->logActivity('profile_picture_updated', [
            'entity_type' => 'user',
            'entity_id' => $userId,
            'details' => 'Profile picture uploaded'
        ]);
        
        return $this->sendSuccess($result['data'], 'Profile picture uploaded successfully');
    }

    /**
     * Update security settings
     */
    public function updateSecurity()
    {
        // Require authentication
        if (!$this->requireAuth()) return;
        
        $userId = $this->currentUser['user_id'];
        $data = $this->getJsonData();
        
        if (isset($data['change_password'])) {
            return $this->changePassword();
        }
        
        if (isset($data['two_factor_auth'])) {
            return $this->updateTwoFactorAuth();
        }
        
        if (isset($data['notification_preferences'])) {
            return $this->updateNotificationPreferences();
        }
        
        return $this->sendValidationError(['action' => 'No valid security action specified']);
    }

    /**
     * Change password
     */
    private function changePassword()
    {
        $data = $this->getJsonData();
        $userId = $this->currentUser['user_id'];
        
        $result = $this->profileService->changePassword(
            $userId,
            $data['current_password'],
            $data['new_password'],
            $data['confirm_password']
        );
        
        if (!$result['success']) {
            return $this->sendValidationError($result['errors']);
        }

        // Revoke all user tokens (force re-login)
        $this->jwtLibrary->revokeAllUserTokens($userId);
        
        // Log activity
        $this->logActivity('password_changed', [
            'entity_type' => 'user',
            'entity_id' => $userId,
            'details' => 'Password changed successfully'
        ]);
        
        return $this->sendSuccess(null, 'Password changed successfully. Please login again.');
    }

    /**
     * Update two-factor authentication
     */
    private function updateTwoFactorAuth()
    {
        $data = $this->getJsonData();
        $userId = $this->currentUser['user_id'];
        
        $enabled = $data['two_factor_auth']['enabled'] ?? false;
        
        // For now, just store the preference
        // In a real implementation, you'd integrate with 2FA service
        $this->profileService->updateTwoFactorAuth($userId, $enabled);
        
        // Log activity
        $this->logActivity('two_factor_auth_updated', [
            'entity_type' => 'user',
            'entity_id' => $userId,
            'details' => 'Two-factor authentication ' . ($enabled ? 'enabled' : 'disabled')
        ]);
        
        return $this->sendSuccess(null, 'Two-factor authentication settings updated');
    }

    /**
     * Update notification preferences
     */
    private function updateNotificationPreferences()
    {
        $data = $this->getJsonData();
        $userId = $this->currentUser['user_id'];
        
        $preferences = $data['notification_preferences'];
        
        $result = $this->profileService->updateNotificationPreferences($userId, $preferences);
        
        if (!$result['success']) {
            return $this->sendValidationError($result['errors']);
        }

        // Log activity
        $this->logActivity('notification_preferences_updated', [
            'entity_type' => 'user',
            'entity_id' => $userId,
            'details' => 'Notification preferences updated'
        ]);
        
        return $this->sendSuccess(null, 'Notification preferences updated');
    }

    /**
     * Get profile statistics
     */
    public function statistics()
    {
        // Require authentication
        if (!$this->requireAuth()) return;
        
        $userId = $this->currentUser['user_id'];
        
        $stats = $this->profileService->getStatistics($userId);
        
        return $this->sendSuccess($stats);
    }

    /**
     * Delete account (self-deletion)
     */
    public function deleteAccount()
    {
        // Require authentication
        if (!$this->requireAuth()) return;
        
        $userId = $this->currentUser['user_id'];
        
        // Validate password confirmation
        $data = $this->getJsonData();
        
        if (!isset($data['password']) || empty($data['password'])) {
            return $this->sendValidationError(['password' => 'Password confirmation required']);
        }
        
        $result = $this->profileService->deleteAccount($userId, $data['password']);
        
        if (!$result['success']) {
            return $this->sendValidationError($result['errors']);
        }

        // Log the deletion
        $this->logActivity('account_deleted_by_user', [
            'entity_type' => 'user',
            'entity_id' => $userId,
            'details' => 'User deleted their own account'
        ]);
        
        return $this->sendSuccess(null, 'Account deleted successfully');
    }
}
