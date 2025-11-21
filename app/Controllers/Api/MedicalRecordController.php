<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\MedicalRecordModel;
use CodeIgniter\HTTP\ResponseInterface;

class MedicalRecordController extends BaseController
{
    protected $medicalRecordModel;

    public function __construct()
    {
        $this->medicalRecordModel = new MedicalRecordModel();
    }

    /**
     * Get all medical records
     */
    public function index()
    {
        $patientId = $this->request->getGet('patient_id');
        $doctorId = $this->request->getGet('doctor_id');
        $branchId = $this->request->getGet('branch_id');

        $builder = $this->medicalRecordModel->builder();

        if ($patientId) {
            $builder->where('patient_id', $patientId);
        }
        if ($doctorId) {
            $builder->where('doctor_id', $doctorId);
        }

        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }

        $records = $builder->orderBy('visit_date', 'DESC')->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $records
        ]);
    }

    /**
     * Get patient's medical history
     */
    public function patientHistory($patientId = null)
    {
        if (!$patientId) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Patient ID is required'
            ]);
        }

        $branchId = $this->request->getGet('branch_id');

        $records = $this->medicalRecordModel->getPatientRecords($patientId, $branchId);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $records
        ]);
    }

    /**
     * Get single medical record
     */
    public function show($id = null)
    {
        $record = $this->medicalRecordModel->find($id);

        if (!$record) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Medical record not found'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $record
        ]);
    }

    /**
     * Create new medical record
     */
    public function create()
    {
        $data = $this->request->getJSON(true);

        // Allow client to pass branch_id to associate medical record with a branch
        // If not provided, branch_id will remain null (global or unspecified).

        if ($this->medicalRecordModel->insert($data)) {
            return $this->response->setStatusCode(201)->setJSON([
                'status'  => 'success',
                'message' => 'Medical record created successfully',
                'data'    => $this->medicalRecordModel->find($this->medicalRecordModel->getInsertID())
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to create medical record',
            'errors'  => $this->medicalRecordModel->errors()
        ]);
    }

    /**
     * Update medical record
     */
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        if (!$this->medicalRecordModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Medical record not found'
            ]);
        }

        if ($this->medicalRecordModel->update($id, $data)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Medical record updated successfully',
                'data'    => $this->medicalRecordModel->find($id)
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to update medical record',
            'errors'  => $this->medicalRecordModel->errors()
        ]);
    }

    /**
     * Delete medical record
     */
    public function delete($id = null)
    {
        if (!$this->medicalRecordModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Medical record not found'
            ]);
        }

        if ($this->medicalRecordModel->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Medical record deleted successfully'
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to delete medical record'
        ]);
    }
}
