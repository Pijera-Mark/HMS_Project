<?php

namespace App\Models;

use CodeIgniter\Model;

class WardModel extends Model
{
    protected $table            = 'wards';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'branch_id',
        'floor',
        'department',
        'total_beds',
        'available_beds',
        'ward_type',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'name'        => 'required|min_length[3]|max_length[100]',
        'branch_id'   => 'required|integer',
        'floor'       => 'required|integer',
        'total_beds'  => 'required|integer',
        'ward_type'   => 'required|in_list[general,private,icu,emergency,maternity,pediatric,surgical]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    /**
     * Get wards with filters
     */
    public function getWardsWithFilters(array $filters = []): array
    {
        $builder = $this->builder()
            ->select('wards.*, branches.name as branch_name')
            ->join('branches', 'branches.id = wards.branch_id', 'left');

        if (!empty($filters['branch_id'])) {
            $builder->where('wards.branch_id', $filters['branch_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('wards.status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('wards.name', $filters['search'])
                ->orLike('wards.department', $filters['search'])
                ->orLike('wards.ward_type', $filters['search'])
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get available wards
     */
    public function getAvailableWards()
    {
        return $this->where('available_beds >', 0)
                    ->where('status', 'active')
                    ->findAll();
    }

    /**
     * Get ward beds
     */
    public function getWardBeds(int $wardId): array
    {
        // Mock implementation - in real system, this would query beds table
        return [
            ['bed_number' => 'A101', 'status' => 'available', 'patient_id' => null],
            ['bed_number' => 'A102', 'status' => 'occupied', 'patient_id' => 123],
            ['bed_number' => 'A103', 'status' => 'available', 'patient_id' => null],
        ];
    }

    /**
     * Get ward patients
     */
    public function getWardPatients(int $wardId): array
    {
        // Mock implementation - in real system, this would query admissions/patients
        return [
            ['id' => 123, 'name' => 'John Doe', 'bed_number' => 'A102', 'admission_date' => '2025-11-20'],
            ['id' => 124, 'name' => 'Jane Smith', 'bed_number' => 'A105', 'admission_date' => '2025-11-22'],
        ];
    }

    /**
     * Create new ward
     */
    public function createWard(array $data): bool
    {
        $data['available_beds'] = $data['total_beds'];
        $data['status'] = 'active';
        
        return $this->insert($data) !== false;
    }

    /**
     * Update ward
     */
    public function updateWard(int $id, array $data): bool
    {
        // If total beds changed, update available beds
        if (isset($data['total_beds'])) {
            $currentWard = $this->find($id);
            if ($currentWard) {
                $occupiedBeds = $currentWard['total_beds'] - $currentWard['available_beds'];
                $data['available_beds'] = max(0, $data['total_beds'] - $occupiedBeds);
            }
        }

        return $this->update($id, $data);
    }

    /**
     * Check if ward has occupied beds
     */
    public function hasOccupiedBeds(int $wardId): bool
    {
        $ward = $this->find($wardId);
        return $ward && ($ward['total_beds'] > $ward['available_beds']);
    }

    /**
     * Update bed availability
     */
    public function updateBedAvailability(int $wardId, int $change): bool
    {
        $ward = $this->find($wardId);
        if (!$ward) {
            return false;
        }

        $newAvailable = $ward['available_beds'] + $change;
        
        if ($newAvailable < 0 || $newAvailable > $ward['total_beds']) {
            return false;
        }

        return $this->update($wardId, ['available_beds' => $newAvailable]);
    }

    /**
     * Get ward statistics
     */
    public function getWardStatistics(): array
    {
        $totalWards = $this->countAll();
        $activeWards = $this->where('status', 'active')->countAllResults();
        $totalBeds = $this->selectSum('total_beds')->get()->getRow()->total_beds ?? 0;
        $availableBeds = $this->selectSum('available_beds')->get()->getRow()->available_beds ?? 0;
        $occupiedBeds = $totalBeds - $availableBeds;

        return [
            'total_wards' => $totalWards,
            'active_wards' => $activeWards,
            'total_beds' => $totalBeds,
            'available_beds' => $availableBeds,
            'occupied_beds' => $occupiedBeds,
            'occupancy_rate' => $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 2) : 0
        ];
    }
}
