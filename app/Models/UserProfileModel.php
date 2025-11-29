<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * User Profile Model
 * - Extended user profile information
 * - Profile picture management
 * - Security settings
 * - Notification preferences
 */
class UserProfileModel extends Model
{
    protected $table = 'user_profiles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'user_id',
        'profile_picture',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'date_of_birth',
        'gender',
        'blood_group',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'two_factor_auth_enabled',
        'two_factor_secret',
        'notification_preferences',
        'updated_at'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';

    /**
     * Get user profile by user ID
     */
    public function getProfileByUserId(int $userId): ?array
    {
        $profile = $this->where('user_id', $userId)->first();
        
        if ($profile && isset($profile['notification_preferences'])) {
            $profile['notification_preferences'] = json_decode($profile['notification_preferences'], true);
        }
        
        return $profile;
    }

    /**
     * Create or update user profile
     */
    public function updateProfile(int $userId, array $data): bool
    {
        $existing = $this->where('user_id', $userId)->first();
        
        if ($existing) {
            return $this->update($existing['id'], array_merge($data, [
                'updated_at' => date('Y-m-d H:i:s')
            ]));
        } else {
            return $this->insert(array_merge($data, [
                'user_id' => $userId,
                'created_at' => date('Y-m-d H:i:s')
            ])) !== false;
        }
    }

    /**
     * Update profile picture
     */
    public function updateProfilePicture(int $userId, string $picturePath): bool
    {
        return $this->updateProfile($userId, ['profile_picture' => $picturePath]);
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(int $userId, array $preferences): bool
    {
        return $this->updateProfile($userId, [
            'notification_preferences' => json_encode($preferences)
        ]);
    }

    /**
     * Update two-factor authentication settings
     */
    public function updateTwoFactorAuth(int $userId, bool $enabled, ?string $secret = null): bool
    {
        return $this->updateProfile($userId, [
            'two_factor_auth_enabled' => $enabled,
            'two_factor_secret' => $enabled ? $secret : null
        ]);
    }

    /**
     * Get profile completion percentage
     */
    public function getProfileCompletion(int $userId): int
    {
        $profile = $this->getProfileByUserId($userId);
        
        if (!$profile) {
            return 0;
        }
        
        $fields = [
            'address',
            'city',
            'state',
            'country',
            'postal_code',
            'date_of_birth',
            'gender',
            'blood_group',
            'emergency_contact_name',
            'emergency_contact_phone',
            'emergency_contact_relation'
        ];
        
        $filledFields = 0;
        foreach ($fields as $field) {
            if (!empty($profile[$field])) {
                $filledFields++;
            }
        }
        
        return round(($filledFields / count($fields)) * 100);
    }

    /**
     * Get users with incomplete profiles
     */
    public function getUsersWithIncompleteProfiles(int $threshold = 50): array
    {
        $users = $this->select('user_id')
            ->findAll();
            
        $incompleteUsers = [];
        foreach ($users as $user) {
            $completion = $this->getProfileCompletion($user['user_id']);
            if ($completion < $threshold) {
                $incompleteUsers[] = [
                    'user_id' => $user['user_id'],
                    'completion_percentage' => $completion
                ];
            }
        }
        
        return $incompleteUsers;
    }

    /**
     * Search profiles by criteria
     */
    public function searchProfiles(array $criteria): array
    {
        $query = $this->select('user_profiles.*, users.name, users.email, users.role')
            ->join('users', 'users.id = user_profiles.user_id');
            
        foreach ($criteria as $field => $value) {
            if (!empty($value)) {
                switch ($field) {
                    case 'name':
                        $query->like('users.name', $value);
                        break;
                    case 'email':
                        $query->like('users.email', $value);
                        break;
                    case 'city':
                        $query->like('user_profiles.city', $value);
                        break;
                    case 'blood_group':
                        $query->where('user_profiles.blood_group', $value);
                        break;
                    case 'gender':
                        $query->where('user_profiles.gender', $value);
                        break;
                }
            }
        }
        
        return $query->findAll();
    }

    /**
     * Get profile statistics
     */
    public function getProfileStatistics(): array
    {
        $totalProfiles = $this->countAll();
        
        $stats = [
            'total_profiles' => $totalProfiles,
            'profiles_with_pictures' => $this->where('profile_picture IS NOT NULL')->countAllResults(),
            'profiles_with_emergency_contacts' => $this->where('emergency_contact_name IS NOT NULL')->countAllResults(),
            'two_factor_enabled' => $this->where('two_factor_auth_enabled', true)->countAllResults(),
            'gender_distribution' => $this->getGenderDistribution(),
            'blood_group_distribution' => $this->getBloodGroupDistribution()
        ];
        
        return $stats;
    }

    /**
     * Get gender distribution
     */
    private function getGenderDistribution(): array
    {
        $distribution = $this->select('gender, COUNT(*) as count')
            ->where('gender IS NOT NULL')
            ->groupBy('gender')
            ->findAll();
            
        $result = ['male' => 0, 'female' => 0, 'other' => 0];
        foreach ($distribution as $item) {
            $result[$item['gender']] = $item['count'];
        }
        
        return $result;
    }

    /**
     * Get blood group distribution
     */
    private function getBloodGroupDistribution(): array
    {
        $distribution = $this->select('blood_group, COUNT(*) as count')
            ->where('blood_group IS NOT NULL')
            ->groupBy('blood_group')
            ->findAll();
            
        $result = [];
        foreach ($distribution as $item) {
            $result[$item['blood_group']] = $item['count'];
        }
        
        return $result;
    }

    /**
     * Delete user profile
     */
    public function deleteProfile(int $userId): bool
    {
        return $this->where('user_id', $userId)->delete() !== false;
    }

    /**
     * Get profile picture URL
     */
    public function getProfilePictureUrl(int $userId): ?string
    {
        $profile = $this->getProfileByUserId($userId);
        
        if (!$profile || empty($profile['profile_picture'])) {
            return null;
        }
        
        return base_url() . $profile['profile_picture'];
    }

    /**
     * Validate profile data
     */
    public function validateProfileData(array $data): array
    {
        $errors = [];
        
        // Validate date of birth
        if (isset($data['date_of_birth'])) {
            $dob = new \DateTime($data['date_of_birth']);
            $today = new \DateTime();
            $age = $today->diff($dob)->y;
            
            if ($age < 0 || $age > 120) {
                $errors['date_of_birth'] = 'Invalid date of birth';
            }
        }
        
        // Validate phone numbers
        if (isset($data['emergency_contact_phone'])) {
            if (!preg_match('/^[0-9+\-\s()]+$/', $data['emergency_contact_phone'])) {
                $errors['emergency_contact_phone'] = 'Invalid phone number format';
            }
        }
        
        // Validate postal code
        if (isset($data['postal_code'])) {
            if (!preg_match('/^[A-Za-z0-9\s\-]+$/', $data['postal_code'])) {
                $errors['postal_code'] = 'Invalid postal code format';
            }
        }
        
        return $errors;
    }
}
