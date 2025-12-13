<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Prescription</h5>
                    <a href="<?= site_url('prescriptions') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Prescriptions
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= site_url('prescriptions/store') ?>">
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
                                    <label for="prescription_date" class="form-label">Prescription Date *</label>
                                    <input type="date" name="prescription_date" id="prescription_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="diagnosis" class="form-label">Diagnosis *</label>
                                    <input type="text" name="diagnosis" id="diagnosis" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="medication" class="form-label">Medication *</label>
                            <input type="text" name="medication" id="medication" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="dosage" class="form-label">Dosage *</label>
                                    <input type="text" name="dosage" id="dosage" class="form-control" placeholder="e.g., 500mg" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="frequency" class‏ class="-relevant">.
                                    <select name="frequency" id="frequency" class="form-select" required>
                                        <option value="">Select Frequency</option>
                                        <option value="once_daily">Once Daily</option>
                                        <option value="twice_daily">Twice Daily</option>
                                        <option value="three_times_daily">Three Times Daily</option>
                                        <option value="as_needed">As Needed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration *</label>
                                    <input type="text" name="duration" id="duration" class="form-control" placeholder="e.g., 7 days" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="instructions" class="form-label">Special Instructions</label>
                            <textarea name="instructions" id="instructions" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= site_url('prescriptions') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Prescription
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
    const prescriptionDate = document.getElementById('prescription_date');
    if (prescriptionDate && !prescriptionDate.value) {
        prescriptionDate.valueAsDate = new Date();
    }
});
</script>
<?= $this->endSection() ?>
