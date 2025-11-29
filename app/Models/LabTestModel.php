<?php

namespace App\Models;

use CodeIgniter\Model;

class LabTestModel extends Model
{
    protected $table            = 'lab_tests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'patient_id',
        'doctor_id',
        'test_name',
        'test_type',
        'test_date',
        'result',
        'result_date',
        'status',
        'notes',
        'cost',
        'result_file',
        'branch_id',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'patient_id' => 'required|integer',
        'doctor_id'  => 'required|integer',
        'test_name'  => 'required|min_length[3]',
        'test_type'  => 'required',
        'test_date'  => 'required|valid_date',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    /**
     * Get lab tests with filters
     */
    public function getTestsWithFilters(array $filters = []): array
    {
        $builder = $this->builder()
            ->select('lab_tests.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name,
                     doctors.first_name as doctor_first_name, doctors.last_name as doctor_last_name,
                     branches.name as branch_name')
            ->join('patients', 'patients.id = lab_tests.patient_id', 'left')
            ->join('doctors', 'doctors.id = lab_tests.doctor_id', 'left')
            ->join('branches', 'branches.id = lab_tests.branch_id', 'left');

        if (!empty($filters['patient_id'])) {
            $builder->where('lab_tests.patient_id', $filters['patient_id']);
        }

        if (!empty($filters['doctor_id'])) {
            $builder->where('lab_tests.doctor_id', $filters['doctor_id']);
        }

        if (!empty($filters['test_type'])) {
            $builder->where('lab_tests.test_type', $filters['test_type']);
        }

        if (!empty($filters['status'])) {
            $builder->where('lab_tests.status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('lab_tests.test_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('lab_tests.test_date <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('patients.first_name', $filters['search'])
                ->orLike('patients.last_name', $filters['search'])
                ->orLike('doctors.first_name', $filters['search'])
                ->orLike('doctors.last_name', $filters['search'])
                ->orLike('lab_tests.test_name', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('lab_tests.test_date', 'DESC')->get()->getResultArray();
    }

    /**
     * Get lab tests by patient
     */
    public function getPatientTests($patientId, $branchId = null)
    {
        $builder = $this->where('patient_id', $patientId);

        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }

        return $builder->orderBy('test_date', 'DESC')->findAll();
    }

    /**
     * Get test with details
     */
    public function getTestWithDetails(int $id): ?array
    {
        return $this->builder()
            ->select('lab_tests.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name,
                     doctors.first_name as doctor_first_name, doctors.last_name as doctor_last_name,
                     branches.name as branch_name')
            ->join('patients', 'patients.id = lab_tests.patient_id', 'left')
            ->join('doctors', 'doctors.id = lab_tests.doctor_id', 'left')
            ->join('branches', 'branches.id = lab_tests.branch_id', 'left')
            ->where('lab_tests.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Create lab test
     */
    public function createTest(array $data): bool
    {
        $data['branch_id'] = $data['branch_id'] ?? session()->get('branch_id');
        $data['status'] = $data['status'] ?? 'ordered';
        
        return $this->insert($data) !== false;
    }

    /**
     * Update lab test
     */
    public function updateTest(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Get test types
     */
    public function getTestTypes(): array
    {
        return [
            'blood_test' => 'Blood Test',
            'urine_test' => 'Urine Test',
            'x_ray' => 'X-Ray',
            'ultrasound' => 'Ultrasound',
            'ct_scan' => 'CT Scan',
            'mri' => 'MRI',
            'ecg' => 'ECG',
            'eeg' => 'EEG',
            'pathology' => 'Pathology',
            'microbiology' => 'Microbiology'
        ];
    }

    /**
     * Get lab test statistics
     */
    public function getLabTestStats(array $filters = []): array
    {
        $builder = $this->builder();

        if (!empty($filters['date_from'])) {
            $builder->where('test_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('test_date <=', $filters['date_to']);
        }

        if (!empty($filters['branch_id'])) {
            $builder->where('branch_id', $filters['branch_id']);
        }

        $totalTests = $builder->countAllResults(false);

        // Get status statistics
        $statusStats = $this->builder()
            ->select('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->getResultArray();

        // Get test type statistics
        $typeStats = $this->builder()
            ->select('test_type, COUNT(*) as count')
            ->groupBy('test_type')
            ->orderBy('count', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return [
            'total_tests' => $totalTests,
            'status_breakdown' => $statusStats,
            'type_breakdown' => $typeStats
        ];
    }

    /**
     * Get pending tests
     */
    public function getPendingTests(): array
    {
        return $this->builder()
            ->select('lab_tests.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name')
            ->join('patients', 'patients.id = lab_tests.patient_id', 'left')
            ->where('lab_tests.status', 'ordered')
            ->orderBy('lab_tests.test_date', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Update test status
     */
    public function updateTestStatus(int $id, string $status): bool
    {
        $data = ['status' => $status];
        
        if ($status === 'completed') {
            $data['result_date'] = date('Y-m-d H:i:s');
        }
        
        return $this->update($id, $data);
    }
}
