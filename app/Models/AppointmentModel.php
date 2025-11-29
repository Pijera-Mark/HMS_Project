<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table            = 'appointments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'patient_id',
        'doctor_id',
        'scheduled_at',
        'duration_minutes',
        'status',
        'reason',
        'notes',
        'branch_id'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'patient_id'       => 'required|integer',
        'doctor_id'        => 'required|integer',
        'scheduled_at'     => 'required',
        'duration_minutes' => 'permit_empty|integer',
        'status'           => 'required|in_list[requested,scheduled,confirmed,cancelled,completed]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    /**
     * Get appointment statistics
     */
    public function getAppointmentStats(array $filters = []): array
    {
        $builder = $this->builder()
            ->select('
                COUNT(*) as total_appointments,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_count,
                SUM(CASE WHEN status = "scheduled" THEN 1 ELSE 0 END) as scheduled_count,
                SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed_count,
                SUM(CASE WHEN status = "requested" THEN 1 ELSE 0 END) as requested_count,
                AVG(duration_minutes) as average_duration
            ');

        if (!empty($filters['date_from'])) {
            $builder->where('DATE(scheduled_at) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('DATE(scheduled_at) <=', $filters['date_to']);
        }

        if (!empty($filters['branch_id'])) {
            $builder->where('branch_id', $filters['branch_id']);
        }

        $result = $builder->get()->getRowArray();
        
        return [
            'total_appointments' => (int)($result['total_appointments'] ?? 0),
            'completed_count' => (int)($result['completed_count'] ?? 0),
            'cancelled_count' => (int)($result['cancelled_count'] ?? 0),
            'scheduled_count' => (int)($result['scheduled_count'] ?? 0),
            'confirmed_count' => (int)($result['confirmed_count'] ?? 0),
            'requested_count' => (int)($result['requested_count'] ?? 0),
            'average_duration' => round((float)($result['average_duration'] ?? 0), 1),
            'completion_rate' => $result['total_appointments'] > 0 ? 
                (($result['completed_count'] / $result['total_appointments']) * 100) : 0
        ];
    }
}
