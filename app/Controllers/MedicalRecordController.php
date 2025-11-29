<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MedicalRecordModel;
use App\Models\PatientModel;
use App\Models\DoctorModel;
use App\Services\ValidationService;

/**
 * Medical Records Controller
 * Manages patient medical records and history
 */
class MedicalRecordController extends BaseController
{
    protected MedicalRecordModel $medicalRecordModel;
    protected PatientModel $patientModel;
    protected DoctorModel $doctorModel;
    protected ValidationService $validationService;

    public function __construct()
    {
        $this->medicalRecordModel = new MedicalRecordModel();
        $this->patientModel = new PatientModel();
        $this->doctorModel = new DoctorModel();
        $this->validationService = new ValidationService();
    }

    /**
     * Display medical records list
     */
    public function index()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist'])) {
            return $redirect;
        }

        $filters = [
            'patient_id' => $this->request->getGet('patient_id'),
            'doctor_id' => $this->request->getGet('doctor_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'search' => $this->request->getGet('search')
        ];

        $records = $this->medicalRecordModel->getRecordsWithFilters($filters);
        $patients = $this->patientModel->findAll();
        $doctors = $this->doctorModel->findAll();

        return view('medical-records/index', [
            'records' => $records,
            'patients' => $patients,
            'doctors' => $doctors,
            'filters' => $filters
        ]);
    }

    /**
     * Display medical record details
     */
    public function show($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist'])) {
            return $redirect;
        }

        $record = $this->medicalRecordModel->getRecordWithDetails($id);
        
        if (!$record) {
            return redirect()->to('/medical-records')->with('error', 'Medical record not found');
        }

        return view('medical-records/show', [
            'record' => $record
        ]);
    }

    /**
     * Display create medical record form
     */
    public function create()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor'])) {
            return $redirect;
        }

        $patients = $this->patientModel->findAll();
        $doctors = $this->doctorModel->findAll();

        return view('medical-records/create', [
            'patients' => $patients,
            'doctors' => $doctors
        ]);
    }

    /**
     * Store new medical record
     */
    public function store()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        $validation = $this->validationService->validateAndSanitize($data, 'medical_record');
        
        if (!$validation['success']) {
            return redirect()->back()->withInput()->with('errors', $validation['errors']);
        }

        $sanitizedData = $validation['data'];

        if ($this->medicalRecordModel->createRecord($sanitizedData)) {
            return redirect()->to('/medical-records')->with('success', 'Medical record created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create medical record');
    }

    /**
     * Display edit medical record form
     */
    public function edit($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor'])) {
            return $redirect;
        }

        $record = $this->medicalRecordModel->find($id);
        
        if (!$record) {
            return redirect()->to('/medical-records')->with('error', 'Medical record not found');
        }

        $patients = $this->patientModel->findAll();
        $doctors = $this->doctorModel->findAll();

        return view('medical-records/edit', [
            'record' => $record,
            'patients' => $patients,
            'doctors' => $doctors
        ]);
    }

    /**
     * Update medical record
     */
    public function update($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        $validation = $this->validationService->validateAndSanitize($data, 'medical_record');
        
        if (!$validation['success']) {
            return redirect()->back()->withInput()->with('errors', $validation['errors']);
        }

        $sanitizedData = $validation['data'];

        if ($this->medicalRecordModel->updateRecord($id, $sanitizedData)) {
            return redirect()->to('/medical-records')->with('success', 'Medical record updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update medical record');
    }

    /**
     * Delete medical record
     */
    public function delete($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor'])) {
            return $redirect;
        }

        $record = $this->medicalRecordModel->find($id);
        
        if (!$record) {
            return redirect()->to('/medical-records')->with('error', 'Medical record not found');
        }

        if ($this->medicalRecordModel->delete($id)) {
            return redirect()->to('/medical-records')->with('success', 'Medical record deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete medical record');
    }

    /**
     * Get patient medical history
     */
    public function patientHistory($patientId)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist'])) {
            return $redirect;
        }

        $patient = $this->patientModel->find($patientId);
        $records = $this->medicalRecordModel->getPatientRecords($patientId);

        return view('medical-records/patient-history', [
            'patient' => $patient,
            'records' => $records
        ]);
    }
}
