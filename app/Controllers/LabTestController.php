<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LabTestModel;
use App\Models\PatientModel;
use App\Models\DoctorModel;
use App\Services\ValidationService;

/**
 * Lab Tests Controller
 * Manages laboratory tests and results
 */
class LabTestController extends BaseController
{
    protected LabTestModel $labTestModel;
    protected PatientModel $patientModel;
    protected DoctorModel $doctorModel;
    protected ValidationService $validationService;

    public function __construct()
    {
        $this->labTestModel = new LabTestModel();
        $this->patientModel = new PatientModel();
        $this->doctorModel = new DoctorModel();
        $this->validationService = new ValidationService();
    }

    /**
     * Display lab tests list
     */
    public function index()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist'])) {
            return $redirect;
        }

        $filters = [
            'patient_id' => $this->request->getGet('patient_id'),
            'doctor_id' => $this->request->getGet('doctor_id'),
            'test_type' => $this->request->getGet('test_type'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'search' => $this->request->getGet('search')
        ];

        $tests = $this->labTestModel->getTestsWithFilters($filters);
        $patients = $this->patientModel->findAll();
        $doctors = $this->doctorModel->findAll();
        $testTypes = $this->labTestModel->getTestTypes();

        return view('lab-tests/index', [
            'tests' => $tests,
            'patients' => $patients,
            'doctors' => $doctors,
            'testTypes' => $testTypes,
            'filters' => $filters
        ]);
    }

    /**
     * Display lab test details
     */
    public function show($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist'])) {
            return $redirect;
        }

        $test = $this->labTestModel->getTestWithDetails($id);
        
        if (!$test) {
            return redirect()->to('/lab-tests')->with('error', 'Lab test not found');
        }

        return view('lab-tests/show', [
            'test' => $test
        ]);
    }

    /**
     * Display create lab test form
     */
    public function create()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist'])) {
            return $redirect;
        }

        $patients = $this->patientModel->findAll();
        $doctors = $this->doctorModel->findAll();
        $testTypes = $this->labTestModel->getTestTypes();

        return view('lab-tests/create', [
            'patients' => $patients,
            'doctors' => $doctors,
            'testTypes' => $testTypes
        ]);
    }

    /**
     * Store new lab test
     */
    public function store()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        $validation = $this->validationService->validateAndSanitize($data, 'lab_test');
        
        if (!$validation['success']) {
            return redirect()->back()->withInput()->with('errors', $validation['errors']);
        }

        $sanitizedData = $validation['data'];

        if ($this->labTestModel->createTest($sanitizedData)) {
            return redirect()->to('/lab-tests')->with('success', 'Lab test created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create lab test');
    }

    /**
     * Display edit lab test form
     */
    public function edit($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor'])) {
            return $redirect;
        }

        $test = $this->labTestModel->find($id);
        
        if (!$test) {
            return redirect()->to('/lab-tests')->with('error', 'Lab test not found');
        }

        $patients = $this->patientModel->findAll();
        $doctors = $this->doctorModel->findAll();
        $testTypes = $this->labTestModel->getTestTypes();

        return view('lab-tests/edit', [
            'test' => $test,
            'patients' => $patients,
            'doctors' => $doctors,
            'testTypes' => $testTypes
        ]);
    }

    /**
     * Update lab test
     */
    public function update($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        $validation = $this->validationService->validateAndSanitize($data, 'lab_test');
        
        if (!$validation['success']) {
            return redirect()->back()->withInput()->with('errors', $validation['errors']);
        }

        $sanitizedData = $validation['data'];

        if ($this->labTestModel->updateTest($id, $sanitizedData)) {
            return redirect()->to('/lab-tests')->with('success', 'Lab test updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update lab test');
    }

    /**
     * Delete lab test
     */
    public function delete($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor'])) {
            return $redirect;
        }

        $test = $this->labTestModel->find($id);
        
        if (!$test) {
            return redirect()->to('/lab-tests')->with('error', 'Lab test not found');
        }

        if ($this->labTestModel->delete($id)) {
            return redirect()->to('/lab-tests')->with('success', 'Lab test deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete lab test');
    }

    /**
     * Update lab test status
     */
    public function updateStatus($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist'])) {
            return $redirect;
        }

        $status = $this->request->getPost('status');
        
        if (!in_array($status, ['ordered', 'sample_collected', 'in_progress', 'completed', 'cancelled'])) {
            return redirect()->back()->with('error', 'Invalid status');
        }

        if ($this->labTestModel->update($id, ['status' => $status])) {
            return redirect()->back()->with('success', 'Lab test status updated');
        }

        return redirect()->back()->with('error', 'Failed to update lab test status');
    }

    /**
     * Upload lab test results
     */
    public function uploadResults($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor'])) {
            return $redirect;
        }

        $test = $this->labTestModel->find($id);
        
        if (!$test) {
            return redirect()->to('/lab-tests')->with('error', 'Lab test not found');
        }

        $file = $this->request->getFile('result_file');
        
        if ($file && $file->isValid()) {
            // Handle file upload
            $newName = 'lab_result_' . $id . '_' . time() . '.' . $file->getExtension();
            $uploadPath = WRITEPATH . 'uploads/lab_results/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            if ($file->move($uploadPath, $newName)) {
                $this->labTestModel->update($id, [
                    'result_file' => 'uploads/lab_results/' . $newName,
                    'status' => 'completed',
                    'completed_at' => date('Y-m-d H:i:s')
                ]);
                
                return redirect()->to('/lab-tests')->with('success', 'Lab test results uploaded successfully');
            }
        }

        return redirect()->back()->with('error', 'Failed to upload lab test results');
    }

    /**
     * Get patient lab tests
     */
    public function patientTests($patientId)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist'])) {
            return $redirect;
        }

        $patient = $this->patientModel->find($patientId);
        $tests = $this->labTestModel->getPatientTests($patientId);

        return view('lab-tests/patient-tests', [
            'patient' => $patient,
            'tests' => $tests
        ]);
    }
}
