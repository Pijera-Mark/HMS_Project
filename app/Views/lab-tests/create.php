<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Lab Test</h5>
                    <a href="<?= site_url('lab-tests') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Lab Tests
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= site_url('lab-tests/store') ?>">
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
                                    <label for="test_date" class="form-label">Test Date *</label>
                                    <input type="date" name="test_date" id="test_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="test_type" class="form-label">Test Type *</label>
                                    <select name="test_type" id="test_type" class="form-select" required>
                                        <option value="">Select Test Type</option>
                                        <option value="blood">Blood Test</option>
                                        <option value="urine">Urine Test</option>
                                        <option value="xray">X-Ray</option>
                                        <option value="ultrasound">Ultrasound</option>
                                        <option value="ecg">ECG</option>
                                        <option value="mri">MRI</option>
                                        <option value="ct_scan">CT Scan</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="test_name" class="form-label">Test Name *</label>
                            <input type="text" name="test_name" id="test_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="test_description" class="form-label">Test Description</label>
                            <textarea name="test_description" id="test_description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="clinical_notes" class="form-label">Clinical Notes</label>
                            <textarea name="clinical_notes" id="clinical_notes" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority</label>
                                    <select name="priority" id="priority" class="form-select">
                                        <option value="normal">Normal</option>
                                        <option value="urgent">Urgent</option>
                                        <option value="emergency">Emergency</option>
                                        <option value="routine">Routine</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="pending">Pending</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sample_collected" class="form-label">Sample Collected</label>
                                    <select name="sample_collected" id="sample_collected" class="form-select">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sample_type" class="form-label">Sample Type</label>
                                    <input type="text" name="sample_type" id="sample_type" class="form-control" placeholder="e.g., Blood, Urine, Tissue">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="instructions" class="form-label">Special Instructions</label>
                            <textarea name="instructions" id="instructions" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= site_url('lab-tests') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Lab Test
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
    // Set today's date as default for test date
    const testDate = document.getElementById('test_date');
    if (testDate && !testDate.value) {
        testDate.valueAsDate = new Date();
    }

    // Show/hide sample type field based on sample collected
    const sampleCollected = document.getElementById('sample_collected');
    const sampleType = document.getElementById('sample_type');
    
    function toggleSampleType() {
        if (sampleCollected.value === 'yes') {
            sampleType.closest('.mb-3').style.display = 'block';
            sampleType.required = true;
        } else {
            sampleType.closest('.mb-3').style.display = 'none';
            sampleType.required = false;
            sampleType.value = '';
        }
    }
    
    sampleCollected.addEventListener('change', toggleSampleType);
    toggleSampleType(); // Initialize
});
</script>
<?= $this->endSection() ?>
