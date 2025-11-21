<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table            = 'invoices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'invoice_number',
        'patient_id',
        'admission_id',
        'created_by',
        'total_amount',
        'status',
        'branch_id'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'invoice_number' => 'required|is_unique[invoices.invoice_number]',
        'patient_id'     => 'required|integer',
        'created_by'     => 'required|integer',
        'total_amount'   => 'required|decimal',
        'status'         => 'required|in_list[unpaid,paid,partially_paid]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    /**
     * Generate invoice number
     */
    public function generateInvoiceNumber()
    {
        $lastInvoice = $this->orderBy('id', 'DESC')->first();
        $lastNumber = $lastInvoice ? intval(substr($lastInvoice['invoice_number'], 4)) : 0;
        return 'INV-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get unpaid invoices
     */
    public function getUnpaidInvoices($branchId = null)
    {
        $builder = $this->whereIn('status', ['unpaid', 'partially_paid']);

        if ($branchId) {
            $builder = $builder->where('branch_id', $branchId);
        }

        return $builder->findAll();
    }
}
