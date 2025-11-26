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
        'name',
        'email',
        'password',
        'role',
        'branch_id',
        'phone',
        'avatar_url',
        'status',
        'last_login'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'name'     => 'required|min_length[3]|max_length[255]|alpha_space',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
        'role'     => 'required|in_list[admin,doctor,nurse,receptionist,pharmacist,lab,accountant,it]',
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
