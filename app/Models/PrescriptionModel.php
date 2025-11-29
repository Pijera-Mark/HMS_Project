<?php

namespace App\Models;

use CodeIgniter\Model;

class PrescriptionModel extends Model
{
    protected $table            = 'prescriptions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'patient_id',
        'doctor_id',
        'medical_record_id',
        'medicine_id',
        'dosage',
        'frequency',
        'duration',
        'quantity',
        'instructions',
        'status',
        'branch_id',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'patient_id'  => 'required|integer',
        'doctor_id'   => 'required|integer',
        'medicine_id' => 'required|integer',
        'dosage'      => 'required|min_length[2]',
        'frequency'   => 'required',
        'quantity'    => 'required|integer',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    /**
     * Get prescriptions with filters
     */
    public function getPrescriptionsWithFilters(array $filters = []): array
    {
        $builder = $this->builder()
            ->select('prescriptions.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name,
                     doctors.first_name as doctor_first_name, doctors.last_name as doctor_last_name,
                     medicines.name as medicine_name, branches.name as branch_name')
            ->join('patients', 'patients.patient_id = prescriptions.patient_id', 'left')
            ->join('doctors', 'doctors.doctor_id = prescriptions.doctor_id', 'left')
            ->join('medicines', 'medicines.id = prescriptions.medicine_id', 'left')
            ->join('branches', 'branches.id = prescriptions.branch_id', 'left');

        if (!empty($filters['patient_id'])) {
            $builder->where('prescriptions.patient_id', $filters['patient_id']);
        }

        if (!empty($filters['doctor_id'])) {
            $builder->where('prescriptions.doctor_id', $filters['doctor_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('prescriptions.status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('prescriptions.created_at >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('prescriptions.created_at <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('patients.first_name', $filters['search'])
                ->orLike('patients.last_name', $filters['search'])
                ->orLike('doctors.first_name', $filters['search'])
                ->orLike('doctors.last_name', $filters['search'])
                ->orLike('medicines.name', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('prescriptions.created_at', 'DESC')->get()->getResultArray();
    }

    /**
     * Get prescriptions by patient
     */
    public function getPatientPrescriptions($patientId, $branchId = null)
    {
        $builder = $this->builder()
            ->select('prescriptions.*, medicines.name as medicine_name, medicines.type as medicine_type')
            ->join('medicines', 'medicines.id = prescriptions.medicine_id', 'left')
            ->where('prescriptions.patient_id', $patientId);

        if ($branchId) {
            $builder->where('prescriptions.branch_id', $branchId);
        }

        return $builder->orderBy('prescriptions.created_at', 'DESC')->findAll();
    }

    /**
     * Get prescription with details
     */
    public function getPrescriptionWithDetails(int $id): ?array
    {
        return $this->builder()
            ->select('prescriptions.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name,
                     doctors.first_name as doctor_first_name, doctors.last_name as doctor_last_name,
                     medicines.name as medicine_name, medicines.type as medicine_type,
                     branches.name as branch_name')
            ->join('patients', 'patients.patient_id = prescriptions.patient_id', 'left')
            ->join('doctors', 'doctors.doctor_id = prescriptions.doctor_id', 'left')
            ->join('medicines', 'medicines.id = prescriptions.medicine_id', 'left')
            ->join('branches', 'branches.id = prescriptions.branch_id', 'left')
            ->where('prescriptions.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Create prescription
     */
    public function createPrescription(array $data): bool
    {
        $data['branch_id'] = $data['branch_id'] ?? session()->get('branch_id');
        $data['status'] = $data['status'] ?? 'pending';
        
        return $this->insert($data) !== false;
    }

    /**
     * Update prescription
     */
    public function updatePrescription(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Get prescription statistics
     */
    public function getPrescriptionStats(array $filters = []): array
    {
        $builder = $this->builder();

        if (!empty($filters['date_from'])) {
            $builder->where('created_at >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('created_at <=', $filters['date_to']);
        }

        if (!empty($filters['branch_id'])) {
            $builder->where('branch_id', $filters['branch_id']);
        }

        $totalPrescriptions = $builder->countAllResults(false);

        // Get status statistics
        $statusStats = $this->builder()
            ->select('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->getResultArray();

        return [
            'total_prescriptions' => $totalPrescriptions,
            'status_breakdown' => $statusStats
        ];
    }

    /**
     * Get active prescriptions for patient
     */
    public function getActivePrescriptions(int $patientId): array
    {
        return $this->builder()
            ->select('prescriptions.*, medicines.name as medicine_name')
            ->join('medicines', 'medicines.id = prescriptions.medicine_id', 'left')
            ->where('prescriptions.patient_id', $patientId)
            ->where('prescriptions.status', 'active')
            ->orderBy('prescriptions.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}
