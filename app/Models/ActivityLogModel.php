<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table            = 'activity_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'user_name',
        'action',
        'entity_type',
        'entity_id',
        'details',
        'ip_address',
        'user_agent',
        'branch_id',
        'created_at'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    protected $validationRules      = [
        'action' => 'required|max_length[100]',
        'user_id' => 'permit_empty|integer|is_not_unique[users.id]',
        'entity_type' => 'permit_empty|max_length[50]',
        'entity_id' => 'permit_empty|integer',
        'branch_id' => 'permit_empty|integer|is_not_unique[branches.id]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    /**
     * Log user activity
     */
    public function logActivity($userId, $userName, $action, $entityType = null, $entityId = null, $details = null, $ipAddress = null, $userAgent = null, $branchId = null)
    {
        $data = [
            'user_id' => $userId,
            'user_name' => $userName,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'details' => is_array($details) ? json_encode($details) : $details,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'branch_id' => $branchId,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert($data);
    }

    /**
     * Get activities by user
     */
    public function getUserActivities($userId, $limit = 50)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get activities by entity
     */
    public function getEntityActivities($entityType, $entityId, $limit = 20)
    {
        return $this->where('entity_type', $entityType)
                    ->where('entity_id', $entityId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get activities by action type
     */
    public function getActivitiesByAction($action, $limit = 100)
    {
        return $this->where('action', $action)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get recent activities for dashboard
     */
    public function getRecentActivities($branchId = null, $limit = 20)
    {
        $builder = $this->orderBy('created_at', 'DESC')->limit($limit);
        
        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }
        
        return $builder->findAll();
    }

    /**
     * Get activity statistics
     */
    public function getActivityStats($branchId = null, $days = 30)
    {
        $builder = $this->select('action, COUNT(*) as count, DATE(created_at) as date')
                        ->where('created_at >=', date('Y-m-d', strtotime("-{$days} days")))
                        ->groupBy('action, DATE(created_at)')
                        ->orderBy('date', 'DESC');
        
        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }
        
        return $builder->findAll();
    }

    /**
     * Get login activities
     */
    public function getLoginActivities($branchId = null, $days = 7)
    {
        $builder = $this->where('action', 'login')
                        ->where('created_at >=', date('Y-m-d', strtotime("-{$days} days")))
                        ->orderBy('created_at', 'DESC');
        
        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }
        
        return $builder->findAll();
    }

    /**
     * Get failed login attempts
     */
    public function getFailedLogins($branchId = null, $days = 7)
    {
        $builder = $this->where('action', 'login_failed')
                        ->where('created_at >=', date('Y-m-d', strtotime("-{$days} days")))
                        ->orderBy('created_at', 'DESC');
        
        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }
        
        return $builder->findAll();
    }

    /**
     * Clean old logs (older than specified days)
     */
    public function cleanOldLogs($days = 90)
    {
        return $this->where('created_at <', date('Y-m-d H:i:s', strtotime("-{$days} days")))
                    ->delete();
    }

    /**
     * Get activities for audit trail
     */
    public function getAuditTrail($filters = [], $page = 1, $limit = 50)
    {
        $builder = $this->select('activity_logs.*, users.email as user_email')
                        ->join('users', 'users.id = activity_logs.user_id', 'left')
                        ->orderBy('activity_logs.created_at', 'DESC');

        // Apply filters
        if (isset($filters['user_id'])) {
            $builder->where('activity_logs.user_id', $filters['user_id']);
        }
        
        if (isset($filters['action'])) {
            $builder->where('activity_logs.action', $filters['action']);
        }
        
        if (isset($filters['entity_type'])) {
            $builder->where('activity_logs.entity_type', $filters['entity_type']);
        }
        
        if (isset($filters['branch_id'])) {
            $builder->where('activity_logs.branch_id', $filters['branch_id']);
        }
        
        if (isset($filters['date_from'])) {
            $builder->where('activity_logs.created_at >=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $builder->where('activity_logs.created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        $total = $builder->countAllResults(false);
        $offset = ($page - 1) * $limit;
        
        $records = $builder->limit($limit, $offset)->findAll();

        return [
            'records' => $records,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ];
    }
}
