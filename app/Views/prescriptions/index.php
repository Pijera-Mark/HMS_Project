<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Prescriptions</h5>
                    <a href="<?= site_url('prescriptions/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Prescription
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
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" 
                                       value="<?= esc($filters['date_from']) ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" 
                                       value="<?= esc($filters['date_to']) ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="active" <?= ($filters['status'] == 'active') ? 'selected' : '' ?>>Active</option>
                                    <option value="completed" <?= ($filters['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= ($filters['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <a href="<?= site_url('prescriptions') ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Prescriptions Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Medication</th>
                                    <th>Dosage</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($prescriptions)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No prescriptions found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($prescriptions as $prescription): ?>
                                        <tr>
                                            <td><?= date('M d, Y', strtotime($prescription['created_at'])) ?></td>
                                            <td><?= esc($prescription['patient_name'] ?? 'N/A') ?></td>
                                            <td><?= esc($prescription['doctor_name'] ?? 'N/A') ?></td>
                                            <td><?= esc($prescription['medication']) ?></td>
                                            <td><?= esc($prescription['dosage']) ?></td>
                                            <td><?= esc($prescription['duration']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $prescription['status'] == 'active' ? 'success' : ($prescription['status'] == 'completed' ? 'info' : 'danger') ?>">
                                                    <?= esc(ucfirst($prescription['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= site_url('prescriptions/' . $prescription['id']) ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= site_url('prescriptions/edit/' . $prescription['id']) ?>" 
                                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                                            onclick="printPrescription('<?= site_url('prescriptions/print/' . $prescription['id']) ?>')" 
                                                            title="Print">
                                                        <i class="fas fa-print"></i>
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
function printPrescription(url) {
    window.open(url, '_blank', 'width=800,height=600');
}
</script>
<?= $this->endSection() ?>
