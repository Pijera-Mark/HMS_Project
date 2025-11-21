<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\PatientModel;
use App\Models\DoctorModel;

class AppointmentController extends BaseController
{
    protected AppointmentModel $appointmentModel;
    protected PatientModel $patientModel;
    protected DoctorModel $doctorModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->patientModel     = new PatientModel();
        $this->doctorModel      = new DoctorModel();
    }

    public function index()
    {
        $user = session()->get('user');
        $branchId = $user['branch_id'] ?? null;
        $isGlobal = $user && (empty($branchId) || $user['role'] === 'admin');

        $date   = $this->request->getGet('date');
        $status = $this->request->getGet('status');

        $builder = $this->appointmentModel->builder();
        $builder->select('appointments.*, patients.first_name AS patient_first_name, patients.last_name AS patient_last_name, doctors.first_name AS doctor_first_name, doctors.last_name AS doctor_last_name');
        $builder->join('patients', 'patients.patient_id = appointments.patient_id', 'left');
        $builder->join('doctors', 'doctors.doctor_id = appointments.doctor_id', 'left');

        if (! $isGlobal && $branchId) {
            $builder->where('appointments.branch_id', $branchId);
        }

        if ($date) {
            $builder->where('DATE(appointments.scheduled_at)', $date);
        }

        if ($status) {
            $builder->where('appointments.status', $status);
        }

        $appointments = $builder->orderBy('appointments.scheduled_at', 'DESC')->get()->getResultArray();

        return view('appointments/index', [
            'appointments' => $appointments,
            'filter_date'  => $date,
            'filter_status'=> $status,
        ]);
    }

    public function show($id = null)
    {
        if ($id === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Appointment not found');
        }

        $user = session()->get('user');
        $branchId = $user['branch_id'] ?? null;
        $isGlobal = $user && (empty($branchId) || $user['role'] === 'admin');

        $builder = $this->appointmentModel->builder();
        $builder->select('appointments.*, '
            . 'patients.first_name AS patient_first_name, patients.last_name AS patient_last_name, '
            . 'doctors.first_name AS doctor_first_name, doctors.last_name AS doctor_last_name');
        $builder->join('patients', 'patients.patient_id = appointments.patient_id', 'left');
        $builder->join('doctors', 'doctors.doctor_id = appointments.doctor_id', 'left');
        if (! $isGlobal && $branchId) {
            $builder->where('appointments.branch_id', $branchId);
        }
        $builder->where('appointments.id', $id);

        $appointment = $builder->get()->getRowArray();

        if (! $appointment) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Appointment not found');
        }

        return view('appointments/show', [
            'appointment' => $appointment,
        ]);
    }

    public function new()
    {
        $user = session()->get('user');
        $branchId = $user['branch_id'] ?? null;
        $isGlobal = $user && (empty($branchId) || $user['role'] === 'admin');

        $patientBuilder = $this->patientModel->orderBy('first_name');
        if (! $isGlobal && $branchId) {
            $patientBuilder = $patientBuilder->where('branch_id', $branchId);
        }
        $patients = $patientBuilder->findAll();

        $doctorBuilder = $this->doctorModel->orderBy('first_name');
        if (! $isGlobal && $branchId) {
            $doctorBuilder = $doctorBuilder->where('branch_id', $branchId);
        }
        $doctors  = $doctorBuilder->findAll();

        return view('appointments/new', [
            'patients' => $patients,
            'doctors'  => $doctors,
        ]);
    }

    public function create()
    {
        $data = $this->request->getPost();

        $user = session()->get('user');
        if ($user && isset($user['branch_id']) && $user['branch_id']) {
            $data['branch_id'] = $user['branch_id'];
        }

        if (! empty($data['scheduled_at'])) {
            $data['scheduled_at'] = date('Y-m-d H:i:s', strtotime($data['scheduled_at']));
        }

        if (! isset($data['duration_minutes']) || $data['duration_minutes'] === '') {
            $data['duration_minutes'] = 30;
        }

        if (! isset($data['status']) || $data['status'] === '') {
            $data['status'] = 'requested';
        }

        if (! $this->appointmentModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->appointmentModel->errors());
        }

        return redirect()->to('/appointments');
    }

    public function confirm($id = null)
    {
        $this->appointmentModel->update($id, ['status' => 'confirmed']);

        return redirect()->to('/appointments');
    }

    public function cancel($id = null)
    {
        $this->appointmentModel->update($id, ['status' => 'cancelled']);

        return redirect()->to('/appointments');
    }

    public function delete($id = null)
    {
        $this->appointmentModel->delete($id);

        return redirect()->to('/appointments');
    }
}
