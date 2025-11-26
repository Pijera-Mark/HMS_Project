<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-outline-secondary btn-sm me-3" onclick="">
                <i class="fas removed me-1"></i> Back
            </button>
            <h1 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Appointments</h1>
        </div>
        <a href="/appointments/new" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Appointment</a>
    </div>

    <p class="text-muted mb-3">
        Total appointments: <?= is_array($appointments) ? count($appointments) : 0 ?>
    </p>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="<?= esc($filter_date ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <?php $statuses = ['requested','scheduled','confirmed','cancelled','completed']; ?>
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?= $s ?>" <?= (isset($filter_status) && $filter_status === $s) ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-filter"></i> Filter</button>
                </div>
            </form>

            <?php if (!empty($filter_date) || !empty($filter_status)): ?>
                <div class="small text-muted mt-2">
                    Showing appointments
                    <?php if (!empty($filter_date)): ?>
                        on <strong><?= esc($filter_date) ?></strong>
                    <?php endif; ?>
                    <?php if (!empty($filter_status)): ?>
                        with status <strong><?= ucfirst(esc($filter_status)) ?></strong>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Scheduled At</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($appointments)): ?>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?= esc($appointment['id']) ?></td>
                                <td>
                                    <?= esc(($appointment['patient_first_name'] ?? '') . ' ' . ($appointment['patient_last_name'] ?? '')) ?>
                                    <div class="text-muted small">ID: <?= esc($appointment['patient_id']) ?></div>
                                </td>
                                <td>
                                    <?= esc(($appointment['doctor_first_name'] ?? '') . ' ' . ($appointment['doctor_last_name'] ?? '')) ?>
                                    <div class="text-muted small">ID: <?= esc($appointment['doctor_id']) ?></div>
                                </td>
                                <td><?= isset($appointment['scheduled_at']) ? date('M d, Y h:i A', strtotime($appointment['scheduled_at'])) : '-' ?></td>
                                <td>
                                    <?php
                                        $status = $appointment['status'] ?? 'requested';
                                        $badgeClass = match ($status) {
                                            'confirmed' => 'bg-success',
                                            'completed' => 'bg-primary',
                                            'cancelled' => 'bg-danger',
                                            'scheduled' => 'bg-info',
                                            default => 'bg-secondary',
                                        };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= ucfirst($status) ?></span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/appointments/show/<?= $appointment['id'] ?>" class="btn btn-sm btn-info me-1" title="View details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (($appointment['status'] ?? '') !== 'confirmed' && ($appointment['status'] ?? '') !== 'completed'): ?>
                                            <a href="/appointments/confirm/<?= $appointment['id'] ?>" class="btn btn-sm btn-success" title="Confirm" onclick="return confirm('Confirm this appointment?');">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (($appointment['status'] ?? '') !== 'cancelled' && ($appointment['status'] ?? '') !== 'completed'): ?>
                                            <a href="/appointments/cancel/<?= $appointment['id'] ?>" class="btn btn-sm btn-warning" title="Cancel" onclick="return confirm('Cancel this appointment?');">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="/appointments/delete/<?= $appointment['id'] ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this appointment?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No appointments found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
