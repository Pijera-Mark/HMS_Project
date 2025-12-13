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
        'date_of_birth'  => 'required|valid_date',
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
            'valid_date' => 'Please enter a valid date of birth'
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
     * Override the validation method to add custom age validation
     */
    public function validate($data): bool
    {
        // First run the parent validation
        $result = parent::validate($data);
        
        // If basic validation passes, check age validation
        if ($result && isset($data['date_of_birth'])) {
            if (!$this->validate_age($data['date_of_birth'])) {
                $this->validation->setError('date_of_birth', 'Patient must be between 0 and 150 years old and date cannot be in the future');
                $result = false;
            }
        }
        
        return $result;
    }

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

    /**
     * Custom validation method for age range (0-150 years) and future date check
     */
    public function validate_age(string $value): bool
    {
        try {
            $dob = new \DateTime($value);
            $today = new \DateTime();
            $age = $dob->diff($today)->y;
            
            if ($age < 0 || $age > 150) {
                return false;
            }
            
            // Check if date is not in the future
            if ($dob > $today) {
                return false;
            }
            
            return true;
            
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get patient demographics
     */
    public function getDemographics(array $filters = []): array
    {
        try {
            $builder = $this->builder()
                ->select('
                    COUNT(*) as total_patients,
                    AVG(YEAR(CURRENT_DATE) - YEAR(date_of_birth)) as average_age,
                    SUM(CASE WHEN gender = "male" THEN 1 ELSE 0 END) as male_count,
                    SUM(CASE WHEN gender = "female" THEN 1 ELSE 0 END) as female_count,
                    SUM(CASE WHEN gender = "other" THEN 1 ELSE 0 END) as other_count,
                    SUM(CASE WHEN blood_type = "A+" THEN 1 ELSE 0 END) as a_positive,
                    SUM(CASE WHEN blood_type = "A-" THEN 1 ELSE 0 END) as a_negative,
                    SUM(CASE WHEN blood_type = "B+" THEN 1 ELSE 0 END) as b_positive,
                    SUM(CASE WHEN blood_type = "B-" THEN 1 ELSE 0 END) as b_negative,
                    SUM(CASE WHEN blood_type = "AB+" THEN 1 ELSE 0 END) as ab_positive,
                    SUM(CASE WHEN blood_type = "AB-" THEN 1 ELSE 0 END) as ab_negative,
                    SUM(CASE WHEN blood_type = "O+" THEN 1 ELSE 0 END) as o_positive,
                    SUM(CASE WHEN blood_type = "O-" THEN 1 ELSE 0 END) as o_negative
                ');

            if (!empty($filters['date_from'])) {
                $builder->where('DATE(created_at) >=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $builder->where('DATE(created_at) <=', $filters['date_to']);
            }

            if (!empty($filters['branch_id'])) {
                $builder->where('branch_id', $filters['branch_id']);
            }

            $result = $builder->get()->getRowArray();
            
            // Debug logging
            log_message('debug', 'Patient demographics query result: ' . json_encode($result));
            
            return [
                'total_patients' => (int)($result['total_patients'] ?? 0),
                'average_age' => round((float)($result['average_age'] ?? 0), 1),
                'gender_distribution' => [
                    'male' => (int)($result['male_count'] ?? 0),
                    'female' => (int)($result['female_count'] ?? 0),
                    'other' => (int)($result['other_count'] ?? 0)
                ],
                'blood_type_distribution' => [
                    'A+' => (int)($result['a_positive'] ?? 0),
                    'A-' => (int)($result['a_negative'] ?? 0),
                    'B+' => (int)($result['b_positive'] ?? 0),
                    'B-' => (int)($result['b_negative'] ?? 0),
                    'AB+' => (int)($result['ab_positive'] ?? 0),
                    'AB-' => (int)($result['ab_negative'] ?? 0),
                    'O+' => (int)($result['o_positive'] ?? 0),
                    'O-' => (int)($result['o_negative'] ?? 0)
                ]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error in getDemographics: ' . $e->getMessage());
            return [
                'total_patients' => 0,
                'average_age' => 0,
                'gender_distribution' => [
                    'male' => 0,
                    'female' => 0,
                    'other' => 0
                ],
                'blood_type_distribution' => [
                    'A+' => 0, 'A-' => 0, 'B+' => 0, 'B-' => 0,
                    'AB+' => 0, 'AB-' => 0, 'O+' => 0, 'O-' => 0
                ]
            ];
        }
    }

    /**
     * Get new patients by period
     */
    public function getNewPatientsByPeriod(array $filters = []): array
    {
        $builder = $this->builder()
            ->select('
                DATE(created_at) as date,
                COUNT(*) as new_patients
            ')
            ->groupBy('DATE(created_at)')
            ->orderBy('date', 'DESC');

        if (!empty($filters['date_from'])) {
            $builder->where('DATE(created_at) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('DATE(created_at) <=', $filters['date_to']);
        }

        if (!empty($filters['branch_id'])) {
            $builder->where('branch_id', $filters['branch_id']);
        }

        $results = $builder->get()->getResultArray();
        
        $formattedResults = [];
        foreach ($results as $result) {
            $formattedResults[] = [
                'date' => $result['date'] ?? date('Y-m-d'),
                'new_patients' => (int)($result['new_patients'] ?? 0)
            ];
        }
        
        return $formattedResults;
    }

    /**
     * Get patient statistics
     */
    public function getPatientStatistics(array $filters = []): array
    {
        $builder = $this->builder()
            ->select('
                COUNT(*) as total_patients,
                COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_new,
                COUNT(CASE WHEN DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) THEN 1 END) as yesterday_new,
                COUNT(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as this_week,
                COUNT(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 END) as this_month,
                COUNT(CASE WHEN status = "active" THEN 1 END) as active_patients,
                COUNT(CASE WHEN status = "inactive" THEN 1 END) as inactive_patients
            ');

        if (!empty($filters['date_from'])) {
            $builder->where('DATE(created_at) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('DATE(created_at) <=', $filters['date_to']);
        }

        if (!empty($filters['branch_id'])) {
            $builder->where('branch_id', $filters['branch_id']);
        }

        $result = $builder->get()->getRowArray();
        
        return [
            'total_patients' => (int)($result['total_patients'] ?? 0),
            'today_new' => (int)($result['today_new'] ?? 0),
            'yesterday_new' => (int)($result['yesterday_new'] ?? 0),
            'this_week' => (int)($result['this_week'] ?? 0),
            'this_month' => (int)($result['this_month'] ?? 0),
            'active_patients' => (int)($result['active_patients'] ?? 0),
            'inactive_patients' => (int)($result['inactive_patients'] ?? 0),
            'growth_rate' => ($result['yesterday_new'] ?? 0) > 0 ? 
                ((($result['today_new'] ?? 0) - ($result['yesterday_new'] ?? 0)) / ($result['yesterday_new'] ?? 1)) * 100 : 0
        ];
    }
}
