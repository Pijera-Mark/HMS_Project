<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\LabTestModel;
use CodeIgniter\HTTP\ResponseInterface;

class LabTestController extends BaseController
{
    protected $labTestModel;

    public function __construct()
    {
        $this->labTestModel = new LabTestModel();
    }

    /**
     * Get all lab tests
     */
    public function index()
    {
        $patientId = $this->request->getGet('patient_id');
        $doctorId = $this->request->getGet('doctor_id');
        $status = $this->request->getGet('status');
        $branchId = $this->request->getGet('branch_id');

        $builder = $this->labTestModel->builder();

        if ($patientId) {
            $builder->where('patient_id', $patientId);
        }
        if ($doctorId) {
            $builder->where('doctor_id', $doctorId);
        }
        if ($status) {
            $builder->where('status', $status);
        }
        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }

        $tests = $builder->orderBy('test_date', 'DESC')->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $tests
        ]);
    }

    /**
     * Get pending lab tests
     */
    public function pending()
    {
        $branchId = $this->request->getGet('branch_id');

        $tests = $this->labTestModel->getPendingTests($branchId);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $tests
        ]);
    }

    /**
     * Get completed lab tests
     */
    public function completed()
    {
        $branchId = $this->request->getGet('branch_id');

        $tests = $this->labTestModel->getCompletedTests($branchId);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $tests
        ]);
    }

    /**
     * Get patient's lab tests
     */
    public function patientTests($patientId = null)
    {
        if (!$patientId) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Patient ID is required'
            ]);
        }

        $branchId = $this->request->getGet('branch_id');

        $tests = $this->labTestModel->getPatientTests($patientId, $branchId);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $tests
        ]);
    }

    /**
     * Get single lab test
     */
    public function show($id = null)
    {
        $test = $this->labTestModel->find($id);

        if (!$test) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Lab test not found'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $test
        ]);
    }

    /**
     * Create new lab test
     */
    public function create()
    {
        $data = $this->request->getJSON(true);

        // Allow client to pass branch_id to associate lab test with a branch
        // If not provided, branch_id will remain null (global or unspecified).

        if ($this->labTestModel->insert($data)) {
            return $this->response->setStatusCode(201)->setJSON([
                'status'  => 'success',
                'message' => 'Lab test created successfully',
                'data'    => $this->labTestModel->find($this->labTestModel->getInsertID())
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to create lab test',
            'errors'  => $this->labTestModel->errors()
        ]);
    }

    /**
     * Update lab test
     */
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        if (!$this->labTestModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Lab test not found'
            ]);
        }

        if ($this->labTestModel->update($id, $data)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Lab test updated successfully',
                'data'    => $this->labTestModel->find($id)
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to update lab test',
            'errors'  => $this->labTestModel->errors()
        ]);
    }

    /**
     * Submit lab test results
     */
    public function submitResult($id = null)
    {
        $data = $this->request->getJSON(true);

        if (!$this->labTestModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Lab test not found'
            ]);
        }

        $result = $data['result'] ?? null;
        $resultDate = $data['result_date'] ?? date('Y-m-d');

        if ($this->labTestModel->updateTestStatus($id, 'completed', $result, $resultDate)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Lab test result submitted successfully',
                'data'    => $this->labTestModel->find($id)
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to submit lab test result'
        ]);
    }

    /**
     * Cancel lab test
     */
    public function cancel($id = null)
    {
        if (!$this->labTestModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Lab test not found'
            ]);
        }

        if ($this->labTestModel->updateTestStatus($id, 'cancelled')) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Lab test cancelled',
                'data'    => $this->labTestModel->find($id)
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to cancel lab test'
        ]);
    }

    /**
     * Delete lab test
     */
    public function delete($id = null)
    {
        if (!$this->labTestModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Lab test not found'
            ]);
        }

        if ($this->labTestModel->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Lab test deleted successfully'
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to delete lab test'
        ]);
    }
}
