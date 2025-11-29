<?php

namespace App\Services;

/**
 * Activity Service - Centralized activity logging
 * Eliminates redundancy across controllers
 */
class ActivityService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Log user activity
     */
    public function logActivity(string $action, array $context = []): bool
    {
        $logData = array_merge($context, [
            'action' => $action,
            'ip_address' => $this->getIpAddress(),
            'user_agent' => $this->getUserAgent(),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->db->table('activity_logs')->insert($logData);
    }

    /**
     * Log security event
     */
    public function logSecurityEvent(string $event, array $context = []): bool
    {
        $logData = array_merge($context, [
            'event_type' => $event,
            'ip_address' => $this->getIpAddress(),
            'user_agent' => $this->getUserAgent(),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->db->table('security_logs')->insert($logData);
    }

    /**
     * Get user activities
     */
    public function getUserActivities(int $userId, int $limit = 50): array
    {
        return $this->db->table('activity_logs')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get recent activities for dashboard
     */
    public function getRecentActivities(int $userId = null, int $limit = 10): array
    {
        $query = $this->db->table('activity_logs')
            ->orderBy('created_at', 'DESC')
            ->limit($limit);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->get()->getResultArray();
    }

    /**
     * Get activity statistics
     */
    public function getActivityStats(int $userId = null, string $period = '7 days'): array
    {
        $query = $this->db->table('activity_logs')
            ->select('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderBy('count', 'DESC');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Add date filter based on period
        switch ($period) {
            case '1 day':
                $query->where('created_at >=', date('Y-m-d H:i:s', strtotime('-1 day')));
                break;
            case '7 days':
                $query->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')));
                break;
            case '30 days':
                $query->where('created_at >=', date('Y-m-d H:i:s', strtotime('-30 days')));
                break;
        }

        return $query->get()->getResultArray();
    }

    /**
     * Get login history
     */
    public function getLoginHistory(int $userId, int $limit = 20): array
    {
        return $this->db->table('activity_logs')
            ->where('user_id', $userId)
            ->where('action', 'user_login')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get failed login attempts
     */
    public function getFailedLoginAttempts(string $email = null, int $limit = 20): array
    {
        $query = $this->db->table('activity_logs')
            ->where('action', 'login_failed')
            ->orderBy('created_at', 'DESC')
            ->limit($limit);

        if ($email) {
            $query->like('details', $email);
        }

        return $query->get()->getResultArray();
    }

    /**
     * Check for suspicious activity
     */
    public function checkSuspiciousActivity(int $userId): array
    {
        $suspicious = [];

        // Check for multiple failed logins
        $failedLogins = $this->db->table('activity_logs')
            ->where('action', 'login_failed')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-1 hour')))
            ->countAllResults();

        if ($failedLogins > 5) {
            $suspicious[] = 'Multiple failed login attempts detected';
        }

        // Check for logins from unusual locations
        $recentLogins = $this->db->table('activity_logs')
            ->where('user_id', $userId)
            ->where('action', 'user_login')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->get()
            ->getResultArray();

        $ipAddresses = array_unique(array_column($recentLogins, 'ip_address'));
        if (count($ipAddresses) > 3) {
            $suspicious[] = 'Logins from multiple IP addresses detected';
        }

        return $suspicious;
    }

    /**
     * Clean old activity logs
     */
    public function cleanOldLogs(int $days = 90): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->db->table('activity_logs')
            ->where('created_at <', $cutoffDate)
            ->delete();
    }

    /**
     * Export activity logs
     */
    public function exportLogs(array $filters = []): array
    {
        $query = $this->db->table('activity_logs')
            ->select('activity_logs.*, users.name as user_name, users.email')
            ->join('users', 'users.id = activity_logs.user_id', 'left')
            ->orderBy('activity_logs.created_at', 'DESC');

        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('activity_logs.user_id', $filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->where('activity_logs.action', $filters['action']);
        }

        if (isset($filters['date_from'])) {
            $query->where('activity_logs.created_at >=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('activity_logs.created_at <=', $filters['date_to']);
        }

        if (isset($filters['limit'])) {
            $query->limit($filters['limit']);
        }

        return $query->get()->getResultArray();
    }

    /**
     * Get activity heatmap data
     */
    public function getActivityHeatmap(int $userId = null, int $days = 30): array
    {
        $query = $this->db->table('activity_logs')
            ->select('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at >=', date('Y-m-d', strtotime("-{$days} days")))
            ->groupBy('DATE(created_at)')
            ->orderBy('date');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $results = $query->get()->getResultArray();
        
        // Fill missing dates with 0
        $heatmap = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $count = 0;
            
            foreach ($results as $result) {
                if ($result['date'] === $date) {
                    $count = (int) $result['count'];
                    break;
                }
            }
            
            $heatmap[$date] = $count;
        }

        return $heatmap;
    }

    /**
     * Helper methods
     */
    private function getIpAddress(): string
    {
        $request = \Config\Services::request();
        return $request->getIPAddress();
    }

    private function getUserAgent(): string
    {
        $request = \Config\Services::request();
        return $request->getUserAgent();
    }

    /**
     * Create activity log entry with context
     */
    public function createLog(string $action, int $userId = null, string $entityType = null, int $entityId = null, array $details = []): bool
    {
        $context = [
            'user_id' => $userId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'details' => is_array($details) ? json_encode($details) : $details
        ];

        return $this->logActivity($action, $context);
    }

    /**
     * Bulk log activities
     */
    public function bulkLogActivities(array $activities): bool
    {
        if (empty($activities)) {
            return true;
        }

        $data = [];
        foreach ($activities as $activity) {
            $data[] = array_merge($activity, [
                'ip_address' => $this->getIpAddress(),
                'user_agent' => $this->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->db->table('activity_logs')->insertBatch($data);
    }
}
