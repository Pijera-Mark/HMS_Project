<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'branch_id',
        'phone',
        'avatar_url',
        'status',
        'last_login',
        'password_reset_at',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'username' => 'required|min_length[3]|max_length[50]|alpha_numeric|is_unique[users.username,id,{id}]',
        'first_name' => 'required|min_length[2]|max_length[50]|alpha_space',
        'last_name' => 'required|min_length[2]|max_length[50]|alpha_space',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
        'role'     => 'required|in_list[admin,doctor,nurse,receptionist,pharmacist,lab_staff,accountant,it_staff]',
        'phone'    => 'permit_empty|regex_match[/^[\+]?[0-9]{10,15}$/]',
        'branch_id' => 'permit_empty|integer|is_not_unique[branches.id]'
    ];
    protected $validationMessages   = [
        'name' => [
            'required' => 'Name is required',
            'min_length' => 'Name must be at least 3 characters long',
            'alpha_space' => 'Name can only contain letters and spaces'
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'is_unique' => 'Email already exists'
        ],
        'password' => [
            'required' => 'Password is required',
            'min_length' => 'Password must be at least 8 characters long',
            'regex_match' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character'
        ],
        'role' => [
            'required' => 'Role is required',
            'in_list' => 'Invalid role selected'
        ],
        'phone' => [
            'regex_match' => 'Please enter a valid phone number'
        ],
        'branch_id' => [
            'integer' => 'Invalid branch selected',
            'is_not_unique' => 'Selected branch does not exist'
        ]
    ];
    protected $skipValidation       = false;

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    protected $afterInsert = ['recordLogin'];
    protected $afterUpdate = ['recordActivity'];

    /**
     * Hash password before insert/update
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    /**
     * Record login activity
     */
    protected function recordLogin(array $data)
    {
        if (isset($data['result'])) {
            $this->update($data['result']['id'], ['last_login' => date('Y-m-d H:i:s')]);
        }
        return $data;
    }

    /**
     * Record user activity
     */
    protected function recordActivity(array $data)
    {
        // Can be enhanced with activity logging
        return $data;
    }

    /**
     * Verify user credentials
     */
    public function verifyCredentials($email, $password)
    {
        $user = $this->where('email', $email)->first();
        
        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role)
    {
        return $this->where('role', $role)->where('status', 'active')->findAll();
    }

    /**
     * Get active users by branch
     */
    public function getUsersByBranch($branchId)
    {
        return $this->where('branch_id', $branchId)->where('status', 'active')->findAll();
    }

    /**
     * Soft delete user (change status to inactive)
     */
    public function softDelete($id)
    {
        return $this->update($id, ['status' => 'inactive']);
    }

    /**
     * Get all users with details
     */
    public function getAllUsersWithDetails()
    {
        return $this->select('users.*, branches.name as branch_name')
                    ->join('branches', 'branches.id = users.branch_id', 'left')
                    ->orderBy('users.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get filtered users
     */
    public function getFilteredUsers($search = null, $role = null, $branchId = null, $status = null)
    {
        $builder = $this->select('users.*, branches.name as branch_name')
                       ->join('branches', 'branches.id = users.branch_id', 'left');

        if ($search) {
            $builder->groupStart()
                    ->like('users.username', $search)
                    ->orLike('users.first_name', $search)
                    ->orLike('users.last_name', $search)
                    ->orLike('users.email', $search)
                    ->groupEnd();
        }

        if ($role) {
            $builder->where('users.role', $role);
        }

        if ($branchId) {
            $builder->where('users.branch_id', $branchId);
        }

        if ($status) {
            $builder->where('users.status', $status);
        }

        return $builder->orderBy('users.created_at', 'DESC')->findAll();
    }

    /**
     * Get available roles
     */
    public function getAvailableRoles()
    {
        return [
            'admin',
            'doctor', 
            'nurse',
            'receptionist',
            'lab_staff',
            'pharmacist',
            'accountant',
            'it_staff'
        ];
    }

    /**
     * Get branches
     */
    public function getBranches()
    {
        $db = \Config\Database::connect();
        return $db->table('branches')->select('id, name')->get()->getResultArray();
    }

    /**
     * Get users by role statistics
     */
    public function getUsersByRoleStats()
    {
        return $this->select('role, COUNT(*) as count')
                    ->groupBy('role')
                    ->get()
                    ->getResultArray();
    }

    /**
     * Get users by branch statistics
     */
    public function getUsersByBranch()
    {
        return $this->select('branches.name, COUNT(*) as count')
                    ->join('branches', 'branches.id = users.branch_id', 'left')
                    ->groupBy('users.branch_id')
                    ->get()
                    ->getResultArray();
    }

    /**
     * Get recent logins
     */
    public function getRecentLogins($limit = 10)
    {
        return $this->select('username, first_name, last_name, last_login')
                    ->where('last_login IS NOT NULL')
                    ->orderBy('last_login', 'DESC')
                    ->limit($limit)
                    ->get()
                    ->getResultArray();
    }

    /**
     * Update last login
     */
    public function updateLastLogin($userId)
    {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Check if username exists
     */
    public function usernameExists($username, $excludeId = null)
    {
        $builder = $this->where('username', $username);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeId = null)
    {
        $builder = $this->where('email', $email);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Get user by username
     */
    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Change user status
     */
    public function changeStatus($userId, $status)
    {
        return $this->update($userId, [
            'status' => $status,
            'updated_by' => session()->get('user')['user_id'] ?? null
        ]);
    }

    /**
     * Reset user password
     */
    public function resetPassword($userId, $newPassword)
    {
        return $this->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'password_reset_at' => date('Y-m-d H:i:s'),
            'updated_by' => session()->get('user')['user_id'] ?? null
        ]);
    }

    /**
     * Get user statistics
     */
    public function getUserStatistics()
    {
        return [
            'total' => $this->countAll(),
            'active' => $this->where('status', 'active')->countAllResults(),
            'inactive' => $this->where('status', 'inactive')->countAllResults(),
            'by_role' => $this->getUsersByRoleStats(),
            'by_branch' => $this->getUsersByBranch()
        ];
    }

    /**
     * Check if user has permission for specific action
     */
    public function hasPermission($userId, $permission)
    {
        $user = $this->find($userId);
        if (!$user) {
            return false;
        }

        $permissions = $this->getRolePermissions($user['role']);
        return in_array($permission, $permissions);
    }

    /**
     * Get permissions based on role
     */
    private function getRolePermissions($role)
    {
        $rolePermissions = [
            'admin' => ['view_dashboard', 'manage_users', 'manage_patients', 'manage_appointments', 'manage_billing', 'manage_inventory', 'view_reports'],
            'doctor' => ['view_dashboard', 'view_patients', 'manage_appointments', 'manage_medical_records', 'manage_prescriptions'],
            'nurse' => ['view_dashboard', 'view_patients', 'manage_appointments', 'manage_medical_records'],
            'receptionist' => ['view_dashboard', 'manage_patients', 'manage_appointments'],
            'pharmacist' => ['view_dashboard', 'manage_inventory', 'view_prescriptions'],
            'lab' => ['view_dashboard', 'manage_lab_tests', 'view_patients'],
            'accountant' => ['view_dashboard', 'manage_billing', 'view_reports'],
            'it' => ['view_dashboard', 'manage_users', 'system_maintenance']
        ];

        return $rolePermissions[$role] ?? [];
    }

    /**
     * Get user statistics
     */
    public function getUserStats($branchId = null)
    {
        $builder = $this->select('role, COUNT(*) as count')
                    ->where('status', 'active');
        
        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }
        
        return $builder->groupBy('role')->findAll();
    }
}
