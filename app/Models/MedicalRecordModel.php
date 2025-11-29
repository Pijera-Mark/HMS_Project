<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicalRecordModel extends Model
{
    protected $table            = 'medical_records';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'patient_id',
        'doctor_id',
        'appointment_id',
        'admission_id',
        'visit_date',
        'diagnosis',
        'symptoms',
        'treatment',
        'prescription',
        'notes',
        'follow_up_date',
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
        'visit_date'  => 'required|valid_date',
        'diagnosis'   => 'required|min_length[3]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    /**
     * Get medical records with filters
     */
    public function getRecordsWithFilters(array $filters = []): array
    {
        $builder = $this->builder()
            ->select('medical_records.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name, 
                     doctors.first_name as doctor_first_name, doctors.last_name as doctor_last_name,
                     branches.name as branch_name')
            ->join('patients', 'patients.patient_id = medical_records.patient_id', 'left')
            ->join('doctors', 'doctors.doctor_id = medical_records.doctor_id', 'left')
            ->join('branches', 'branches.id = medical_records.branch_id', 'left');

        if (!empty($filters['patient_id'])) {
            $builder->where('medical_records.patient_id', $filters['patient_id']);
        }

        if (!empty($filters['doctor_id'])) {
            $builder->where('medical_records.doctor_id', $filters['doctor_id']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('medical_records.visit_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('medical_records.visit_date <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('patients.first_name', $filters['search'])
                ->orLike('patients.last_name', $filters['search'])
                ->orLike('doctors.first_name', $filters['search'])
                ->orLike('doctors.last_name', $filters['search'])
                ->orLike('medical_records.diagnosis', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('medical_records.visit_date', 'DESC')->get()->getResultArray();
    }

    /**
     * Get medical records by patient
     */
    public function getPatientRecords($patientId, $branchId = null)
    {
        $builder = $this->where('patient_id', $patientId);

        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }

        return $builder->orderBy('visit_date', 'DESC')->findAll();
    }

    /**
     * Get record with details
     */
    public function getRecordWithDetails(int $id): ?array
    {
        return $this->builder()
            ->select('medical_records.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name,
                     doctors.first_name as doctor_first_name, doctors.last_name as doctor_last_name,
                     branches.name as branch_name')
            ->join('patients', 'patients.patient_id = medical_records.patient_id', 'left')
            ->join('doctors', 'doctors.doctor_id = medical_records.doctor_id', 'left')
            ->join('branches', 'branches.id = medical_records.branch_id', 'left')
            ->where('medical_records.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Create medical record
     */
    public function createRecord(array $data): bool
    {
        $data['branch_id'] = $data['branch_id'] ?? session()->get('branch_id');
        
        return $this->insert($data) !== false;
    }

    /**
     * Update medical record
     */
    public function updateRecord(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Get patient medical history
     */
    public function getPatientMedicalHistory(int $patientId): array
    {
        return $this->builder()
            ->select('medical_records.*, doctors.first_name as doctor_first_name, doctors.last_name as doctor_last_name')
            ->join('doctors', 'doctors.doctor_id = medical_records.doctor_id', 'left')
            ->where('medical_records.patient_id', $patientId)
            ->orderBy('medical_records.visit_date', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get medical record statistics
     */
    public function getMedicalRecordStats(array $filters = []): array
    {
        $builder = $this->builder();

        if (!empty($filters['date_from'])) {
            $builder->where('visit_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('visit_date <=', $filters['date_to']);
        }

        if (!empty($filters['branch_id'])) {
            $builder->where('branch_id', $filters['branch_id']);
        }

        $totalRecords = $builder->countAllResults(false);

        // Get diagnosis statistics
        $diagnosisStats = $this->builder()
            ->select('diagnosis, COUNT(*) as count')
            ->groupBy('diagnosis')
            ->orderBy('count', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return [
            'total_records' => $totalRecords,
            'top_diagnoses' => $diagnosisStats
        ];
    }
}
