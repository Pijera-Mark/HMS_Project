<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DoctorModel;
use App\Models\BranchModel;
use App\Services\ValidationService;

/**
 * Doctor Management Controller (Web Interface)
 * Handles doctor-specific operations separate from general user management
 */
class DoctorController extends BaseController
{
    protected DoctorModel $doctorModel;
    protected BranchModel $branchModel;
    protected ValidationService $validationService;

    public function __construct()
    {
        $this->doctorModel = new DoctorModel();
        $this->branchModel = new BranchModel();
        $this->validationService = new ValidationService();
    }

    /**
     * Display doctors list
     */
    public function index()
    {
        // Check permissions
        if ($redirect = $this->enforceRoles(['admin', 'receptionist'])) {
            return $redirect;
        }

        $filters = [
            'specialization' => $this->request->getGet('specialization'),
            'status' => $this->request->getGet('status'),
            'search' => $this->request->getGet('search'),
            'branch_id' => $this->request->getGet('branch_id')
        ];

        $doctors = $this->doctorModel->getDoctorsWithFilters($filters);
        $branches = $this->branchModel->findAll();
        $specializations = $this->doctorModel->getSpecializations();

        return view('doctors/index', [
            'doctors' => $doctors,
            'branches' => $branches,
            'specializations' => $specializations,
            'filters' => $filters
        ]);
    }

    /**
     * Display doctor details
     */
    public function show($id)
    {
        // Check permissions
        if ($redirect = $this->enforceRoles(['admin', 'receptionist', 'doctor'])) {
            return $redirect;
        }

        $doctor = $this->doctorModel->find($id);
        
        if (!$doctor) {
            return redirect()->to('/doctors')->with('error', 'Doctor not found');
        }

        // Get doctor's schedule and appointments
        $schedule = $this->doctorModel->getDoctorSchedule($id);
        $upcomingAppointments = $this->doctorModel->getUpcomingAppointments($id);

        return view('doctors/show', [
            'doctor' => $doctor,
            'schedule' => $schedule,
            'upcoming_appointments' => $upcomingAppointments
        ]);
    }

    /**
     * Display create doctor form
     */
    public function create()
    {
        // Check permissions
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $branches = $this->branchModel->findAll();
        $specializations = $this->doctorModel->getSpecializations();

        return view('doctors/create', [
            'branches' => $branches,
            'specializations' => $specializations
        ]);
    }

    /**
     * Store new doctor
     */
    public function store()
    {
        // Check permissions
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        // Validate doctor data
        $validation = $this->validationService->validateAndSanitize($data, 'doctor');
        
        if (!$validation['success']) {
            return redirect()->back()->withInput()->with('errors', $validation['errors']);
        }

        $sanitizedData = $validation['data'];

        // Create doctor
        if ($this->doctorModel->save($sanitizedData)) {
            // Log activity
            $this->logActivity('doctor_created', [
                'entity_type' => 'doctor',
                'details' => 'New doctor created: ' . $sanitizedData['first_name'] . ' ' . $sanitizedData['last_name']
            ]);

            return redirect()->to('/doctors')->with('success', 'Doctor created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create doctor');
    }

    /**
     * Display edit doctor form
     */
    public function edit($id)
    {
        // Check permissions
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $doctor = $this->doctorModel->find($id);
        
        if (!$doctor) {
            return redirect()->to('/doctors')->with('error', 'Doctor not found');
        }

        $branches = $this->branchModel->findAll();
        $specializations = $this->doctorModel->getSpecializations();

        return view('doctors/edit', [
            'doctor' => $doctor,
            'branches' => $branches,
            'specializations' => $specializations
        ]);
    }

    /**
     * Update doctor
     */
    public function update($id)
    {
        // Check permissions
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        // Validate doctor data
        $validation = $this->validationService->validateAndSanitize($data, 'doctor');
        
        if (!$validation['success']) {
            return redirect()->back()->withInput()->with('errors', $validation['errors']);
        }

        $sanitizedData = $validation['data'];

        // Update doctor
        if ($this->doctorModel->updateDoctor($id, $sanitizedData)) {
            // Log activity
            $this->logActivity('doctor_updated', [
                'entity_type' => 'doctor',
                'entity_id' => $id,
                'details' => 'Doctor updated: ' . $sanitizedData['first_name'] . ' ' . $sanitizedData['last_name']
            ]);

            return redirect()->to('/doctors')->with('success', 'Doctor updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update doctor');
    }

    /**
     * Delete doctor
     */
    public function delete($id)
    {
        // Check permissions
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $doctor = $this->doctorModel->find($id);
        
        if (!$doctor) {
            return redirect()->to('/doctors')->with('error', 'Doctor not found');
        }

        // Check if doctor has active appointments
        if ($this->doctorModel->hasActiveAppointments($id)) {
            return redirect()->to('/doctors')->with('error', 'Cannot delete doctor with active appointments');
        }

        // Delete doctor
        if ($this->doctorModel->deleteDoctor($id)) {
            // Log activity
            $this->logActivity('doctor_deleted', [
                'entity_type' => 'doctor',
                'entity_id' => $id,
                'details' => 'Doctor deleted: ' . $doctor['first_name'] . ' ' . $doctor['last_name']
            ]);

            return redirect()->to('/doctors')->with('success', 'Doctor deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete doctor');
    }

    /**
     * Display doctor schedule
     */
    public function schedule($id)
    {
        // Check permissions
        if ($redirect = $this->enforceRoles(['admin', 'receptionist', 'doctor'])) {
            return $redirect;
        }

        $doctor = $this->doctorModel->find($id);
        
        if (!$doctor) {
            return redirect()->to('/doctors')->with('error', 'Doctor not found');
        }

        $schedule = $this->doctorModel->getDoctorSchedule($id);
        $appointments = $this->doctorModel->getDoctorAppointments($id);

        return view('doctors/schedule', [
            'doctor' => $doctor,
            'schedule' => $schedule,
            'appointments' => $appointments
        ]);
    }

    /**
     * Get available doctors for appointment booking
     */
    public function available()
    {
        $date = $this->request->getGet('date');
        $specialization = $this->request->getGet('specialization');
        
        $availableDoctors = $this->doctorModel->getAvailableDoctors($date, $specialization);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $availableDoctors
        ]);
    }

    /**
     * Change doctor status
     */
    public function changeStatus($id)
    {
        // Check permissions
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $status = $this->request->getPost('status');
        
        if (!in_array($status, ['active', 'inactive', 'on_leave'])) {
            return redirect()->back()->with('error', 'Invalid status');
        }

        $doctor = $this->doctorModel->find($id);
        
        if (!$doctor) {
            return redirect()->to('/doctors')->with('error', 'Doctor not found');
        }

        if ($this->doctorModel->update($id, ['status' => $status])) {
            // Log activity
            $this->logActivity('doctor_status_changed', [
                'entity_type' => 'doctor',
                'entity_id' => $id,
                'details' => 'Doctor status changed to: ' . $status
            ]);

            return redirect()->back()->with('success', 'Doctor status updated');
        }

        return redirect()->back()->with('error', 'Failed to update doctor status');
    }
}
