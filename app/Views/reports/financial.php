<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-chart-line"></i>
            Financial Reports
        </h1>
        <div class="page-breadcrumb">
            <a href="/dashboard" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Financial Reports</span>
        </div>
    </div>

    <!-- Filters -->
    <div class="content-card mb-4">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-filter me-2"></i>Report Filters
            </h5>
            <form method="GET" action="/reports/financial" class="row g-3">
                <div class="col-md-3">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" 
                           value="<?= esc($filters['date_from'] ?? date('Y-m-01')) ?>">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" 
                           value="<?= esc($filters['date_to'] ?? date('Y-m-d')) ?>">
                </div>
                <div class="col-md-3">
                    <label for="branch_id" class="form-label">Branch</label>
                    <select name="branch_id" id="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        <?php if (isset($branches)): ?>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?= $branch['id'] ?>" 
                                        <?= ($filters['branch_id'] ?? '') == $branch['id'] ? 'selected' : '' ?>>
                                    <?= esc($branch['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="report_type" class="form-label">Report Type</label>
                    <select name="report_type" id="report_type" class="form-select">
                        <option value="summary" <?= ($filters['report_type'] ?? 'summary') == 'summary' ? 'selected' : '' ?>>Summary</option>
                        <option value="detailed" <?= ($filters['report_type'] ?? 'summary') == 'detailed' ? 'selected' : '' ?>>Detailed</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Generate Report
                    </button>
                    <a href="/reports/financial" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="content-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="summary-icon bg-primary">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="summary-title">Total Revenue</h6>
                            <h3 class="summary-value"><?= number_format($data['summary']['total_revenue'] ?? 0, 2) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="summary-icon bg-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="summary-title">Paid Amount</h6>
                            <h3 class="summary-value"><?= number_format($data['summary']['paid_amount'] ?? 0, 2) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="summary-icon bg-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="summary-title">Unpaid Amount</h6>
                            <h3 class="summary-value"><?= number_format($data['summary']['unpaid_amount'] ?? 0, 2) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="summary-icon bg-info">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="summary-title">Total Invoices</h6>
                            <h3 class="summary-value"><?= number_format($data['summary']['total_invoices'] ?? 0) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="content-card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-area me-2"></i>Revenue Trend
                    </h5>
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="content-card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>Payment Methods
                    </h5>
                    <canvas id="paymentChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Invoices -->
    <div class="content-card">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-list me-2"></i>Recent Invoices
            </h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Patient</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($data['invoices']) && count($data['invoices']) > 0): ?>
                            <?php foreach ($data['invoices'] as $invoice): ?>
                                <tr>
                                    <td><?= esc($invoice['invoice_number']) ?></td>
                                    <td><?= esc($invoice['first_name'] . ' ' . $invoice['last_name']) ?></td>
                                    <td><?= number_format($invoice['total_amount'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $invoice['status'] == 'paid' ? 'success' : ($invoice['status'] == 'partially_paid' ? 'warning' : 'danger') ?>">
                                            <?= ucfirst($invoice['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($invoice['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No invoices found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.page-container {
    padding: 20px;
    min-height: 100vh;
    background: #f8f9fa;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e9ecef;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.page-breadcrumb {
    font-size: 0.875rem;
}

.breadcrumb-link {
    color: #6c757d;
    text-decoration: none;
}

.breadcrumb-link:hover {
    color: #495057;
}

.breadcrumb-current {
    color: #6c757d;
}

.breadcrumb-separator {
    margin: 0 8px;
}

.summary-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.summary-title {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0;
}

.summary-value {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.content-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-body {
    padding: 20px;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 20px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = <?= json_encode($data['revenue'] ?? []) ?>;
    
    // Check if revenue data exists and is valid
    if (revenueData && Array.isArray(revenueData) && revenueData.length > 0) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueData.map(item => item.date || 'Unknown'),
                datasets: [{
                    label: 'Revenue',
                    data: revenueData.map(item => parseFloat(item.revenue) || 0),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Paid Revenue',
                    data: revenueData.map(item => parseFloat(item.paid_revenue) || 0),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    } else {
        // Show no data message
        revenueCtx.font = '16px Arial';
        revenueCtx.fillStyle = '#666';
        revenueCtx.textAlign = 'center';
        revenueCtx.fillText('No revenue data available for the selected period', revenueCtx.canvas.width / 2, revenueCtx.canvas.height / 2);
    }

    // Payment Methods Chart
    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    const paymentData = <?= json_encode($data['payments'] ?? []) ?>;
    
    // Check if payment data exists and is valid
    if (paymentData && Array.isArray(paymentData) && paymentData.length > 0) {
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: paymentData.map(item => item.payment_method || 'Unknown'),
                datasets: [{
                    data: paymentData.map(item => parseFloat(item.total_amount) || 0),
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    } else {
        // Show no data message
        paymentCtx.font = '16px Arial';
        paymentCtx.fillStyle = '#666';
        paymentCtx.textAlign = 'center';
        paymentCtx.fillText('No payment data available', paymentCtx.canvas.width / 2, paymentCtx.canvas.height / 2);
    }
});
</script>
<?= $this->endSection() ?>
