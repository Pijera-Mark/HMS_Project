<?php

namespace App\Controllers\Api;

use App\Controllers\EnhancedBaseController;
use App\Models\UserModel;
use App\Models\SecurityLogModel;
use App\Libraries\SecurityLibrary;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Enhanced User Controller with comprehensive user management
 * - CRUD operations for users
 * - Role-based access control
 * - Account deletion with proper cleanup
 * - Security audit logging
 */
class UserController extends EnhancedBaseController
{
    protected UserModel $userModel;
    protected SecurityLogModel $securityLogModel;
    protected SecurityLibrary $security;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->securityLogModel = new SecurityLogModel();
        $this->security = new SecurityLibrary($this->request);
    }

    /**
     * Get all users with filtering and pagination
     */
    public function index()
    {
        // Require authentication and proper role
        if (!$this->requireRole(['admin'])) return;
        
        $search = $this->request->getGet('search');
        $role = $this->request->getGet('role');
        $status = $this->request->getGet('status');
        $branchId = $this->request->getGet('branch_id');
        
        $cacheKey = 'users_list_' . md5(json_encode([
            'search' => $search,
            'role' => $role,
            'status' => $status,
            'branch_id' => $branchId
        ]));
        
        // Try cache first
        $cached = $this->getCachedData($cacheKey);
        if ($cached !== null) {
            return $this->sendSuccess($cached, 'Users retrieved from cache');
        }
        
        $query = $this->userModel;
        
        // Apply filters
        if ($search) {
            $query = $query->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
                ->orLike('phone', $search)
                ->groupEnd();
        }
        
        if ($role) {
            $query = $query->where('role', $role);
        }
        
        if ($status) {
            $query = $query->where('status', $status);
        }
        
        if ($branchId) {
            $query = $query->where('branch_id', $branchId);
        }
        
        // Get paginated results
        $result = $this->paginate($query);
        
        // Cache for 5 minutes
        $this->cacheData($cacheKey, $result, 300);
        
        return $this->sendSuccess($result);
    }

    /**
     * Get single user
     */
    public function show($id = null)
    {
        // Require authentication
        if (!$this->requireAuth()) return;
        
        // Users can only view their own profile unless admin
        if (!$this->requireRole(['admin']) && $id != $this->currentUser['user_id']) {
            return $this->sendForbidden('You can only view your own profile');
        }
        
        if (!$id) {
            return $this->sendValidationError(['id' => 'User ID is required']);
        }
        
        $cacheKey = 'user_' . $id;
        
        // Try cache first
        $cached = $this->getCachedData($cacheKey);
        if ($cached !== null) {
            return $this->sendSuccess($cached, 'User retrieved from cache');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return $this->sendNotFound('User not found');
        }
        
        // Remove sensitive data for non-admins
        if ($this->currentUser['role'] !== 'admin') {
            unset($user['password'], $user['reset_token'], $user['reset_expires']);
        }
        
        // Cache for 10 minutes
        $this->cacheData($cacheKey, $user, 600);
        
        return $this->sendSuccess($user);
    }

    /**
     * Create new user
     */
    public function create()
    {
        // Require admin role
        if (!$this->requireRole(['admin'])) return;
        
        // Validate input
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'role' => 'required|in_list[admin,doctor,nurse,receptionist,pharmacist]',
            'phone' => 'permit_empty|min_length[10]|max_length[15]',
            'branch_id' => 'permit_empty|is_natural_no_zero',
            'status' => 'permit_empty|in_list[active,inactive]'
        ];
        
        if (!$this->validateRequest($rules)) return;
        
        $data = $this->sanitizeInput($this->getJsonData());
        
        // Validate password strength
        $passwordErrors = $this->security->validatePasswordStrength($data['password']);
        if (!empty($passwordErrors)) {
            return $this->sendValidationError($passwordErrors);
        }
        
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Set default status
        $data['status'] = $data['status'] ?? 'active';
        
        if ($this->userModel->insert($data)) {
            $userId = $this->userModel->getInsertID();
            $user = $this->userModel->find($userId);
            
            // Remove sensitive data
            unset($user['password']);
            
            // Clear relevant caches
            $this->clearCache('users_list_');
            
            // Log activity
            $this->logActivity('user_created', [
                'entity_type' => 'user',
                'entity_id' => $userId,
                'details' => 'New user created: ' . $data['name'] . ' (' . $data['role'] . ')'
            ]);
            
            return $this->sendSuccess($user, 'User created successfully', 201);
        }
        
        return $this->sendValidationError($this->userModel->errors());
    }

    /**
     * Update user
     */
    public function update($id = null)
    {
        // Require authentication
        if (!$this->requireAuth()) return;
        
        if (!$id) {
            return $this->sendValidationError(['id' => 'User ID is required']);
        }
        
        // Users can only update their own profile unless admin
        if (!$this->requireRole(['admin']) && $id != $this->currentUser['user_id']) {
            return $this->sendForbidden('You can only update your own profile');
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->sendNotFound('User not found');
        }
        
        // Different validation rules for admins vs self-update
        $rules = [
            'name' => 'if_exists|min_length[2]|max_length[100]',
            'email' => 'if_exists|valid_email|is_unique[users.email,id,' . $id . ']',
            'phone' => 'if_exists|min_length[10]|max_length[15]',
            'status' => 'if_exists|in_list[active,inactive]'
        ];
        
        // Only admins can update role and branch
        if ($this->currentUser['role'] === 'admin') {
            $rules['role'] = 'if_exists|in_list[admin,doctor,nurse,receptionist,pharmacist]';
            $rules['branch_id'] = 'if_exists|is_natural_no_zero';
        }
        
        // Password update rules
        $data = $this->getJsonData();
        if (isset($data['password']) && !empty($data['password'])) {
            $rules['password'] = 'min_length[8]';
            $rules['current_password'] = 'required';
            
            // Verify current password for self-update
            if ($this->currentUser['user_id'] == $id) {
                if (!password_verify($data['current_password'], $user['password'])) {
                    return $this->sendValidationError(['current_password' => 'Current password is incorrect']);
                }
            }
        }
        
        if (!$this->validateRequest($rules)) return;
        
        $updateData = $this->sanitizeInput($data);
        
        // Handle password update
        if (isset($updateData['password']) && !empty($updateData['password'])) {
            // Validate password strength
            $passwordErrors = $this->security->validatePasswordStrength($updateData['password']);
            if (!empty($passwordErrors)) {
                return $this->sendValidationError($passwordErrors);
            }
            
            $updateData['password'] = password_hash($updateData['password'], PASSWORD_DEFAULT);
        } else {
            unset($updateData['password']);
        }
        
        // Remove current password field
        unset($updateData['current_password']);
        
        if ($this->userModel->update($id, $updateData)) {
            $updatedUser = $this->userModel->find($id);
            
            // Remove sensitive data
            unset($updatedUser['password']);
            
            // Clear caches
            $this->clearCache('user_' . $id);
            $this->clearCache('users_list_');
            
            // Log activity
            $this->logActivity('user_updated', [
                'entity_type' => 'user',
                'entity_id' => $id,
                'details' => 'User updated: ' . $updatedUser['name']
            ]);
            
            return $this->sendSuccess($updatedUser, 'User updated successfully');
        }
        
        return $this->sendValidationError($this->userModel->errors());
    }

    /**
     * Delete user account (Admin only)
     * This permanently deletes the user and all associated data
     */
    public function delete($id = null)
    {
        // Require admin role
        if (!$this->requireRole(['admin'])) return;
        
        if (!$id) {
            return $this->sendValidationError(['id' => 'User ID is required']);
        }
        
        // Prevent self-deletion
        if ($id == $this->currentUser['user_id']) {
            return $this->sendForbidden('You cannot delete your own account');
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->sendNotFound('User not found');
        }
        
        // Start database transaction
        $this->db->transStart();
        
        try {
            // Log the deletion before it happens
            $this->logActivity('user_deleted', [
                'entity_type' => 'user',
                'entity_id' => $id,
                'details' => 'User account deleted: ' . $user['name'] . ' (' . $user['email'] . ')'
            ]);
            
            // Log security event
            $this->securityLogModel->logEvent(
                'account_deleted',
                $this->currentUser['user_id'],
                $this->request->getIPAddress(),
                $this->request->getUserAgent(),
                $this->request->getPath(),
                $this->request->getMethod(),
                [
                    'deleted_user_id' => $id,
                    'deleted_user_email' => $user['email'],
                    'deleted_user_role' => $user['role']
                ],
                'high'
            );
            
            // Delete user's activity logs
            $this->db->table('activity_logs')
                ->where('user_id', $id)
                ->delete();
            
            // Delete user's security logs
            $this->db->table('security_logs')
                ->where('user_id', $id)
                ->delete();
            
            // Handle user-specific data based on role
            $this->handleUserDataDeletion($id, $user['role']);
            
            // Finally delete the user
            $this->userModel->delete($id);
            
            // Complete transaction
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            // Clear caches
            $this->clearCache('user_' . $id);
            $this->clearCache('users_list_');
            
            return $this->sendSuccess(null, 'User account and all associated data deleted successfully');
            
        } catch (\Exception $e) {
            // Rollback transaction
            $this->db->transRollback();
            
            log_message('error', 'User deletion failed: ' . $e->getMessage());
            
            return $this->sendServerError('Failed to delete user account. Please try again.');
        }
    }

    /**
     * Handle role-specific data deletion
     */
    private function handleUserDataDeletion(int $userId, string $role): void
    {
        switch ($role) {
            case 'doctor':
                // Delete doctor-specific records
                $this->db->table('appointments')
                    ->where('doctor_id', $userId)
                    ->delete();
                    
                $this->db->table('prescriptions')
                    ->where('doctor_id', $userId)
                    ->delete();
                break;
                
            case 'patient':
                // Delete patient-specific records
                $this->db->table('medical_records')
                    ->where('patient_id', $userId)
                    ->delete();
                    
                $this->db->table('appointments')
                    ->where('patient_id', $userId)
                    ->delete();
                    
                $this->db->table('admissions')
                    ->where('patient_id', $userId)
                    ->delete();
                    
                $this->db->table('prescriptions')
                    ->where('patient_id', $userId)
                    ->delete();
                break;
                
            case 'receptionist':
                // Delete appointments created by this receptionist
                $this->db->table('appointments')
                    ->where('created_by', $userId)
                    ->update(['created_by' => null]);
                break;
                
            case 'pharmacist':
                // Delete prescriptions handled by this pharmacist
                $this->db->table('prescriptions')
                    ->where('pharmacist_id', $userId)
                    ->update(['pharmacist_id' => null]);
                break;
        }
        
        // Delete any invoices created by this user
        $this->db->table('invoices')
            ->where('created_by', $userId)
            ->update(['created_by' => null]);
    }

    /**
     * Soft delete user (deactivate account)
     * Alternative to permanent deletion
     */
    public function deactivate($id = null)
    {
        // Require admin role
        if (!$this->requireRole(['admin'])) return;
        
        if (!$id) {
            return $this->sendValidationError(['id' => 'User ID is required']);
        }
        
        // Prevent self-deactivation
        if ($id == $this->currentUser['user_id']) {
            return $this->sendForbidden('You cannot deactivate your own account');
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->sendNotFound('User not found');
        }
        
        if ($user['status'] === 'inactive') {
            return $this->sendError('User is already inactive');
        }
        
        if ($this->userModel->update($id, ['status' => 'inactive'])) {
            // Log activity
            $this->logActivity('user_deactivated', [
                'entity_type' => 'user',
                'entity_id' => $id,
                'details' => 'User deactivated: ' . $user['name']
            ]);
            
            // Log security event
            $this->securityLogModel->logEvent(
                'account_deactivated',
                $this->currentUser['user_id'],
                $this->request->getIPAddress(),
                $this->request->getUserAgent(),
                $this->request->getPath(),
                $this->request->getMethod(),
                [
                    'deactivated_user_id' => $id,
                    'deactivated_user_email' => $user['email']
                ],
                'medium'
            );
            
            // Clear caches
            $this->clearCache('user_' . $id);
            $this->clearCache('users_list_');
            
            return $this->sendSuccess(null, 'User account deactivated successfully');
        }
        
        return $this->sendServerError('Failed to deactivate user account');
    }

    /**
     * Reactivate user account
     */
    public function reactivate($id = null)
    {
        // Require admin role
        if (!$this->requireRole(['admin'])) return;
        
        if (!$id) {
            return $this->sendValidationError(['id' => 'User ID is required']);
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->sendNotFound('User not found');
        }
        
        if ($user['status'] === 'active') {
            return $this->sendError('User is already active');
        }
        
        if ($this->userModel->update($id, ['status' => 'active'])) {
            // Log activity
            $this->logActivity('user_reactivated', [
                'entity_type' => 'user',
                'entity_id' => $id,
                'details' => 'User reactivated: ' . $user['name']
            ]);
            
            // Clear caches
            $this->clearCache('user_' . $id);
            $this->clearCache('users_list_');
            
            return $this->sendSuccess(null, 'User account reactivated successfully');
        }
        
        return $this->sendServerError('Failed to reactivate user account');
    }

    /**
     * Get user statistics
     */
    public function statistics()
    {
        // Require admin role
        if (!$this->requireRole(['admin'])) return;
        
        $cacheKey = 'user_statistics';
        
        // Try cache first
        $cached = $this->getCachedData($cacheKey);
        if ($cached !== null) {
            return $this->sendSuccess($cached, 'User statistics retrieved from cache');
        }
        
        $stats = [
            'total_users' => $this->userModel->countAll(),
            'active_users' => $this->userModel->where('status', 'active')->countAllResults(),
            'inactive_users' => $this->userModel->where('status', 'inactive')->countAllResults(),
            'users_by_role' => [
                'admin' => $this->userModel->where('role', 'admin')->countAllResults(),
                'doctor' => $this->userModel->where('role', 'doctor')->countAllResults(),
                'nurse' => $this->userModel->where('role', 'nurse')->countAllResults(),
                'receptionist' => $this->userModel->where('role', 'receptionist')->countAllResults(),
                'pharmacist' => $this->userModel->where('role', 'pharmacist')->countAllResults(),
            ],
            'recent_users' => $this->userModel
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->find()
        ];
        
        // Cache for 5 minutes
        $this->cacheData($cacheKey, $stats, 300);
        
        return $this->sendSuccess($stats);
    }

    /**
     * Admin-only: update another user's password
     */
    public function adminUpdatePassword()
    {
        // Require authentication and admin role
        if (!$this->requireRole(['admin'])) return;
        
        // Validate input
        $rules = [
            'admin_id' => 'required|is_natural_no_zero',
            'user_id' => 'required|is_natural_no_zero',
            'new_password' => 'required|min_length[8]'
        ];
        
        if (!$this->validateRequest($rules)) return;
        
        $data = $this->sanitizeInput($this->getJsonData());
        
        // Verify the admin is the current user
        if ($data['admin_id'] != $this->currentUser['user_id']) {
            return $this->sendForbidden('You can only perform actions with your own admin account');
        }

        $admin = $this->userModel->find($data['admin_id']);

        if (!$admin || $admin['role'] !== 'admin' || $admin['status'] !== 'active') {
            return $this->sendForbidden('Only active admin users can update passwords');
        }

        $user = $this->userModel->find($data['user_id']);

        if (!$user) {
            return $this->sendNotFound('User not found');
        }
        
        // Validate new password strength
        $passwordErrors = $this->security->validatePasswordStrength($data['new_password']);
        if (!empty($passwordErrors)) {
            return $this->sendValidationError($passwordErrors);
        }
        
        // Hash the new password
        $hashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);

        if ($this->userModel->update($data['user_id'], ['password' => $hashedPassword])) {
            // Clear user cache
            $this->clearCache('user_' . $data['user_id']);
            
            // Log activity
            $this->logActivity('password_updated_by_admin', [
                'entity_type' => 'user',
                'entity_id' => $data['user_id'],
                'details' => 'Admin updated password for user: ' . $user['name']
            ]);
            
            // Log security event
            $this->securityLogModel->logEvent(
                'password_changed_by_admin',
                $this->currentUser['user_id'],
                $this->request->getIPAddress(),
                $this->request->getUserAgent(),
                $this->request->getPath(),
                $this->request->getMethod(),
                [
                    'target_user_id' => $data['user_id'],
                    'target_user_email' => $user['email']
                ],
                'medium'
            );
            
            return $this->sendSuccess(null, 'Password updated successfully');
        }

        return $this->sendServerError('Failed to update password');
    }
}