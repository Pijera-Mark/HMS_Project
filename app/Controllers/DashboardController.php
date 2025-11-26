<?php

namespace App\Controllers;

use App\Models\PatientModel;
use App\Models\DoctorModel;
use App\Models\AppointmentModel;
use App\Models\AdmissionModel;
use App\Models\MedicineModel;
use App\Models\InvoiceModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $user = session()->get('user');

        if (!$user) {
            return redirect()->to('/login');
        }

        $branchId = $user['branch_id'] ?? null;
        $isGlobal = $user && (empty($branchId) || $user['role'] === 'admin');

        $patientModel     = new PatientModel();
        $doctorModel      = new DoctorModel();
        $appointmentModel = new AppointmentModel();
        $admissionModel   = new AdmissionModel();
        $medicineModel    = new MedicineModel();
        $invoiceModel     = new InvoiceModel();

        if ($isGlobal || ! $branchId) {
            $totalPatients = $patientModel->countAll();
        } else {
            $totalPatients = $patientModel->where('branch_id', $branchId)->countAllResults();
        }

        if ($isGlobal || ! $branchId) {
            $totalDoctors = $doctorModel->countAll();
        } else {
            $totalDoctors = $doctorModel->where('branch_id', $branchId)->countAllResults();
        }

        if ($isGlobal || ! $branchId) {
            $totalAppointments = $appointmentModel->countAll();
            $todayAppointments = $appointmentModel
                ->where('DATE(scheduled_at)', date('Y-m-d'))
                ->countAllResults();
            $activeAdmissions  = $admissionModel
                ->where('status', 'admitted')
                ->countAllResults();
        } else {
            $totalAppointments = $appointmentModel
                ->where('branch_id', $branchId)
                ->countAllResults();
            $todayAppointments = $appointmentModel
                ->where('branch_id', $branchId)
                ->where('DATE(scheduled_at)', date('Y-m-d'))
                ->countAllResults();
            $activeAdmissions  = $admissionModel
                ->where('branch_id', $branchId)
                ->where('status', 'admitted')
                ->countAllResults();
        }

        $branchFilterId = $isGlobal ? null : $branchId;

        $lowStockList      = $medicineModel->getLowStockMedicines($branchFilterId);
        $expiringList      = $medicineModel->getExpiringMedicines(30, $branchFilterId);
        $unpaidInvoiceList = $invoiceModel->getUnpaidInvoices($branchFilterId);

        $lowStockMedicines   = is_array($lowStockList) ? count($lowStockList) : 0;
        $expiringMedicines   = is_array($expiringList) ? count($expiringList) : 0;
        $unpaidInvoices      = is_array($unpaidInvoiceList) ? count($unpaidInvoiceList) : 0;

        $recentAppointmentsQuery = $appointmentModel
            ->where('scheduled_at >=', date('Y-m-d', strtotime('-7 days')));

        if (! $isGlobal && $branchId) {
            $recentAppointmentsQuery = $recentAppointmentsQuery->where('branch_id', $branchId);
        }

        $recentAppointments = $recentAppointmentsQuery
            ->orderBy('scheduled_at', 'DESC')
            ->limit(5)
            ->find();

        $totalRevenueBuilder = $invoiceModel
            ->selectSum('total_amount')
            ->where('status', 'paid');
        if (! $isGlobal && $branchId) {
            $totalRevenueBuilder = $totalRevenueBuilder->where('branch_id', $branchId);
        }
        $totalRevenueRow = $totalRevenueBuilder->first();

        $pendingAmountBuilder = $invoiceModel
            ->selectSum('total_amount')
            ->whereIn('status', ['unpaid', 'partially_paid']);
        if (! $isGlobal && $branchId) {
            $pendingAmountBuilder = $pendingAmountBuilder->where('branch_id', $branchId);
        }
        $pendingAmountRow = $pendingAmountBuilder->first();

        $totalRevenue  = $totalRevenueRow['total_amount'] ?? 0;
        $pendingAmount = $pendingAmountRow['total_amount'] ?? 0;

        $view = ($user['role'] ?? null) === 'admin'
            ? 'dashboard/admin'
            : 'dashboard/user';

        return view($view, [
            'user'                => $user,
            'stats'               => [
                'total_patients'      => $totalPatients,
                'total_doctors'       => $totalDoctors,
                'total_appointments'  => $totalAppointments,
                'today_appointments'  => $todayAppointments,
                'active_admissions'   => $activeAdmissions,
                'low_stock_medicines' => $lowStockMedicines,
                'expiring_medicines'  => $expiringMedicines,
                'unpaid_invoices'     => $unpaidInvoices,
                'total_revenue'       => $totalRevenue,
                'pending_amount'      => $pendingAmount,
            ],
            'recent_appointments' => $recentAppointments,
        ]);
    }

    public function help()
    {
        return view('help/index', [
            'user' => session()->get('user')
        ]);
    }

    // Temporary methods for sidebar features
    public function wards()
    {
        return view('temp/wards', [
            'user' => session()->get('user'),
            'message' => 'Ward Management feature coming soon!'
        ]);
    }

    public function medicalRecords()
    {
        return view('temp/medical-records', [
            'user' => session()->get('user'),
            'message' => 'Medical Records feature coming soon!'
        ]);
    }

    public function prescriptions()
    {
        return view('temp/prescriptions', [
            'user' => session()->get('user'),
            'message' => 'Prescriptions feature coming soon!'
        ]);
    }

    public function labTests()
    {
        return view('temp/lab-tests', [
            'user' => session()->get('user'),
            'message' => 'Lab Tests feature coming soon!'
        ]);
    }

    public function financialReports()
    {
        return view('temp/financial-reports', [
            'user' => session()->get('user'),
            'message' => 'Financial Reports feature coming soon!'
        ]);
    }

    public function patientReports()
    {
        return view('temp/patient-reports', [
            'user' => session()->get('user'),
            'message' => 'Patient Reports feature coming soon!'
        ]);
    }

    public function auditTrail()
    {
        return view('temp/audit-trail', [
            'user' => session()->get('user'),
            'message' => 'Audit Trail feature coming soon!'
        ]);
    }

    public function profile()
    {
        return view('temp/profile', [
            'user' => session()->get('user'),
            'message' => 'Profile management feature coming soon!'
        ]);
    }
}
