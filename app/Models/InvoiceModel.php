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

    /**
     * Get revenue summary
     */
    public function getRevenueSummary(array $filters = []): array
    {
        $builder = $this->builder()
            ->select('
                COUNT(*) as total_invoices,
                SUM(total_amount) as total_revenue,
                SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN status = "unpaid" THEN total_amount ELSE 0 END) as unpaid_amount,
                SUM(CASE WHEN status = "partially_paid" THEN total_amount ELSE 0 END) as partially_paid_amount,
                AVG(total_amount) as average_invoice_amount
            ');

        if (!empty($filters['date_from'])) {
            $builder->where('DATE(created_at) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('DATE(created_at) <=', $filters['date_to']);
        }

        if (!empty($filters['branch_id'])) {
            $builder->where('branch_id', $filters['branch_id']);
        }

        $result = $builder->get()->getRowArray();
        
        return [
            'total_invoices' => (int)($result['total_invoices'] ?? 0),
            'total_revenue' => (float)($result['total_revenue'] ?? 0),
            'paid_amount' => (float)($result['paid_amount'] ?? 0),
            'unpaid_amount' => (float)($result['unpaid_amount'] ?? 0),
            'partially_paid_amount' => (float)($result['partially_paid_amount'] ?? 0),
            'average_invoice_amount' => (float)($result['average_invoice_amount'] ?? 0),
            'collection_rate' => $result['total_revenue'] > 0 ? 
                (($result['paid_amount'] / $result['total_revenue']) * 100) : 0
        ];
    }

    /**
     * Get daily revenue
     */
    public function getDailyRevenue(array $filters = []): array
    {
        $builder = $this->builder()
            ->select('
                DATE(created_at) as date,
                COUNT(*) as invoice_count,
                SUM(total_amount) as revenue,
                SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as paid_revenue
            ')
            ->groupBy('DATE(created_at)')
            ->orderBy('date', 'DESC');

        if (!empty($filters['date_from'])) {
            $builder->where('DATE(created_at) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('DATE(created_at) <=', $filters['date_to']);
        }

        if (!empty($filters['branch_id'])) {
            $builder->where('branch_id', $filters['branch_id']);
        }

        $results = $builder->get()->getResultArray();
        
        // Ensure data is properly formatted and numeric
        $formattedResults = [];
        foreach ($results as $result) {
            $formattedResults[] = [
                'date' => $result['date'] ?? date('Y-m-d'),
                'invoice_count' => (int)($result['invoice_count'] ?? 0),
                'revenue' => (float)($result['revenue'] ?? 0),
                'paid_revenue' => (float)($result['paid_revenue'] ?? 0)
            ];
        }
        
        return $formattedResults;
    }

    /**
     * Get invoices with filters
     */
    public function getInvoicesWithFilters(array $filters = []): array
    {
        $builder = $this->builder()
            ->select('invoices.*, patients.first_name, patients.last_name, patients.email')
            ->join('patients', 'patients.patient_id = invoices.patient_id', 'left');

        if (!empty($filters['date_from'])) {
            $builder->where('DATE(invoices.created_at) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('DATE(invoices.created_at) <=', $filters['date_to']);
        }

        if (!empty($filters['branch_id'])) {
            $builder->where('invoices.branch_id', $filters['branch_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('invoices.status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('invoices.invoice_number', $filters['search'])
                ->orLike('patients.first_name', $filters['search'])
                ->orLike('patients.last_name', $filters['search'])
                ->orLike('patients.email', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('invoices.created_at', 'DESC')->limit(50)->get()->getResultArray();
    }

    /**
     * Get payment methods summary
     */
    public function getPaymentMethodsSummary(array $filters = []): array
    {
        // Since invoices table doesn't have payment_method column in the current structure,
        // we'll return a basic summary. In a real implementation, this would come from a payments table.
        $builder = $this->builder()
            ->select('
                status as payment_method,
                COUNT(*) as count,
                SUM(total_amount) as total_amount
            ')
            ->groupBy('status');

        if (!empty($filters['date_from'])) {
            $builder->where('DATE(created_at) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('DATE(created_at) <=', $filters['date_to']);
        }

        if (!empty($filters['branch_id'])) {
            $builder->where('branch_id', $filters['branch_id']);
        }

        $results = $builder->get()->getResultArray();
        
        // Map status to more descriptive payment method names
        $paymentMethods = [];
        foreach ($results as $result) {
            $methodName = match($result['payment_method']) {
                'paid' => 'Paid (Full)',
                'partially_paid' => 'Partially Paid',
                'unpaid' => 'Unpaid',
                default => ucfirst($result['payment_method'])
            };
            
            $paymentMethods[] = [
                'payment_method' => $methodName,
                'count' => (int)$result['count'],
                'total_amount' => (float)$result['total_amount']
            ];
        }

        return $paymentMethods;
    }
}
