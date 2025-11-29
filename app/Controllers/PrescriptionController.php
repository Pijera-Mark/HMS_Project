<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PrescriptionModel;
use App\Models\PatientModel;
use App\Models\DoctorModel;
use App\Models\MedicineModel;
use App\Services\ValidationService;

/**
 * Prescription Management Controller
 * Manages patient prescriptions and medications
 */
class PrescriptionController extends BaseController
{
    protected PrescriptionModel $prescriptionModel;
    protected PatientModel $patientModel;
    protected DoctorModel $doctorModel;
    protected MedicineModel $medicineModel;
    protected ValidationService $validationService;

    public function __construct()
    {
        $this->prescriptionModel = new PrescriptionModel();
        $this->patientModel = new PatientModel();
        $this->doctorModel = new DoctorModel();
        $this->medicineModel = new MedicineModel();
        $this->validationService = new ValidationService();
    }

    /**
     * Display prescriptions list
     */
    public function index()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist'])) {
            return $redirect;
        }

        $filters = [
            'patient_id' => $this->request->getGet('patient_id'),
            'doctor_id' => $this->request->getGet('doctor_id'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'search' => $this->request->getGet('search')
        ];

        $prescriptions = $this->prescriptionModel->getPrescriptionsWithFilters($filters);
        $patients = $this->patientModel->findAll();
        $doctors = $this->doctorModel->findAll();

        return view('prescriptions/index', [
            'prescriptions' => $prescriptions,
            'patients' => $patients,
            'doctors' => $doctors,
            'filters' => $filters
        ]);
    }

    /**
     * Display prescription details
     */
    public function show($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist'])) {
            return $redirect;
        }

        $prescription = $this->prescriptionModel->getPrescriptionWithDetails($id);
        
        if (!$prescription) {
            return redirect()->to('/prescriptions')->with('error', 'Prescription not found');
        }

        return view('prescriptions/show', [
            'prescription' => $prescription
        ]);
    }

    /**
     * Display create prescription form
     */
    public function create()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor'])) {
            return $redirect;
        }

        $patients = $this->patientModel->findAll();
        $doctors = $this->doctorModel->findAll();
        $medicines = $this->medicineModel->findAll();

        return view('prescriptions/create', [
            'patients' => $patients,
            'doctors' => $doctors,
            'medicines' => $medicines
        ]);
    }

    /**
     * Store new prescription
     */
    public function store()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        $validation = $this->validationService->validateAndSanitize($data, 'prescription');
        
        if (!$validation['success']) {
            return redirect()->back()->withInput()->with('errors', $validation['errors']);
        }

        $sanitizedData = $validation['data'];

        if ($this->prescriptionModel->createPrescription($sanitizedData)) {
            return redirect()->to('/prescriptions')->with('success', 'Prescription created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create prescription');
    }

    /**
     * Display edit prescription form
     */
    public function edit($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor'])) {
            return $redirect;
        }

        $prescription = $this->prescriptionModel->find($id);
        
        if (!$prescription) {
            return redirect()->to('/prescriptions')->with('error', 'Prescription not found');
        }

        $patients = $this->patientModel->findAll();
        $doctors = $this->doctorModel->findAll();
        $medicines = $this->medicineModel->findAll();

        return view('prescriptions/edit', [
            'prescription' => $prescription,
            'patients' => $patients,
            'doctors' => $doctors,
            'medicines' => $medicines
        ]);
    }

    /**
     * Update prescription
     */
    public function update($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        $validation = $this->validationService->validateAndSanitize($data, 'prescription');
        
        if (!$validation['success']) {
            return redirect()->back()->withInput()->with('errors', $validation['errors']);
        }

        $sanitizedData = $validation['data'];

        if ($this->prescriptionModel->updatePrescription($id, $sanitizedData)) {
            return redirect()->to('/prescriptions')->with('success', 'Prescription updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update prescription');
    }

    /**
     * Delete prescription
     */
    public function delete($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor'])) {
            return $redirect;
        }

        $prescription = $this->prescriptionModel->find($id);
        
        if (!$prescription) {
            return redirect()->to('/prescriptions')->with('error', 'Prescription not found');
        }

        if ($this->prescriptionModel->delete($id)) {
            return redirect()->to('/prescriptions')->with('success', 'Prescription deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete prescription');
    }

    /**
     * Update prescription status
     */
    public function updateStatus($id)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist'])) {
            return $redirect;
        }

        $status = $this->request->getPost('status');
        
        if (!in_array($status, ['pending', 'dispensed', 'completed', 'cancelled'])) {
            return redirect()->back()->with('error', 'Invalid status');
        }

        if ($this->prescriptionModel->update($id, ['status' => $status])) {
            return redirect()->back()->with('success', 'Prescription status updated');
        }

        return redirect()->back()->with('error', 'Failed to update prescription status');
    }

    /**
     * Get patient prescriptions
     */
    public function patientPrescriptions($patientId)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist'])) {
            return $redirect;
        }

        $patient = $this->patientModel->find($patientId);
        $prescriptions = $this->prescriptionModel->getPatientPrescriptions($patientId);

        return view('prescriptions/patient-prescriptions', [
            'patient' => $patient,
            'prescriptions' => $prescriptions
        ]);
    }
}
