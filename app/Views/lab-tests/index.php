<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lab Tests</h5>
                    <a href="<?= site_url('lab-tests/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Lab Test
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="patient_id" class="form-label">Patient</label>
                                <select name="patient_id" id="patient_id" class="form-select">
                                    <option value="">All Patients</option>
                                    <?php foreach ($patients as $patient): ?>
                                        <option value="<?= $patient['patient_id'] ?>" <?= ($filters['patient_id'] == $patient['patient_id']) ? 'selected' : '' ?>>
                                            <?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="doctor_id" class="form-label">Doctor</label>
                                <select name="doctor_id" id="doctor_id" class="form-select">
                                    <option value="">All Doctors</option>
                                    <?php foreach ($doctors as $doctor): ?>
                                        <option value="<?= $doctor['doctor_id'] ?>" <?= ($filters['doctor_id'] == $doctor['doctor_id']) ? 'selected' : '' ?>>
                                            <?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="test_type" class="form-label">Test Type</label>
                                <select name="test_type" id="test_type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="blood" <?= ($filters['test_type'] == 'blood') ? 'selected' : '' ?>>Blood Test</option>
                                    <option value="urine" <?= ($filters['test_type'] == 'urine') ? 'selected' : '' ?>>Urine Test</option>
                                    <option value="xray" <?= ($filters['test_type'] == 'xray') ? 'selected' : '' ?>>X-Ray</option>
                                    <option value="ultrasound" <?= ($filters['test_type'] == 'ultrasound') ? 'selected' : '' ?>>Ultrasound</option>
                                    <option value="ecg" <?= ($filters['test_type'] == 'ecg') ? 'selected' : '' ?>>ECG</option>
                                    <option value="mri" <?= ($filters['test_type'] == 'mri') ? 'selected' : '' ?>>MRI</option>
                                    <option value="ct_scan" <?= ($filters['test_type'] == 'ct_scan') ? 'selected' : '' ?>>CT Scan</option>
                                    <option value="other" <?= ($filters['test_type'] == 'other') ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" 
                                       value="<?= esc($filters['date_from']) ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" 
                                       value="<?= esc($filters['date_to']) ?>">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="pending" <?= ($filters['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="in_progress" <?= ($filters['status'] == 'in_progress') ? 'selected' : '' ?>>In Progress</option>
                                    <option value="completed" <?= ($filters['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= ($filters['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-9">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <a href="<?= site_url('lab-tests') ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Lab Tests Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Test ID</th>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Test Type</th>
                                    <th>Test Name</th>
                                    <th>Status</th>
                                    <th>Results</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($labTests)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No lab tests found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($labTests as $test): ?>
                                        <tr>
                                            <td><?= esc($test['test_id']) ?></td>
                                            <td><?= date('M d, Y', strtotime($test['test_date'])) ?></td>
                                            <td><?= esc($test['patient_name'] ?? 'N/A') ?></td>
                                            <td><?= esc($test['doctor_name'] ?? 'N/A') ?></td>
                                            <td><?= esc(ucfirst(str_replace('_', ' ', $test['test_type']))) ?></td>
                                            <td><?= esc($test['test_name']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $this->getLabTestStatusColor($test['status']) ?>">
                                                    <?= esc(ucfirst($test['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($test['status'] == 'completed' && !empty($test['results'])): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                                            onclick="viewResults('<?= site_url('lab-tests/results/' . $test['id']) ?>')" 
                                                            title="View Results">
                                                        <i class="fas fa-file-medical"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= site_url('lab-tests/' . $test['id']) ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($test['status'] != 'completed'): ?>
                                                        <a href="<?= site_url('lab-tests/edit/' . $test['id']) ?>" 
                                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ($test['status'] == 'pending'): ?>
                                                        <a href="<?= site_url('lab-tests/start/' . $test['id']) ?>" 
                                                           class="btn btn-sm btn-outline-success" title="Start Test">
                                                            <i class="fas fa-play"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ($test['status'] == 'in_progress'): ?>
                                                        <a href="<?= site_url('lab-tests/complete/' . $test['id']) ?>" 
                                                           class="btn btn-sm btn-outline-success" title="Complete Test">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmDelete('<?= site_url('lab-tests/delete/' . $test['id']) ?>')" 
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(url) {
    if (confirm('Are you sure you want to delete this lab test?')) {
        window.location.href = url;
    }
}

function viewResults(url) {
    window.open(url, '_blank', 'width=800,height=600');
}
</script>

<?php
// Helper function for status color (in a real app, this would be in a helper)
function getLabTestStatusColor($status) {
    switch($status) {
        case 'pending': return 'warning';
        case 'in_progress': return 'info';
        case 'completed': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}
?>
<?= $this->endSection() ?>
