<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;
use App\Models\PatientModel;
use App\Models\AppointmentModel;
use App\Models\BranchModel;
use App\Services\ValidationService;

/**
 * Reports Controller
 * Generates various system reports
 */
class ReportsController extends BaseController
{
    protected InvoiceModel $invoiceModel;
    protected PatientModel $patientModel;
    protected AppointmentModel $appointmentModel;
    protected BranchModel $branchModel;
    protected ValidationService $validationService;

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
        $this->patientModel = new PatientModel();
        $this->appointmentModel = new AppointmentModel();
        $this->branchModel = new BranchModel();
        $this->validationService = new ValidationService();
    }

    /**
     * Financial Reports
     */
    public function financial()
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $filters = [
            'date_from' => $this->request->getGet('date_from') ?? date('Y-m-01'),
            'date_to' => $this->request->getGet('date_to') ?? date('Y-m-d'),
            'branch_id' => $this->request->getGet('branch_id'),
            'report_type' => $this->request->getGet('report_type') ?? 'summary'
        ];

        $data = $this->generateFinancialReport($filters);

        return view('reports/financial', [
            'data' => $data,
            'filters' => $filters,
            'branches' => $this->branchModel->findAll()
        ]);
    }

    /**
     * Patient Reports
     */
    public function patient()
    {
        if ($redirect = $this->enforceRoles(['admin', 'doctor', 'receptionist'])) {
            return $redirect;
        }

        $filters = [
            'date_from' => $this->request->getGet('date_from') ?? date('Y-m-01'),
            'date_to' => $this->request->getGet('date_to') ?? date('Y-m-d'),
            'branch_id' => $this->request->getGet('branch_id'),
            'report_type' => $this->request->getGet('report_type') ?? 'demographics'
        ];

        $data = $this->generatePatientReport($filters);

        return view('reports/patient', [
            'data' => $data,
            'filters' => $filters,
            'branches' => $this->branchModel->findAll()
        ]);
    }

    /**
     * Audit Trail Reports
     */
    public function audit()
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $filters = [
            'date_from' => $this->request->getGet('date_from') ?? date('Y-m-01'),
            'date_to' => $this->request->getGet('date_to') ?? date('Y-m-d'),
            'user_id' => $this->request->getGet('user_id'),
            'action' => $this->request->getGet('action'),
            'entity_type' => $this->request->getGet('entity_type')
        ];

        $data = $this->generateAuditReport($filters);

        return view('reports/audit', [
            'data' => $data,
            'filters' => $filters
        ]);
    }

    /**
     * Generate financial report data
     */
    private function generateFinancialReport(array $filters): array
    {
        $data = [
            'summary' => [],
            'revenue' => [],
            'expenses' => [],
            'invoices' => [],
            'payments' => []
        ];

        // Revenue summary
        $data['summary'] = $this->invoiceModel->getRevenueSummary($filters);
        
        // Daily revenue
        $data['revenue'] = $this->invoiceModel->getDailyRevenue($filters);
        
        // Recent invoices
        $data['invoices'] = $this->invoiceModel->getInvoicesWithFilters($filters);
        
        // Payment methods summary
        $data['payments'] = $this->invoiceModel->getPaymentMethodsSummary($filters);

        return $data;
    }

    /**
     * Generate patient report data
     */
    private function generatePatientReport(array $filters): array
    {
        $data = [
            'demographics' => [],
            'appointments' => [],
            'new_patients' => [],
            'patient_stats' => []
        ];

        // Patient demographics
        $data['demographics'] = $this->patientModel->getDemographics($filters);
        
        // Appointment statistics
        $data['appointments'] = $this->appointmentModel->getAppointmentStats($filters);
        
        // New patients by period
        $data['new_patients'] = $this->patientModel->getNewPatientsByPeriod($filters);
        
        // General patient statistics
        $data['patient_stats'] = $this->patientModel->getPatientStatistics($filters);

        return $data;
    }

    /**
     * Generate audit report data
     */
    private function generateAuditReport(array $filters): array
    {
        $data = [
            'activities' => [],
            'user_stats' => [],
            'action_stats' => [],
            'timeline' => []
        ];

        // Activity logs
        $data['activities'] = $this->getActivityLogs($filters);
        
        // User activity statistics
        $data['user_stats'] = $this->getUserActivityStats($filters);
        
        // Action type statistics
        $data['action_stats'] = $this->getActionStats($filters);
        
        // Activity timeline
        $data['timeline'] = $this->getActivityTimeline($filters);

        return $data;
    }

    /**
     * Export report to PDF/Excel
     */
    public function export($type)
    {
        if ($redirect = $this->enforceRoles(['admin'])) {
            return $redirect;
        }

        $filters = $this->request->getGet();
        $format = $this->request->getGet('format', 'pdf');

        switch ($type) {
            case 'financial':
                $data = $this->generateFinancialReport($filters);
                $filename = 'financial_report_' . date('Y-m-d');
                break;
            case 'patient':
                $data = $this->generatePatientReport($filters);
                $filename = 'patient_report_' . date('Y-m-d');
                break;
            case 'audit':
                $data = $this->generateAuditReport($filters);
                $filename = 'audit_report_' . date('Y-m-d');
                break;
            default:
                return redirect()->back()->with('error', 'Invalid report type');
        }

        if ($format === 'pdf') {
            return $this->exportToPDF($data, $filename);
        } elseif ($format === 'excel') {
            return $this->exportToExcel($data, $filename);
        }

        return redirect()->back()->with('error', 'Invalid export format');
    }

    /**
     * Export to PDF
     */
    private function exportToPDF(array $data, string $filename)
    {
        // Implementation would depend on PDF library (e.g., DomPDF, TCPDF)
        // For now, return a simple CSV export
        return $this->exportToCSV($data, $filename);
    }

    /**
     * Export to Excel
     */
    private function exportToExcel(array $data, string $filename)
    {
        // Implementation would depend on Excel library (e.g., PhpSpreadsheet)
        // For now, return a simple CSV export
        return $this->exportToCSV($data, $filename);
    }

    /**
     * Export to CSV
     */
    private function exportToCSV(array $data, string $filename)
    {
        $csv = '';
        
        if (isset($data['invoices'])) {
            $csv .= "Invoice Report\n";
            $csv .= "ID,Patient,Amount,Status,Date\n";
            foreach ($data['invoices'] as $invoice) {
                $csv .= "{$invoice['id']},{$invoice['patient_name']},{$invoice['amount']},{$invoice['status']},{$invoice['created_at']}\n";
            }
        }

        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', "attachment; filename=\"{$filename}.csv\"");
        echo $csv;
        exit;
    }

    /**
     * Get activity logs for audit
     */
    private function getActivityLogs(array $filters): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('activity_logs')
            ->select('activity_logs.*, users.name as user_name, users.email')
            ->join('users', 'users.id = activity_logs.user_id', 'left')
            ->orderBy('activity_logs.created_at', 'DESC');

        if (isset($filters['date_from'])) {
            $builder->where('activity_logs.created_at >=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $builder->where('activity_logs.created_at <=', $filters['date_to']);
        }
        if (isset($filters['user_id'])) {
            $builder->where('activity_logs.user_id', $filters['user_id']);
        }
        if (isset($filters['action'])) {
            $builder->where('activity_logs.action', $filters['action']);
        }
        if (isset($filters['entity_type'])) {
            $builder->where('activity_logs.entity_type', $filters['entity_type']);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get user activity statistics
     */
    private function getUserActivityStats(array $filters): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('activity_logs')
            ->select('users.name, COUNT(*) as count')
            ->join('users', 'users.id = activity_logs.user_id', 'left')
            ->groupBy('activity_logs.user_id')
            ->orderBy('count', 'DESC')
            ->limit(10);

        return $builder->get()->getResultArray();
    }

    /**
     * Get action statistics
     */
    private function getActionStats(array $filters): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('activity_logs')
            ->select('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderBy('count', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get activity timeline
     */
    private function getActivityTimeline(array $filters): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('activity_logs')
            ->select('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('DATE(created_at)')
            ->orderBy('date', 'DESC')
            ->limit(30);

        return $builder->get()->getResultArray();
    }
}
