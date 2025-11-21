<?php

namespace App\Controllers;

use App\Models\PatientModel;

class PatientController extends BaseController
{
    protected PatientModel $patientModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
    }

    public function index()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'nurse', 'receptionist'])) {
            return $redirect;
        }

        $user = session()->get('user');
        $branchId = $user['branch_id'] ?? null;
        $isGlobal = $user && (empty($branchId) || $user['role'] === 'admin');

        $search = $this->request->getGet('search');
        $builder = $this->patientModel;

        if (! $isGlobal && $branchId) {
            $builder = $builder->where('branch_id', $branchId);
        }

        if ($search) {
            $builder = $builder
                ->groupStart()
                ->like('first_name', $search)
                ->orLike('last_name', $search)
                ->orLike('phone', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        $patients = $builder->findAll();

        return view('patients/index', [
            'patients' => $patients,
        ]);
    }

    public function show($id = null)
    {
        $patient = $this->patientModel->find($id);

        if (! $patient) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Patient not found');
        }

        return view('patients/show', [
            'patient' => $patient,
        ]);
    }

    public function new()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'nurse', 'receptionist'])) {
            return $redirect;
        }

        return view('patients/new');
    }

    public function create()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'nurse', 'receptionist'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        $user = session()->get('user');
        if ($user && isset($user['branch_id']) && $user['branch_id']) {
            $data['branch_id'] = $user['branch_id'];
        }

        if (! $this->patientModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->patientModel->errors());
        }

        return redirect()->to('/patients');
    }

    public function edit($id = null)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'nurse', 'receptionist'])) {
            return $redirect;
        }

        $patient = $this->patientModel->find($id);

        if (! $patient) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Patient not found');
        }

        return view('patients/edit', [
            'patient' => $patient,
        ]);
    }

    public function update($id = null)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'nurse', 'receptionist'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        if (! $this->patientModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->patientModel->errors());
        }

        return redirect()->to('/patients');
    }

    public function delete($id = null)
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'nurse', 'receptionist'])) {
            return $redirect;
        }

        $this->patientModel->delete($id);

        return redirect()->to('/patients');
    }
}
