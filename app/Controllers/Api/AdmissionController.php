<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\AdmissionModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdmissionController extends BaseController
{
    protected $admissionModel;

    public function __construct()
    {
        $this->admissionModel = new AdmissionModel();
    }

    /**
     * Get all admissions with filters
     */
    public function index()
    {
        $status = $this->request->getGet('status');
        $patientId = $this->request->getGet('patient_id');
        $branchId = $this->request->getGet('branch_id');

        $builder = $this->admissionModel->builder();

        if ($status) {
            $builder->where('status', $status);
        }
        if ($patientId) {
            $builder->where('patient_id', $patientId);
        }
        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }

        $admissions = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $admissions
        ]);
    }

    /**
     * Get active admissions
     */
    public function active()
    {
        $branchId = $this->request->getGet('branch_id');

        $admissions = $this->admissionModel->getActiveAdmissions($branchId);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $admissions
        ]);
    }

    /**
     * Create new admission
     */
    public function create()
    {
        $data = $this->request->getJSON(true);

        // Allow client to pass branch_id to associate admission with a branch
        // If not provided, branch_id will remain null (global or unspecified).

        // Set default status
        if (!isset($data['status'])) {
            $data['status'] = 'admitted';
        }

        // Set admission date if not provided
        if (!isset($data['admission_date'])) {
            $data['admission_date'] = date('Y-m-d H:i:s');
        }

        if ($this->admissionModel->insert($data)) {
            return $this->response->setStatusCode(201)->setJSON([
                'status'  => 'success',
                'message' => 'Patient admitted successfully',
                'data'    => $this->admissionModel->find($this->admissionModel->getInsertID())
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to admit patient',
            'errors'  => $this->admissionModel->errors()
        ]);
    }

    /**
     * Update admission
     */
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        if (!$this->admissionModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Admission record not found'
            ]);
        }

        if ($this->admissionModel->update($id, $data)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Admission updated successfully',
                'data'    => $this->admissionModel->find($id)
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to update admission',
            'errors'  => $this->admissionModel->errors()
        ]);
    }

    /**
     * Discharge patient
     */
    public function discharge($id = null)
    {
        $data = $this->request->getJSON(true);

        if (!$this->admissionModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Admission record not found'
            ]);
        }

        $dischargeDate = $data['discharge_date'] ?? date('Y-m-d H:i:s');
        $notes = $data['notes'] ?? '';

        if ($this->admissionModel->dischargePatient($id, $dischargeDate, $notes)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Patient discharged successfully',
                'data'    => $this->admissionModel->find($id)
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to discharge patient'
        ]);
    }

    /**
     * Get admission details
     */
    public function show($id = null)
    {
        $admission = $this->admissionModel->find($id);

        if (!$admission) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Admission record not found'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $admission
        ]);
    }
}
