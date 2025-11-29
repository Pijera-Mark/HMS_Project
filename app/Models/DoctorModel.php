<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorModel extends Model
{
    protected $table            = 'doctors';
    protected $primaryKey       = 'doctor_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'first_name',
        'last_name',
        'specialization',
        'license_number',
        'phone',
        'email',
        'department',
        'qualification',
        'experience_years',
        'consultation_fee',
        'available_days',
        'available_hours',
        'status',
        'branch_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'first_name'      => 'required|min_length[2]|max_length[50]',
        'last_name'       => 'required|min_length[2]|max_length[50]',
        'specialization'  => 'required|min_length[3]|max_length[100]',
        'license_number'  => 'required|is_unique[doctors.license_number]',
        'phone'           => 'required|min_length[10]|max_length[15]',
        'email'           => 'required|valid_email|is_unique[doctors.email]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get doctors with filters
     */
    public function getDoctorsWithFilters(array $filters = []): array
    {
        $builder = $this->builder()
            ->select('doctors.*, branches.name as branch_name')
            ->join('branches', 'branches.id = doctors.branch_id', 'left');

        if (!empty($filters['specialization'])) {
            $builder->where('doctors.specialization', $filters['specialization']);
        }

        if (!empty($filters['status'])) {
            $builder->where('doctors.status', $filters['status']);
        }

        if (!empty($filters['branch_id'])) {
            $builder->where('doctors.branch_id', $filters['branch_id']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('doctors.first_name', $filters['search'])
                ->orLike('doctors.last_name', $filters['search'])
                ->orLike('doctors.specialization', $filters['search'])
                ->orLike('doctors.email', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('doctors.first_name', 'ASC')->get()->getResultArray();
    }

    /**
     * Get specializations
     */
    public function getSpecializations(): array
    {
        $specializations = $this->builder()
            ->select('specialization')
            ->distinct()
            ->where('status', 'active')
            ->orderBy('specialization', 'ASC')
            ->get()
            ->getResultArray();

        return array_column($specializations, 'specialization');
    }

    /**
     * Get active doctors
     */
    public function getActiveDoctors(): array
    {
        return $this->where('status', 'active')
                    ->orderBy('first_name', 'ASC')
                    ->findAll();
    }

    /**
     * Get doctors by branch
     */
    public function getDoctorsByBranch(int $branchId): array
    {
        return $this->where('branch_id', $branchId)
                    ->where('status', 'active')
                    ->orderBy('first_name', 'ASC')
                    ->findAll();
    }

    /**
     * Get doctor with details
     */
    public function getDoctorWithDetails(int $id): ?array
    {
        return $this->builder()
            ->select('doctors.*, branches.name as branch_name')
            ->join('branches', 'branches.id = doctors.branch_id', 'left')
            ->where('doctors.doctor_id', $id)
            ->get()
            ->getRowArray();
    }
}
