<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-calendar-plus me-2"></i>New Appointment</h1>
        <a href="/appointments" class="btn btn-outline-secondary"><i class="fas removed me-1"></i>Back to Appointments</a>
    </div>

    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="/appointments/create" method="post">
                <?= csrf_field() ?>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-user-injured me-1"></i>Patient</label>
                        <select name="patient_id" class="form-select" required>
                            <option value="">Select Patient</option>
                            <?php foreach ($patients as $patient): ?>
                                <option value="<?= $patient['patient_id'] ?>" <?= set_select('patient_id', $patient['patient_id']) ?>>
                                    <?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?> (ID: <?= esc($patient['patient_id']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-user-md me-1"></i>Doctor</label>
                        <select name="doctor_id" class="form-select" required>
                            <option value="">Select Doctor</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?= $doctor['doctor_id'] ?>" <?= set_select('doctor_id', $doctor['doctor_id']) ?>>
                                    Dr. <?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?> - <?= esc($doctor['specialization']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-clock me-1"></i>Scheduled Date & Time</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" value="<?= esc(old('scheduled_at')) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><i class="fas fa-hourglass-half me-1"></i>Duration (minutes)</label>
                        <input type="number" name="duration_minutes" class="form-control" value="<?= esc(old('duration_minutes', '30')) ?>" min="10" max="240">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><i class="fas fa-info-circle me-1"></i>Status</label>
                        <select name="status" class="form-select">
                            <option value="requested" <?= set_select('status', 'requested', true) ?>>Requested</option>
                            <option value="scheduled" <?= set_select('status', 'scheduled') ?>>Scheduled</option>
                            <option value="confirmed" <?= set_select('status', 'confirmed') ?>>Confirmed</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-notes-medical me-1"></i>Reason</label>
                    <textarea name="reason" class="form-control" rows="3"><?= esc(old('reason')) ?></textarea>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i>Create Appointment</button>
                    <a href="/appointments" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
