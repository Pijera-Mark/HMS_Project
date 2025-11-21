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
}
