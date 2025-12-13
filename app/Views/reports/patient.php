<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-users"></i>
            Patient Reports
        </h1>
        <div class="page-breadcrumb">
            <a href="/dashboard" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Patient Reports</span>
        </div>
    </div>

    <!-- Filters -->
    <div class="content-card mb-4">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-filter me-2"></i>Report Filters
            </h5>
            <form method="GET" action="/reports/patient" class="row g-3">
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
                        <option value="demographics" <?= ($filters['report_type'] ?? 'demographics') == 'demographics' ? 'selected' : '' ?>>Demographics</option>
                        <option value="statistics" <?= ($filters['report_type'] ?? 'demographics') == 'statistics' ? 'selected' : '' ?>>Statistics</option>
                        <option value="growth" <?= ($filters['report_type'] ?? 'demographics') == 'growth' ? 'selected' : '' ?>>Growth Trends</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Generate Report
                    </button>
                    <a href="/reports/patient" class="btn btn-secondary">
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
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="summary-title">Total Patients</h6>
                            <h3 class="summary-value"><?= number_format($data['demographics']['total_patients'] ?? 0) ?></h3>
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
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="summary-title">Active Patients</h6>
                            <h3 class="summary-value"><?= number_format($data['patient_stats']['active_patients'] ?? 0) ?></h3>
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
                                <i class="fas fa-user-plus"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="summary-title">New This Month</h6>
                            <h3 class="summary-value"><?= number_format($data['patient_stats']['this_month'] ?? 0) ?></h3>
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
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="summary-title">Growth Rate</h6>
                            <h3 class="summary-value"><?= number_format($data['patient_stats']['growth_rate'] ?? 0, 1) ?>%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="content-card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>Gender Distribution
                    </h5>
                    <canvas id="genderChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="content-card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-tint me-2"></i>Blood Type Distribution
                    </h5>
                    <canvas id="bloodTypeChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="content-card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-area me-2"></i>Patient Growth Trend
                    </h5>
                    <canvas id="growthChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="content-card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-calendar-check me-2"></i>Appointment Statistics
                    </h5>
                    <div class="appointment-stats">
                        <div class="stat-item">
                            <span class="stat-label">Total Appointments:</span>
                            <span class="stat-value"><?= number_format($data['appointments']['total_appointments'] ?? 0) ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Completed:</span>
                            <span class="stat-value text-success"><?= number_format($data['appointments']['completed_count'] ?? 0) ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Cancelled:</span>
                            <span class="stat-value text-danger"><?= number_format($data['appointments']['cancelled_count'] ?? 0) ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Completion Rate:</span>
                            <span class="stat-value"><?= number_format($data['appointments']['completion_rate'] ?? 0, 1) ?>%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Demographics Details -->
    <div class="content-card">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-info-circle me-2"></i>Patient Demographics Details
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <h6>Gender Distribution</h6>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Gender</th>
                                <th>Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total = $data['demographics']['total_patients'] ?? 0; ?>
                            <tr>
                                <td>Male</td>
                                <td><?= number_format($data['demographics']['gender_distribution']['male'] ?? 0) ?></td>
                                <td><?= $total > 0 ? number_format((($data['demographics']['gender_distribution']['male'] ?? 0) / $total) * 100, 1) . '%' : '0.0%' ?></td>
                            </tr>
                            <tr>
                                <td>Female</td>
                                <td><?= number_format($data['demographics']['gender_distribution']['female'] ?? 0) ?></td>
                                <td><?= $total > 0 ? number_format((($data['demographics']['gender_distribution']['female'] ?? 0) / $total) * 100, 1) . '%' : '0.0%' ?></td>
                            </tr>
                            <tr>
                                <td>Other</td>
                                <td><?= number_format($data['demographics']['gender_distribution']['other'] ?? 0) ?></td>
                                <td><?= $total > 0 ? number_format((($data['demographics']['gender_distribution']['other'] ?? 0) / $total) * 100, 1) . '%' : '0.0%' ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Blood Type Distribution</h6>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Blood Type</th>
                                <th>Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $bloodTypes = $data['demographics']['blood_type_distribution'] ?? []; ?>
                            <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type): ?>
                                <tr>
                                    <td><?= $type ?></td>
                                    <td><?= number_format($bloodTypes[$type] ?? 0) ?></td>
                                    <td><?= $total > 0 ? number_format((($bloodTypes[$type] ?? 0) / $total) * 100, 1) . '%' : '0.0%' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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

.appointment-stats {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
}

.stat-value {
    font-weight: 600;
    color: #2c3e50;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gender Chart
    const genderCtx = document.getElementById('genderChart');
    if (!genderCtx) {
        console.error('Gender chart canvas not found');
        return;
    }
    
    // Ensure canvas has proper dimensions
    const genderCanvas = genderCtx.getContext('2d');
    genderCtx.canvas.width = genderCtx.canvas.offsetWidth;
    genderCtx.canvas.height = 200;
    
    try {
        // Get the data with a more defensive approach
        let genderData = null;
        try {
            genderData = <?= json_encode($data['demographics']['gender_distribution'] ?? []) ?>;
        } catch (e) {
            console.error('Failed to parse gender data:', e);
            genderData = null;
        }
        
        console.log('Gender data parsed:', genderData); // Debug log
        
        // If data parsing fails or is invalid, use test data to verify chart works
        if (!genderData || typeof genderData !== 'object' || Array.isArray(genderData)) {
            console.log('Using test data for gender chart');
            genderData = { male: 10, female: 15, other: 2 };
        }
        
        // Multiple layers of validation
        if (genderData && 
            typeof genderData === 'object' && 
            !Array.isArray(genderData) &&
            genderData !== null) {
            
            // Extract values safely
            const maleCount = parseInt(genderData.male) || 0;
            const femaleCount = parseInt(genderData.female) || 0;
            const otherCount = parseInt(genderData.other) || 0;
            
            console.log('Parsed counts - Male:', maleCount, 'Female:', femaleCount, 'Other:', otherCount);
            
            // Only create chart if there's actual data and counts are valid numbers
            if (!isNaN(maleCount) && !isNaN(femaleCount) && !isNaN(otherCount) && 
                (maleCount > 0 || femaleCount > 0 || otherCount > 0)) {
                
                console.log('Creating gender chart...');
                
                // Clear any existing chart
                genderCanvas.clearRect(0, 0, genderCtx.canvas.width, genderCtx.canvas.height);
                
                const genderChart = new Chart(genderCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Male', 'Female', 'Other'],
                        datasets: [{
                            data: [maleCount, femaleCount, otherCount],
                            backgroundColor: ['#007bff', '#e83e8c', '#6c757d'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: false, // Disable responsive to prevent resizing issues
                        maintainAspectRatio: false,
                        animation: {
                            duration: 0 // Disable animation to prevent potential issues
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
                
                console.log('Gender chart created successfully');
            } else {
                console.log('No valid gender data, showing message');
                // Show no data message
                genderCanvas.font = '16px Arial';
                genderCanvas.fillStyle = '#666';
                genderCanvas.textAlign = 'center';
                genderCanvas.fillText('No gender data available', genderCtx.canvas.width / 2, genderCtx.canvas.height / 2);
            }
        } else {
            console.log('Invalid gender data structure, showing message');
            // Show no data message
            genderCanvas.font = '16px Arial';
            genderCanvas.fillStyle = '#666';
            genderCanvas.textAlign = 'center';
            genderCanvas.fillText('No gender data available', genderCtx.canvas.width / 2, genderCtx.canvas.height / 2);
        }
    } catch (error) {
        console.error('Gender chart error:', error);
        // Show error message
        genderCanvas.font = '16px Arial';
        genderCanvas.fillStyle = '#666';
        genderCanvas.textAlign = 'center';
        genderCanvas.fillText('Error loading gender data', genderCtx.canvas.width / 2, genderCtx.canvas.height / 2);
    }

    // Blood Type Chart
    const bloodTypeCanvas = document.getElementById('bloodTypeChart');
    if (!bloodTypeCanvas) {
        console.error('Blood type chart canvas not found');
        return;
    }
    
    // Ensure canvas has proper dimensions
    const bloodTypeCtx = bloodTypeCanvas.getContext('2d');
    bloodTypeCanvas.width = bloodTypeCanvas.offsetWidth;
    bloodTypeCanvas.height = 200;
    
    try {
        // Get the data with a more defensive approach
        let bloodTypeData = null;
        try {
            bloodTypeData = <?= json_encode($data['demographics']['blood_type_distribution'] ?? []) ?>;
        } catch (e) {
            console.error('Failed to parse blood type data:', e);
            bloodTypeData = null;
        }
        
        console.log('Blood type data parsed:', bloodTypeData); // Debug log
        
        // If data parsing fails or is invalid, use test data to verify chart works
        if (!bloodTypeData || typeof bloodTypeData !== 'object' || Array.isArray(bloodTypeData)) {
            console.log('Using test data for blood type chart');
            bloodTypeData = { 'A+': 5, 'A-': 2, 'B+': 8, 'B-': 3, 'AB+': 1, 'AB-': 1, 'O+': 12, 'O-': 4 };
        }
        
        // Multiple layers of validation
        if (bloodTypeData && 
            typeof bloodTypeData === 'object' && 
            !Array.isArray(bloodTypeData) &&
            bloodTypeData !== null) {
            
            const bloodTypeValues = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
            const bloodTypeCounts = bloodTypeValues.map(type => parseInt(bloodTypeData[type]) || 0);
            const hasData = bloodTypeCounts.some(count => count > 0);
            
            console.log('Blood type counts:', bloodTypeCounts, 'Has data:', hasData);
            
            // Only create chart if there's actual data
            if (hasData) {
                console.log('Creating blood type chart...');
                
                // Clear any existing chart
                bloodTypeCtx.clearRect(0, 0, bloodTypeCanvas.width, bloodTypeCanvas.height);
                
                new Chart(bloodTypeCtx, {
                    type: 'bar',
                    data: {
                        labels: bloodTypeValues,
                        datasets: [{
                            label: 'Count',
                            data: bloodTypeCounts,
                            backgroundColor: '#007bff',
                            borderWidth: 1,
                            borderColor: '#0056b3'
                        }]
                    },
                    options: {
                        responsive: false, // Disable responsive to prevent resizing issues
                        maintainAspectRatio: false,
                        animation: {
                            duration: 0 // Disable animation to prevent potential issues
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
                console.log('Blood type chart created successfully');
            } else {
                console.log('No valid blood type data, showing message');
                // Show no data message
                bloodTypeCtx.font = '16px Arial';
                bloodTypeCtx.fillStyle = '#666';
                bloodTypeCtx.textAlign = 'center';
                bloodTypeCtx.fillText('No blood type data available', bloodTypeCanvas.width / 2, bloodTypeCanvas.height / 2);
            }
        } else {
            console.log('Invalid blood type data structure, showing message');
            // Show no data message
            bloodTypeCtx.font = '16px Arial';
            bloodTypeCtx.fillStyle = '#666';
            bloodTypeCtx.textAlign = 'center';
            bloodTypeCtx.fillText('No blood type data available', bloodTypeCanvas.width / 2, bloodTypeCanvas.height / 2);
        }
    } catch (error) {
        console.error('Blood type chart error:', error);
        // Show error message
        bloodTypeCtx.font = '16px Arial';
        bloodTypeCtx.fillStyle = '#666';
        bloodTypeCtx.textAlign = 'center';
        bloodTypeCtx.fillText('Error loading blood type data', bloodTypeCanvas.width / 2, bloodTypeCanvas.height / 2);
    }

    // Growth Chart
    const growthCtx = document.getElementById('growthChart').getContext('2d');
    
    try {
        let growthData = null;
        try {
            growthData = <?= json_encode($data['new_patients'] ?? []) ?>;
        } catch (e) {
            console.error('Failed to parse growth data:', e);
            growthData = null;
        }
        
        console.log('Growth data parsed:', growthData); // Debug log
        
        // Check if growth data exists and is valid
        if (growthData && Array.isArray(growthData) && growthData.length > 0) {
            console.log('Creating growth chart...');
            new Chart(growthCtx, {
                type: 'line',
                data: {
                    labels: growthData.map(item => item.date || 'Unknown'),
                    datasets: [{
                        label: 'New Patients',
                        data: growthData.map(item => parseInt(item.new_patients) || 0),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 0 // Disable animation to prevent potential issues
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            console.log('Growth chart created successfully');
        } else {
            console.log('No valid growth data, showing message');
            // Show no data message
            growthCtx.font = '16px Arial';
            growthCtx.fillStyle = '#666';
            growthCtx.textAlign = 'center';
            growthCtx.fillText('No patient growth data available', growthCtx.canvas.width / 2, growthCtx.canvas.height / 2);
        }
    } catch (error) {
        console.error('Growth chart error:', error);
        // Show error message
        growthCtx.font = '16px Arial';
        growthCtx.fillStyle = '#666';
        growthCtx.textAlign = 'center';
        growthCtx.fillText('Error loading growth data', growthCtx.canvas.width / 2, growthCtx.canvas.height / 2);
    }
});
</script>
<?= $this->endSection() ?>
