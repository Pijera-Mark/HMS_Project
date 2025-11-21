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
        'branch_id'
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
     * Get lab tests by patient
     */
    public function getPatientTests($patientId, $branchId = null)
    {
        $builder = $this->where('patient_id', $patientId);

        if ($branchId) {
            $builder = $builder->where('branch_id', $branchId);
        }

        return $builder
            ->orderBy('test_date', 'DESC')
            ->findAll();
    }

    /**
     * Get pending lab tests
     */
    public function getPendingTests($branchId = null)
    {
        $builder = $this->where('status', 'pending');

        if ($branchId) {
            $builder = $builder->where('branch_id', $branchId);
        }

        return $builder
            ->orderBy('test_date', 'ASC')
            ->findAll();
    }

    /**
     * Get completed lab tests
     */
    public function getCompletedTests($branchId = null)
    {
        $builder = $this->where('status', 'completed');

        if ($branchId) {
            $builder = $builder->where('branch_id', $branchId);
        }

        return $builder
            ->orderBy('result_date', 'DESC')
            ->findAll();
    }

    /**
     * Update test status
     */
    public function updateTestStatus($id, $status, $result = null, $resultDate = null)
    {
        $data = ['status' => $status];
        
        if ($result !== null) {
            $data['result'] = $result;
        }
        
        if ($resultDate !== null) {
            $data['result_date'] = $resultDate;
        }
        
        return $this->update($id, $data);
    }
}
