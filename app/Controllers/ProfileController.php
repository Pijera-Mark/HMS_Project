<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\ProfileService;

/**
 * Profile Management Controller (Web Interface)
 * Uses shared ProfileService to reduce redundancy
 */
class ProfileController extends BaseController
{
    protected ProfileService $profileService;

    public function __construct()
    {
        $this->profileService = new ProfileService();
    }

    /**
     * Display user profile page
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Please login to access your profile');
        }

        $userId = session()->get('user_id');
        
        $profile = $this->profileService->getProfile($userId);
        
        if (!$profile) {
            return redirect()->to('/login')->with('error', 'User not found');
        }

        $profileCompletion = $this->profileService->getProfileCompletion($userId);
        $notificationPreferences = $this->getDefaultNotificationPreferences();

        $data = [
            'title' => 'My Profile',
            'user' => $profile,
            'profile' => $profile,
            'profile_completion' => $profileCompletion,
            'notification_preferences' => $notificationPreferences
        ];

        return view('profile/index', $data);
    }

    /**
     * Display edit profile form
     */
    public function edit()
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Please login to access your profile');
        }

        $userId = session()->get('user_id');
        $profile = $this->profileService->getProfile($userId);

        $data = [
            'title' => 'Edit Profile',
            'user' => $profile,
            'profile' => $profile,
            'genders' => ['male', 'female', 'other'],
            'blood_groups' => ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']
        ];

        return view('profile/edit', $data);
    }

    /**
     * Update profile information
     */
    public function update()
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Please login to access your profile');
        }

        $userId = session()->get('user_id');
        $data = $this->request->getPost();

        $result = $this->profileService->updateBasicInfo($userId, $data);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }

        return redirect()->to('/profile')->with('success', 'Profile updated successfully');
    }

    /**
     * Display security settings page
     */
    public function security()
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Please login to access your profile');
        }

        $userId = session()->get('user_id');
        $profile = $this->profileService->getProfile($userId);

        $data = [
            'title' => 'Security Settings',
            'profile' => $profile,
            'notification_preferences' => $profile['notification_preferences'] ?? $this->getDefaultNotificationPreferences()
        ];

        return view('profile/security', $data);
    }

    /**
     * Update password
     */
    public function updatePassword()
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Please login to access your profile');
        }

        $userId = session()->get('user_id');
        $data = $this->request->getPost();

        $result = $this->profileService->changePassword(
            $userId,
            $data['current_password'],
            $data['new_password'],
            $data['confirm_password']
        );

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }

        // Log activity
        $this->logActivity('password_changed', [
            'entity_type' => 'user',
            'entity_id' => $userId,
            'details' => 'Password changed successfully'
        ]);

        return redirect()->to('/profile/security')->with('success', 'Password changed successfully');
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications()
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Please login to access your profile');
        }

        $userId = session()->get('user_id');
        $preferences = $this->request->getPost('preferences');

        if (!is_array($preferences)) {
            return redirect()->back()->with('error', 'Invalid notification preferences');
        }

        $result = $this->profileService->updateNotificationPreferences($userId, $preferences);

        if (!$result['success']) {
            return redirect()->back()->with('errors', $result['errors']);
        }

        return redirect()->to('/profile/security')->with('success', 'Notification preferences updated');
    }

    /**
     * Upload profile picture
     */
    public function uploadPicture()
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Please login to access your profile');
        }

        $userId = session()->get('user_id');
        $file = $this->request->getFile('profile_picture');

        $result = $this->profileService->uploadProfilePicture($userId, $file);

        if (!$result['success']) {
            return redirect()->back()->with('errors', $result['errors']);
        }

        return redirect()->to('/profile')->with('success', 'Profile picture uploaded successfully');
    }

    /**
     * Display profile statistics
     */
    public function statistics()
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Please login to access your profile');
        }

        $userId = session()->get('user_id');
        $profile = $this->profileService->getProfile($userId);
        $stats = $this->profileService->getStatistics($userId);

        $data = [
            'title' => 'Profile Statistics',
            'user' => $profile,
            'profile' => $profile,
            'stats' => $stats
        ];

        return view('profile/statistics', $data);
    }

    /**
     * Helper methods
     */
    private function getDefaultNotificationPreferences(): array
    {
        return [
            'email_notifications' => true,
            'sms_notifications' => false,
            'appointment_reminders' => true,
            'medication_reminders' => true,
            'test_results' => true,
            'billing_alerts' => true,
            'system_updates' => false
        ];
    }
}
