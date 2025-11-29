<?php

namespace App\Controllers\Api;

use App\Controllers\EnhancedBaseController;
use App\Models\PatientModel;
use CodeIgniter\HTTP\ResponseInterface;

class PatientController extends EnhancedBaseController
{
    protected $patientModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
    }

    /**
     * Get all patients with optional search
     */
    public function index()
    {
        // Require authentication
        if (!$this->requireAuth()) return;
        
        $search = $this->request->getGet('search');
        $cacheKey = 'patients_list_' . ($search ?? 'all');
        
        // Try to get from cache first
        $cached = $this->getCachedData($cacheKey);
        if ($cached !== null) {
            return $this->sendSuccess($cached, 'Patients retrieved from cache');
        }
        
        if ($search) {
            $patients = $this->patientModel
                ->like('first_name', $search)
                ->orLike('last_name', $search)
                ->orLike('phone', $search)
                ->orLike('email', $search)
                ->findAll();
        } else {
            $patients = $this->patientModel->findAll();
        }
        
        // Cache the results
        $this->cacheData($cacheKey, $patients, 300); // 5 minutes

        return $this->sendSuccess($patients);
    }

    /**
     * Get single patient
     */
    public function show($id = null)
    {
        // Require authentication
        if (!$this->requireAuth()) return;
        
        if (!$id) {
            return $this->sendValidationError(['id' => 'Patient ID is required']);
        }
        
        $cacheKey = 'patient_' . $id;
        
        // Try cache first
        $cached = $this->getCachedData($cacheKey);
        if ($cached !== null) {
            return $this->sendSuccess($cached, 'Patient retrieved from cache');
        }
        
        $patient = $this->patientModel->find($id);

        if (!$patient) {
            return $this->sendNotFound('Patient not found');
        }
        
        // Cache for 10 minutes
        $this->cacheData($cacheKey, $patient, 600);

        return $this->sendSuccess($patient);
    }

    /**
     * Create new patient
     */
    public function create()
    {
        // Require authentication and proper role
        if (!$this->requireRole(['admin', 'doctor', 'receptionist'])) return;
        
        // Validate input
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => 'required|valid_email|is_unique[patients.email]',
            'phone' => 'required|min_length[10]|max_length[15]',
            'date_of_birth' => 'required|valid_date',
            'gender' => 'required|in_list[male,female,other]'
        ];
        
        if (!$this->validateRequest($rules)) return;
        
        $data = $this->sanitizeInput($this->getJsonData());

        if ($this->patientModel->insert($data)) {
            $patientId = $this->patientModel->getInsertID();
            $patient = $this->patientModel->find($patientId);
            
            // Clear relevant caches
            $this->clearCache('patients_list_all');
            
            // Log activity
            $this->logActivity('patient_created', [
                'entity_type' => 'patient',
                'entity_id' => $patientId,
                'details' => 'New patient created: ' . $data['first_name'] . ' ' . $data['last_name']
            ]);
            
            return $this->sendSuccess($patient, 'Patient created successfully', 201);
        }

        return $this->sendValidationError($this->patientModel->errors());
    }

    /**
     * Update patient
     */
    public function update($id = null)
    {
        // Require authentication and proper role
        if (!$this->requireRole(['admin', 'doctor', 'receptionist'])) return;
        
        if (!$id) {
            return $this->sendValidationError(['id' => 'Patient ID is required']);
        }
        
        // Validate input
        $rules = [
            'first_name' => 'if_exists|min_length[2]|max_length[50]',
            'last_name' => 'if_exists|min_length[2]|max_length[50]',
            'email' => 'if_exists|valid_email|is_unique[patients.email,id,' . $id . ']',
            'phone' => 'if_exists|min_length[10]|max_length[15]',
            'date_of_birth' => 'if_exists|valid_date',
            'gender' => 'if_exists|in_list[male,female,other]'
        ];
        
        if (!$this->validateRequest($rules)) return;
        
        $data = $this->sanitizeInput($this->getJsonData());

        if (!$this->patientModel->find($id)) {
            return $this->sendNotFound('Patient not found');
        }

        if ($this->patientModel->update($id, $data)) {
            $patient = $this->patientModel->find($id);
            
            // Clear caches
            $this->clearCache('patient_' . $id);
            $this->clearCache('patients_list_all');
            
            // Log activity
            $this->logActivity('patient_updated', [
                'entity_type' => 'patient',
                'entity_id' => $id,
                'details' => 'Patient updated: ' . $data['first_name'] ?? '' . ' ' . $data['last_name'] ?? ''
            ]);
            
            return $this->sendSuccess($patient, 'Patient updated successfully');
        }

        return $this->sendValidationError($this->patientModel->errors());
    }

    /**
     * Delete patient
     */
    public function delete($id = null)
    {
        // Require admin role for deletion
        if (!$this->requireRole(['admin'])) return;
        
        if (!$id) {
            return $this->sendValidationError(['id' => 'Patient ID is required']);
        }
        
        $patient = $this->patientModel->find($id);
        if (!$patient) {
            return $this->sendNotFound('Patient not found');
        }

        if ($this->patientModel->delete($id)) {
            // Clear caches
            $this->clearCache('patient_' . $id);
            $this->clearCache('patients_list_all');
            
            // Log activity
            $this->logActivity('patient_deleted', [
                'entity_type' => 'patient',
                'entity_id' => $id,
                'details' => 'Patient deleted: ' . $patient['first_name'] . ' ' . $patient['last_name']
            ]);
            
            return $this->sendSuccess(null, 'Patient deleted successfully');
        }

        return $this->sendServerError('Failed to delete patient');
    }
}
