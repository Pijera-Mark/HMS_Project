<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientModel extends Model
{
    protected $table            = 'patients';
    protected $primaryKey       = 'patient_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'blood_type',
        'phone',
        'email',
        'address',
        'emergency_contact',
        'emergency_phone',
        'medical_history',
        'allergies',
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
        'first_name'     => 'required|min_length[2]|max_length[50]|alpha_space',
        'last_name'      => 'required|min_length[2]|max_length[50]|alpha_space',
        'date_of_birth'  => 'required|valid_date|check_age',
        'gender'         => 'required|in_list[Male,Female,Other]',
        'blood_type'     => 'permit_empty|in_list[A+,A-,B+,B-,AB+,AB-,O+,O-]',
        'phone'          => 'required|min_length[10]|max_length[15]|regex_match[/^[\\+]?[0-9]{10,15}$/]',
        'email'          => 'permit_empty|valid_email',
        'emergency_phone' => 'required|min_length[10]|max_length[15]|regex_match[/^[\\+]?[0-9]{10,15}$/]',
        'branch_id'      => 'permit_empty|integer|is_not_unique[branches.id]'
    ];
    protected $validationMessages   = [
        'first_name' => [
            'required' => 'First name is required',
            'min_length' => 'First name must be at least 2 characters long',
            'max_length' => 'First name cannot exceed 50 characters',
            'alpha_space' => 'First name can only contain letters and spaces'
        ],
        'last_name' => [
            'required' => 'Last name is required',
            'min_length' => 'Last name must be at least 2 characters long',
            'max_length' => 'Last name cannot exceed 50 characters',
            'alpha_space' => 'Last name can only contain letters and spaces'
        ],
        'date_of_birth' => [
            'required' => 'Date of birth is required',
            'valid_date' => 'Please enter a valid date of birth',
            'check_age' => 'Patient must be between 0 and 150 years old'
        ],
        'gender' => [
            'required' => 'Gender is required',
            'in_list' => 'Invalid gender selected'
        ],
        'blood_type' => [
            'in_list' => 'Invalid blood type selected'
        ],
        'phone' => [
            'required' => 'Phone number is required',
            'min_length' => 'Phone number must be at least 10 digits',
            'max_length' => 'Phone number cannot exceed 15 digits',
            'regex_match' => 'Please enter a valid phone number'
        ],
        'email' => [
            'valid_email' => 'Please enter a valid email address'
        ],
        'emergency_phone' => [
            'required' => 'Emergency contact phone number is required',
            'min_length' => 'Emergency phone number must be at least 10 digits',
            'max_length' => 'Emergency phone number cannot exceed 15 digits',
            'regex_match' => 'Please enter a valid emergency phone number'
        ],
        'branch_id' => [
            'integer' => 'Invalid branch selected',
            'is_not_unique' => 'Selected branch does not exist'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generatePatientId'];
    protected $afterInsert    = ['logPatientCreation'];
    protected $beforeUpdate   = ['validatePatientUpdate'];
    protected $afterUpdate    = ['logPatientUpdate'];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = ['checkPatientDependencies'];
    protected $afterDelete    = ['logPatientDeletion'];

    /**
     * Generate unique patient ID
     */
    protected function generatePatientId(array $data)
    {
        if (!isset($data['data']['patient_id'])) {
            $data['data']['patient_id'] = $this->generateUniqueId();
        }
        return $data;
    }

    /**
     * Generate unique ID for patient
     */
    private function generateUniqueId(): string
    {
        do {
            $id = 'PAT' . date('Y') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while ($this->where('patient_id', $id)->first());
        
        return $id;
    }

    /**
     * Log patient creation
     */
    protected function logPatientCreation(array $data)
    {
        log_message('info', 'Patient created: ' . json_encode($data));
        return $data;
    }

    /**
     * Validate patient update
     */
    protected function validatePatientUpdate(array $data)
    {
        // Add any additional validation logic here
        return $data;
    }

    /**
     * Log patient update
     */
    protected function logPatientUpdate(array $data)
    {
        log_message('info', 'Patient updated: ' . json_encode($data));
        return $data;
    }

    /**
     * Check patient dependencies before deletion
     */
    protected function checkPatientDependencies(array $data)
    {
        $id = $data['id'];
        
        // Check for active appointments
        $appointmentModel = new \App\Models\AppointmentModel();
        $activeAppointments = $appointmentModel->where('patient_id', $id)
                                                ->where('status', 'scheduled')
                                                ->countAllResults();
        
        if ($activeAppointments > 0) {
            throw new \Exception('Cannot delete patient with active appointments');
        }
        
        // Check for active admissions
        $admissionModel = new \App\Models\AdmissionModel();
        $activeAdmissions = $admissionModel->where('patient_id', $id)
                                            ->where('status', 'admitted')
                                            ->countAllResults();
        
        if ($activeAdmissions > 0) {
            throw new \Exception('Cannot delete patient with active admissions');
        }
        
        return $data;
    }

    /**
     * Log patient deletion
     */
    protected function logPatientDeletion(array $data)
    {
        log_message('info', 'Patient deleted: ' . json_encode($data));
        return $data;
    }
}
