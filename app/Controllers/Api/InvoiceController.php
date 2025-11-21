<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;
use CodeIgniter\HTTP\ResponseInterface;

class InvoiceController extends BaseController
{
    protected $invoiceModel;

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
    }

    /**
     * Get all invoices with filters
     */
    public function index()
    {
        $status = $this->request->getGet('status');
        $patientId = $this->request->getGet('patient_id');
        $branchId = $this->request->getGet('branch_id');

        $builder = $this->invoiceModel->builder();

        if ($status) {
            $builder->where('status', $status);
        }
        if ($patientId) {
            $builder->where('patient_id', $patientId);
        }
        if ($branchId) {
            $builder->where('branch_id', $branchId);
        }

        $invoices = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $invoices
        ]);
    }

    /**
     * Get unpaid invoices
     */
    public function unpaid()
    {
        $branchId = $this->request->getGet('branch_id');

        $invoices = $this->invoiceModel->getUnpaidInvoices($branchId);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $invoices
        ]);
    }

    /**
     * Get single invoice
     */
    public function show($id = null)
    {
        $invoice = $this->invoiceModel->find($id);

        if (!$invoice) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Invoice not found'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $invoice
        ]);
    }

    /**
     * Create new invoice
     */
    public function create()
    {
        $data = $this->request->getJSON(true);

        // Allow client to pass branch_id to associate invoice with a branch
        // If not provided, branch_id will remain null (global or unspecified).

        // Generate invoice number if not provided
        if (!isset($data['invoice_number'])) {
            $data['invoice_number'] = $this->invoiceModel->generateInvoiceNumber();
        }

        // Set default status
        if (!isset($data['status'])) {
            $data['status'] = 'unpaid';
        }

        if ($this->invoiceModel->insert($data)) {
            return $this->response->setStatusCode(201)->setJSON([
                'status'  => 'success',
                'message' => 'Invoice created successfully',
                'data'    => $this->invoiceModel->find($this->invoiceModel->getInsertID())
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to create invoice',
            'errors'  => $this->invoiceModel->errors()
        ]);
    }

    /**
     * Update invoice
     */
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        if (!$this->invoiceModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Invoice not found'
            ]);
        }

        if ($this->invoiceModel->update($id, $data)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Invoice updated successfully',
                'data'    => $this->invoiceModel->find($id)
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to update invoice',
            'errors'  => $this->invoiceModel->errors()
        ]);
    }

    /**
     * Mark invoice as paid
     */
    public function markPaid($id = null)
    {
        if (!$this->invoiceModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Invoice not found'
            ]);
        }

        if ($this->invoiceModel->update($id, ['status' => 'paid'])) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Invoice marked as paid',
                'data'    => $this->invoiceModel->find($id)
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to update invoice status'
        ]);
    }

    /**
     * Delete invoice
     */
    public function delete($id = null)
    {
        if (!$this->invoiceModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Invoice not found'
            ]);
        }

        if ($this->invoiceModel->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Invoice deleted successfully'
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to delete invoice'
        ]);
    }
}
