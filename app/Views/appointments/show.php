<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-calendar-day me-2"></i>Appointment Details</h1>
        <div>
            <a href="/appointments" class="btn btn-outline-secondary me-2">
                <i class="fas removed me-1"></i>Back to Appointments
            </a>
            <a href="/appointments/confirm/<?= esc($appointment['id']) ?>" class="btn btn-success me-1">
                <i class="fas fa-check me-1"></i>Confirm
            </a>
            <a href="/appointments/cancel/<?= esc($appointment['id']) ?>" class="btn btn-warning">
                <i class="fas fa-times me-1"></i>Cancel
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-user-injured me-1"></i>Patient &amp; Doctor</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Patient:</strong>
                    <div>
                        <?= esc(trim(($appointment['patient_first_name'] ?? '') . ' ' . ($appointment['patient_last_name'] ?? ''))) ?>
                        <div class="text-muted small">ID: <?= esc($appointment['patient_id'] ?? '-') ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <strong>Doctor:</strong>
                    <div>
                        <?= esc(trim(($appointment['doctor_first_name'] ?? '') . ' ' . ($appointment['doctor_last_name'] ?? ''))) ?>
                        <div class="text-muted small">ID: <?= esc($appointment['doctor_id'] ?? '-') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-clock me-1"></i>Schedule &amp; Status</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Scheduled At:</strong>
                    <div>
                        <?= isset($appointment['scheduled_at']) ? date('M d, Y h:i A', strtotime($appointment['scheduled_at'])) : '-' ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <strong>Duration:</strong>
                    <div><?= esc($appointment['duration_minutes'] ?? 30) ?> minutes</div>
                </div>
                <div class="col-md-4">
                    <strong>Status:</strong>
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
                    <div><span class="badge <?= $badgeClass ?>"><?= ucfirst($status) ?></span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-notes-medical me-1"></i>Reason</h5>
        </div>
        <div class="card-body">
            <p><?= nl2br(esc($appointment['reason'] ?? 'No reason provided')) ?></p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
