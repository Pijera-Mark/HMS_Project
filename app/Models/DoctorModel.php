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
}
