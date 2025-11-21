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
        $search = $this->request->getGet('search');

        if ($search) {
            $patients = $this->patientModel
                ->like('first_name', $search)
                ->orLike('last_name', $search)
                ->orLike('phone', $search)
                ->orLike('email', $search)
                ->findAll();
        } else {
            $patients = $this->patientModel->findAll();
        }

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
        return view('patients/new');
    }

    public function create()
    {
        $data = $this->request->getPost();

        if (! $this->patientModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->patientModel->errors());
        }

        return redirect()->to('/patients');
    }

    public function edit($id = null)
    {
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
        $data = $this->request->getPost();

        if (! $this->patientModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->patientModel->errors());
        }

        return redirect()->to('/patients');
    }

    public function delete($id = null)
    {
        $this->patientModel->delete($id);

        return redirect()->to('/patients');
    }
}
