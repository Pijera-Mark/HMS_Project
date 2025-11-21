<?php

namespace App\Models;

use CodeIgniter\Model;

class AdmissionModel extends Model
{
    protected $table            = 'admissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'patient_id',
        'admitted_by',
        'assigned_doctor_id',
        'ward_id',
        'room_number',
        'bed_number',
        'admission_date',
        'discharge_date',
        'status',
        'notes',
        'branch_id'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'patient_id'         => 'required|integer',
        'admitted_by'        => 'required|integer',
        'assigned_doctor_id' => 'required|integer',
        'ward_id'            => 'permit_empty|integer',
        'admission_date'     => 'required',
        'status'             => 'required|in_list[admitted,discharged,transferred]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    /**
     * Get active admissions
     */
    public function getActiveAdmissions($branchId = null)
    {
        $builder = $this->where('status', 'admitted');

        if ($branchId) {
            $builder = $builder->where('branch_id', $branchId);
        }

        return $builder->findAll();
    }

    /**
     * Discharge patient
     */
    public function dischargePatient($admissionId, $dischargeDate, $notes = '')
    {
        return $this->update($admissionId, [
            'discharge_date' => $dischargeDate,
            'status'         => 'discharged',
            'notes'          => $notes
        ]);
    }
}
