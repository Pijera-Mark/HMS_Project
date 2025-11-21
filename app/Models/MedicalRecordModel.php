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
        'branch_id'
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
     * Get medical records by patient
     */
    public function getPatientRecords($patientId, $branchId = null)
    {
        $builder = $this->where('patient_id', $patientId);

        if ($branchId) {
            $builder = $builder->where('branch_id', $branchId);
        }

        return $builder
            ->orderBy('visit_date', 'DESC')
            ->findAll();
    }

    /**
     * Get recent medical records
     */
    public function getRecentRecords($limit = 10)
    {
        return $this->orderBy('visit_date', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
