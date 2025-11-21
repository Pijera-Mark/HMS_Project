<?php

namespace App\Controllers;

use App\Models\AdmissionModel;
use App\Models\PatientModel;
use App\Models\DoctorModel;
use App\Models\WardModel;

class AdmissionController extends BaseController
{
    protected AdmissionModel $admissionModel;
    protected PatientModel $patientModel;
    protected DoctorModel $doctorModel;
    protected WardModel $wardModel;

    public function __construct()
    {
        $this->admissionModel = new AdmissionModel();
        $this->patientModel   = new PatientModel();
        $this->doctorModel    = new DoctorModel();
        $this->wardModel      = new WardModel();
    }

    public function index()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'nurse', 'receptionist'])) {
            return $redirect;
        }

        $user = session()->get('user');
        $branchId = $user['branch_id'] ?? null;
        $isGlobal = $user && (empty($branchId) || $user['role'] === 'admin');

        $status = $this->request->getGet('status');

        $builder = $this->admissionModel->builder();
        $builder->select('admissions.*, '
            . 'patients.first_name AS patient_first_name, patients.last_name AS patient_last_name, '
            . 'doctors.first_name AS doctor_first_name, doctors.last_name AS doctor_last_name, '
            . 'wards.name AS ward_name');
        $builder->join('patients', 'patients.patient_id = admissions.patient_id', 'left');
        $builder->join('doctors', 'doctors.doctor_id = admissions.assigned_doctor_id', 'left');
        $builder->join('wards', 'wards.id = admissions.ward_id', 'left');

        if (! $isGlobal && $branchId) {
            $builder->where('admissions.branch_id', $branchId);
        }

        if ($status) {
            $builder->where('admissions.status', $status);
        }

        $admissions = $builder
            ->orderBy('admissions.admission_date', 'DESC')
            ->get()
            ->getResultArray();

        return view('admissions/index', [
            'admissions'     => $admissions,
            'filter_status'  => $status,
        ]);
    }

    public function show($id = null)
    {
        if ($id === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Admission not found');
        }

        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'nurse', 'receptionist'])) {
            return $redirect;
        }

        $user = session()->get('user');
        $branchId = $user['branch_id'] ?? null;
        $isGlobal = $user && (empty($branchId) || $user['role'] === 'admin');

        $builder = $this->admissionModel->builder();
        $builder->select('admissions.*, '
            . 'patients.first_name AS patient_first_name, patients.last_name AS patient_last_name, '
            . 'doctors.first_name AS doctor_first_name, doctors.last_name AS doctor_last_name, '
            . 'wards.name AS ward_name');
        $builder->join('patients', 'patients.patient_id = admissions.patient_id', 'left');
        $builder->join('doctors', 'doctors.doctor_id = admissions.assigned_doctor_id', 'left');
        $builder->join('wards', 'wards.id = admissions.ward_id', 'left');

        if (! $isGlobal && $branchId) {
            $builder->where('admissions.branch_id', $branchId);
        }

        $builder->where('admissions.id', $id);

        $admission = $builder->get()->getRowArray();

        if (! $admission) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Admission not found');
        }

        return view('admissions/show', [
            'admission' => $admission,
        ]);
    }

    public function new()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'nurse', 'receptionist'])) {
            return $redirect;
        }

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
        $doctors = $doctorBuilder->findAll();

        $wards = $this->wardModel->getAvailableWards();

        $selectedPatientId = $this->request->getGet('patient_id');

        return view('admissions/new', [
            'patients'            => $patients,
            'doctors'             => $doctors,
            'wards'               => $wards,
            'selected_patient_id' => $selectedPatientId,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'nurse', 'receptionist'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        $user = session()->get('user');
        if ($user && isset($user['id'])) {
            $data['admitted_by'] = $user['id'];
        }

        if ($user && isset($user['branch_id']) && $user['branch_id']) {
            $data['branch_id'] = $user['branch_id'];
        }

        if (! empty($data['admission_date'])) {
            $data['admission_date'] = date('Y-m-d H:i:s', strtotime($data['admission_date']));
        } else {
            $data['admission_date'] = date('Y-m-d H:i:s');
        }

        if (empty($data['status'])) {
            $data['status'] = 'admitted';
        }

        if (! $this->admissionModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->admissionModel->errors());
        }

        return redirect()->to('/admissions');
    }

    public function discharge($id = null)
    {
        if ($id === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Admission not found');
        }

        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'nurse', 'receptionist'])) {
            return $redirect;
        }

        $admission = $this->admissionModel->find($id);

        if (! $admission) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Admission not found');
        }

        $user = session()->get('user');
        $branchId = $user['branch_id'] ?? null;
        $isGlobal = $user && (empty($branchId) || $user['role'] === 'admin');

        if (! $isGlobal && $branchId && (int) ($admission['branch_id'] ?? 0) !== (int) $branchId) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Admission not found');
        }

        $data = $this->request->getPost();

        $dischargeDate = ! empty($data['discharge_date'])
            ? date('Y-m-d H:i:s', strtotime($data['discharge_date']))
            : date('Y-m-d H:i:s');
        $notes = $data['notes'] ?? '';

        if (! $this->admissionModel->dischargePatient($id, $dischargeDate, $notes)) {
            return redirect()->back()->withInput()->with('errors', $this->admissionModel->errors());
        }

        return redirect()->to('/admissions');
    }
}
