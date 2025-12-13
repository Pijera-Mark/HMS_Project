<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Medical Record</h5>
                    <a href="<?= site_url('medical-records') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Records
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= site_url('medical-records/store') ?>">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="patient_id" class="form-label">Patient *</label>
                                    <select name="patient_id" id="patient_id" class="form-select" required>
                                        <option value="">Select Patient</option>
                                        <?php foreach ($patients as $patient): ?>
                                            <option value="<?= $patient['patient_id'] ?>">
                                                <?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="doctor_id" class="form-label">Doctor *</label>
                                    <select name="doctor_id" id="doctor_id" class="form-select" required>
                                        <option value="">Select Doctor</option>
                                        <?php foreach ($doctors as $doctor): ?>
                                            <option value="<?= $doctor['doctor_id'] ?>">
                                                <?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="visit_date" class="form-label">Visit Date *</label>
                                    <input type="date" name="visit_date" id="visit_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="visit_type" class="form-label">Visit Type *</label>
                                    <select name="visit_type" id="visit_type" class="form-select" required>
                                        <option value="">Select Visit Type</option>
                                        <option value="consultation">Consultation</option>
                                        <option value="follow_up">Follow-up</option>
                                        <option value="emergency">Emergency</option>
                                        <option value="routine">Routine Check-up</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="chief_complaint" class="form-label">Chief Complaint *</label>
                            <textarea name="chief_complaint" id="chief_complaint" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="diagnosis" class="form-label">Diagnosis *</label>
                            <textarea name="diagnosis" id="diagnosis" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="treatment" class="form-label">Treatment Plan *</label>
                            <textarea name="treatment" id="treatment" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="prescription" class="form-label">Prescription</label>
                            <textarea name="prescription" id="prescription" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= site_url('medical-records') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Medical Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const visitDate = document.getElementById('visit_date');
    if (visitDate && !visitDate.value) {
        visitDate.valueAsDate = new Date();
    }
});
</script>
<?= $this->endSection() ?>
