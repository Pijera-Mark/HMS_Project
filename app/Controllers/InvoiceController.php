<?php

namespace App\Controllers;

use App\Models\InvoiceModel;
use App\Models\PatientModel;
use App\Models\AdmissionModel;

class InvoiceController extends BaseController
{
    protected InvoiceModel $invoiceModel;
    protected PatientModel $patientModel;
    protected AdmissionModel $admissionModel;

    public function __construct()
    {
        $this->invoiceModel   = new InvoiceModel();
        $this->patientModel   = new PatientModel();
        $this->admissionModel = new AdmissionModel();
    }

    public function index()
    {
        if ($redirect = $this->enforceRoles(['admin', 'receptionist', 'accountant', 'pharmacist'])) {
            return $redirect;
        }

        $user = session()->get('user');
        $branchId = $user['branch_id'] ?? null;
        $isGlobal = $user && (empty($branchId) || $user['role'] === 'admin');

        $status = $this->request->getGet('status');

        $builder = $this->invoiceModel->builder();
        $builder->select('invoices.*, patients.first_name AS patient_first_name, patients.last_name AS patient_last_name');
        $builder->join('patients', 'patients.patient_id = invoices.patient_id', 'left');

        if (! $isGlobal && $branchId) {
            $builder->where('invoices.branch_id', $branchId);
        }

        if ($status) {
            $builder->where('invoices.status', $status);
        }

        $invoices = $builder
            ->orderBy('invoices.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('invoices/index', [
            'invoices'     => $invoices,
            'filter_status'=> $status,
        ]);
    }

    public function new()
    {
        if ($redirect = $this->enforceRoles(['admin', 'receptionist', 'accountant', 'pharmacist'])) {
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

        return view('invoices/new', [
            'patients' => $patients,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->enforceRoles(['admin', 'receptionist', 'accountant', 'pharmacist'])) {
            return $redirect;
        }

        $data = $this->request->getPost();

        $user = session()->get('user');
        if ($user && isset($user['id'])) {
            $data['created_by'] = $user['id'];
        }
        if ($user && ! empty($user['branch_id'])) {
            $data['branch_id'] = $user['branch_id'];
        }

        if (empty($data['invoice_number'])) {
            $data['invoice_number'] = $this->invoiceModel->generateInvoiceNumber();
        }

        if (empty($data['status'])) {
            $data['status'] = 'unpaid';
        }

        // Validate admission_id if provided
        if (!empty($data['admission_id'])) {
            $admission = $this->admissionModel->find($data['admission_id']);
            if (!$admission) {
                return redirect()->back()->withInput()->with('errors', ['Admission ID not found']);
            }
            
            // Check if admission belongs to the same patient
            if ($admission['patient_id'] !== $data['patient_id']) {
                return redirect()->back()->withInput()->with('errors', ['Admission does not belong to the selected patient']);
            }
        } else {
            // Set admission_id to null if not provided
            $data['admission_id'] = null;
        }

        if (! $this->invoiceModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->invoiceModel->errors());
        }

        return redirect()->to('/invoices');
    }

    public function show($id = null)
    {
        if ($id === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invoice not found');
        }

        if ($redirect = $this->enforceRoles(['admin', 'receptionist', 'accountant', 'pharmacist'])) {
            return $redirect;
        }

        $user = session()->get('user');
        $branchId = $user['branch_id'] ?? null;
        $isGlobal = $user && (empty($branchId) || $user['role'] === 'admin');

        $builder = $this->invoiceModel->builder();
        $builder->select('invoices.*, '
            . 'patients.first_name AS patient_first_name, patients.last_name AS patient_last_name');
        $builder->join('patients', 'patients.patient_id = invoices.patient_id', 'left');

        if (! $isGlobal && $branchId) {
            $builder->where('invoices.branch_id', $branchId);
        }

        $builder->where('invoices.id', $id);

        $invoice = $builder->get()->getRowArray();

        if (! $invoice) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invoice not found');
        }

        return view('invoices/show', [
            'invoice' => $invoice,
        ]);
    }

    public function markPaid($id = null)
    {
        if ($id === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invoice not found');
        }

        if ($redirect = $this->enforceRoles(['admin', 'receptionist', 'accountant', 'pharmacist'])) {
            return $redirect;
        }

        $invoice = $this->invoiceModel->find($id);

        if (! $invoice) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invoice not found');
        }

        $user = session()->get('user');
        $branchId = $user['branch_id'] ?? null;
        $isGlobal = $user && (empty($branchId) || $user['role'] === 'admin');

        if (! $isGlobal && $branchId && (int) ($invoice['branch_id'] ?? 0) !== (int) $branchId) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invoice not found');
        }

        if (! $this->invoiceModel->update($id, ['status' => 'paid'])) {
            return redirect()->back()->with('errors', $this->invoiceModel->errors());
        }

        return redirect()->to('/invoices/show/' . $id);
    }

    public function delete($id = null)
    {
        if ($id === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invoice not found');
        }

        if ($redirect = $this->enforceRoles(['admin', 'receptionist', 'accountant', 'pharmacist'])) {
            return $redirect;
        }

        $invoice = $this->invoiceModel->find($id);

        if (! $invoice) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invoice not found');
        }

        $user = session()->get('user');
        $branchId = $user['branch_id'] ?? null;
        $isGlobal = $user && (empty($branchId) || $user['role'] === 'admin');

        if (! $isGlobal && $branchId && (int) ($invoice['branch_id'] ?? 0) !== (int) $branchId) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invoice not found');
        }

        $this->invoiceModel->delete($id);

        return redirect()->to('/invoices');
    }
}
