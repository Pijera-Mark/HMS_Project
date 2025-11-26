<?php

namespace App\Controllers\Admin;

use App\Controllers\EnhancedBaseController;
use App\Models\UserModel;
use App\Models\ActivityLogModel;

/**
 * Secure User Management Controller
 * - User CRUD operations
 * - Password reset functionality
 * - Role management
 * - Activity logging
 * - Security features
 */
class UserManagementController extends EnhancedBaseController
{
    protected UserModel $userModel;
    protected ActivityLogModel $activityLog;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->activityLog = new ActivityLogModel();
    }

    /**
     * Display user management page
     */
    public function index()
    {
        $this->requireRole(['admin', 'it_staff']);

        $data = [
            'users' => $this->userModel->getAllUsersWithDetails(),
            'roles' => $this->userModel->getAvailableRoles(),
            'branches' => $this->userModel->getBranches(),
            'stats' => $this->getUserStats()
        ];

        return view('admin/user_management', $data);
    }

    /**
     * Get user list (AJAX)
     */
    public function getUsers()
    {
        $this->requireRole(['admin', 'it_staff']);

        $search = $this->request->getGet('search');
        $role = $this->request->getGet('role');
        $branch = $this->request->getGet('branch');
        $status = $this->request->getGet('status');

        $users = $this->userModel->getFilteredUsers($search, $role, $branch, $status);

        return $this->sendSuccess($users);
    }

    /**
     * Create new user
     */
    public function createUser()
    {
        $this->requireRole(['admin', 'it_staff']);

        // Check if there's already an admin account
        if ($this->request->getPost('role') === 'admin') {
            $existingAdmin = $this->userModel->where('role', 'admin')->where('status', 'active')->first();
            if ($existingAdmin) {
                return $this->sendForbidden('Admin account already exists. Only one admin is allowed.');
            }
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'first_name' => 'required|min_length[2]|max_length[50]',
                'last_name' => 'required|min_length[2]|max_length[50]',
                'role' => 'required|in_list[doctor,nurse,receptionist,lab_staff,pharmacist,accountant,it_staff]',
                'branch_id' => 'required|integer|is_not_unique[branches.id]',
                'phone' => 'permit_empty|regex_match[/^[\+]?[0-9]{10,15}$/]',
                'password' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]'
            ];

            // Only allow admin role if no admin exists
            $existingAdmin = $this->userModel->where('role', 'admin')->where('status', 'active')->first();
            if (!$existingAdmin) {
                $rules['role'] = 'required|in_list[admin,doctor,nurse,receptionist,lab_staff,pharmacist,accountant,it_staff]';
            }

            if (!$this->validateRequest($rules)) {
                return;
            }

            $userData = $this->sanitizeInput($this->request->getPost());

            // Hash password
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            $userData['status'] = 'active';
            $userData['created_by'] = $this->currentUser['user_id'];

            if ($this->userModel->insert($userData)) {
                $this->logActivity('User Created', "Created user: {$userData['username']}", $userData);
                return $this->sendSuccess(null, 'User created successfully');
            }

            return $this->sendServerError('Failed to create user');
        }

        // Check if admin already exists to hide admin option
        $existingAdmin = $this->userModel->where('role', 'admin')->where('status', 'active')->first();
        
        return view('admin/create_user', [
            'roles' => $this->getAvailableRolesForCreation($existingAdmin),
            'branches' => $this->userModel->getBranches(),
            'adminExists' => (bool)$existingAdmin
        ]);
    }

    /**
     * Edit user
     */
    public function editUser($userId)
    {
        $this->requireRole(['admin', 'it_staff']);

        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return $this->sendNotFound('User not found');
        }

        // Prevent editing admin role if admin already exists and this is not the admin
        if ($user['role'] === 'admin') {
            $currentAdmin = $this->userModel->where('role', 'admin')->where('id !=', $userId)->where('status', 'active')->first();
            if ($currentAdmin) {
                return $this->sendForbidden('Cannot edit admin account when another admin exists.');
            }
        }

        if ($this->request->getMethod() === 'POST') {
            // Check if trying to change role to admin when admin already exists
            if ($this->request->getPost('role') === 'admin') {
                $existingAdmin = $this->userModel->where('role', 'admin')->where('id !=', $userId)->where('status', 'active')->first();
                if ($existingAdmin) {
                    return $this->sendForbidden('Admin account already exists. Only one admin is allowed.');
                }
            }

            $rules = [
                'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$userId}]",
                'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
                'first_name' => 'required|min_length[2]|max_length[50]',
                'last_name' => 'required|min_length[2]|max_length[50]',
                'role' => 'required|in_list[doctor,nurse,receptionist,lab_staff,pharmacist,accountant,it_staff]',
                'branch_id' => 'required|integer|is_not_unique[branches.id]',
                'phone' => 'permit_empty|regex_match[/^[\+]?[0-9]{10,15}$/]'
            ];

            // Allow admin role only if this is the admin or no admin exists
            $existingAdmin = $this->userModel->where('role', 'admin')->where('id !=', $userId)->where('status', 'active')->first();
            if (!$existingAdmin || $user['role'] === 'admin') {
                $rules['role'] = 'required|in_list[admin,doctor,nurse,receptionist,lab_staff,pharmacist,accountant,it_staff]';
            }

            if (!$this->validateRequest($rules)) {
                return;
            }

            $userData = $this->sanitizeInput($this->request->getPost());
            
            // Don't update password if empty
            if (empty($userData['password'])) {
                unset($userData['password']);
            } else {
                // Validate new password
                if (!$this->validateRequest(['password' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]'])) {
                    return;
                }
                $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            }

            $userData['updated_by'] = $this->currentUser['user_id'];

            if ($this->userModel->update($userId, $userData)) {
                $this->logActivity('User Updated', "Updated user: {$userData['username']}", $userData);
                return $this->sendSuccess(null, 'User updated successfully');
            }

            return $this->sendServerError('Failed to update user');
        }

        // Check if admin already exists to hide admin option
        $existingAdmin = $this->userModel->where('role', 'admin')->where('id !=', $userId)->where('status', 'active')->first();
        
        return view('admin/edit_user', [
            'user' => $user,
            'roles' => $this->getAvailableRolesForCreation($existingAdmin, $user),
            'branches' => $this->userModel->getBranches(),
            'canEditAdmin' => !$existingAdmin || $user['role'] === 'admin'
        ]);
    }

    /**
     * Reset user password (secure method)
     */
    public function resetPassword($userId)
    {
        $this->requireRole(['admin', 'it_staff']);

        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return $this->sendNotFound('User not found');
        }

        if ($this->request->getMethod() === 'POST') {
            // Generate secure random password
            $newPassword = $this->generateSecurePassword();
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            if ($this->userModel->update($userId, [
                'password' => $hashedPassword,
                'password_reset_at' => date('Y-m-d H:i:s'),
                'updated_by' => $this->currentUser['user_id']
            ])) {
                $this->logActivity('Password Reset', "Reset password for user: {$user['username']}", ['user_id' => $userId]);
                
                return $this->sendSuccess([
                    'new_password' => $newPassword,
                    'username' => $user['username'],
                    'reset_time' => date('Y-m-d H:i:s')
                ], 'Password reset successfully');
            }

            return $this->sendServerError('Failed to reset password');
        }

        return view('admin/reset_password', ['user' => $user]);
    }

    /**
     * Change user status (activate/deactivate)
     */
    public function changeStatus($userId)
    {
        $this->requireRole(['admin', 'it_staff']);

        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return $this->sendNotFound('User not found');
        }

        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';

        if ($this->userModel->update($userId, [
            'status' => $newStatus,
            'updated_by' => $this->currentUser['user_id']
        ])) {
            $this->logActivity('Status Changed', "Changed status for user: {$user['username']} to {$newStatus}", ['user_id' => $userId]);
            return $this->sendSuccess(['status' => $newStatus], "User status changed to {$newStatus}");
        }

        return $this->sendServerError('Failed to change status');
    }

    /**
     * Delete user (soft delete)
     */
    public function deleteUser($userId)
    {
        $this->requireRole(['admin']);

        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return $this->sendNotFound('User not found');
        }

        // Prevent self-deletion
        if ($userId == $this->currentUser['user_id']) {
            return $this->sendForbidden('Cannot delete your own account');
        }

        // Prevent deletion of admin account
        if ($user['role'] === 'admin') {
            return $this->sendForbidden('Cannot delete admin account');
        }

        if ($this->userModel->delete($userId)) {
            $this->logActivity('User Deleted', "Deleted user: {$user['username']}", ['user_id' => $userId]);
            return $this->sendSuccess(null, 'User deleted successfully');
        }

        return $this->sendServerError('Failed to delete user');
    }

    /**
     * Get available roles for user creation/editing
     */
    protected function getAvailableRolesForCreation($existingAdmin = null, $currentUser = null)
    {
        $roles = [
            'doctor',
            'nurse', 
            'receptionist',
            'lab_staff',
            'pharmacist',
            'accountant',
            'it_staff'
        ];

        // Only allow admin role if no admin exists or editing existing admin
        if (!$existingAdmin || ($currentUser && $currentUser['role'] === 'admin')) {
            array_unshift($roles, 'admin');
        }

        return $roles;
    }

    /**
     * Get user activity log
     */
    public function getUserActivity($userId)
    {
        $this->requireRole(['admin', 'it_staff']);

        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return $this->sendNotFound('User not found');
        }

        $activities = $this->activityLog->getUserActivities($userId, 50);

        return $this->sendSuccess([
            'user' => $user,
            'activities' => $activities
        ]);
    }

    /**
     * Export users to CSV/Excel
     */
    public function exportUsers()
    {
        $this->requireRole(['admin', 'it_staff']);

        $format = $this->request->getGet('format', 'csv');
        $filters = $this->request->getGet();

        $users = $this->userModel->getFilteredUsers(
            $filters['search'] ?? null,
            $filters['role'] ?? null,
            $filters['branch'] ?? null,
            $filters['status'] ?? null
        );

        if ($format === 'csv') {
            return $this->exportToCSV($users);
        } elseif ($format === 'excel') {
            return $this->exportToExcel($users);
        }

        return $this->sendError('Invalid export format');
    }

    /**
     * Get user statistics
     */
    protected function getUserStats(): array
    {
        return [
            'total_users' => $this->userModel->countAll(),
            'active_users' => $this->userModel->where('status', 'active')->countAllResults(),
            'inactive_users' => $this->userModel->where('status', 'inactive')->countAllResults(),
            'by_role' => $this->userModel->getUsersByRoleStats(),
            'by_branch' => $this->userModel->getUsersByBranchStats(),
            'recent_logins' => $this->userModel->getRecentLogins(10)
        ];
    }

    /**
     * Generate secure random password
     */
    protected function generateSecurePassword(int $length = 12): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@$!%*?&';
        $password = '';
        
        // Ensure at least one of each required character type
        $password .= 'abcdefghijklmnopqrstuvwxyz'[rand(0, 25)];
        $password .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[rand(0, 25)];
        $password .= '0123456789'[rand(0, 9)];
        $password .= '@$!%*?&'[rand(0, 7)];
        
        // Fill remaining length
        for ($i = 4; $i < $length; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }
        
        return str_shuffle($password);
    }

    /**
     * Log user management activity
     */
    protected function logActivity(string $action, string $description, array $data = []): void
    {
        $this->activityLog->insert([
            'user_id' => $this->currentUser['user_id'],
            'action' => $action,
            'description' => $description,
            'data' => json_encode($data),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()
        ]);
    }

    /**
     * Export to CSV
     */
    protected function exportToCSV(array $users): void
    {
        $filename = 'users_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Header
        fputcsv($output, [
            'User ID', 'Username', 'Email', 'First Name', 'Last Name', 
            'Role', 'Branch', 'Phone', 'Status', 'Created At', 'Last Login'
        ]);
        
        // Data
        foreach ($users as $user) {
            fputcsv($output, [
                $user['user_id'],
                $user['username'],
                $user['email'],
                $user['first_name'],
                $user['last_name'],
                $user['role'],
                $user['branch_name'] ?? 'N/A',
                $user['phone'] ?? 'N/A',
                $user['status'],
                $user['created_at'],
                $user['last_login'] ?? 'Never'
            ]);
        }
        
        fclose($output);
        exit();
    }

    /**
     * Export to Excel (simplified version)
     */
    protected function exportToExcel(array $users): void
    {
        // For now, redirect to CSV export
        // In production, you'd use a library like PhpSpreadsheet
        $this->exportToCSV($users);
    }
}
