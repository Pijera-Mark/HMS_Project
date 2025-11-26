<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="mb-4">
        <h1><i class="fas fa-user-injured me-2"></i>Patient Details</h1>
    </div>
    <div class="mb-4">
        <a href="/patients/edit/<?= esc($patient['patient_id']) ?>" class="btn btn-warning me-2">
            <i class="fas fa-edit me-1"></i>Edit
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-user me-1"></i>Basic Information</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>ID:</strong>
                    <div><?= esc($patient['patient_id']) ?></div>
                </div>
                <div class="col-md-4">
                    <strong>Name:</strong>
                    <div><?= esc($patient['first_name']) ?> <?= esc($patient['last_name']) ?></div>
                </div>
                <div class="col-md-4">
                    <strong>Date of Birth:</strong>
                    <div><?= esc($patient['date_of_birth']) ?></div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Gender:</strong>
                    <div><?= esc($patient['gender']) ?></div>
                </div>
                <div class="col-md-4">
                    <strong>Status:</strong>
                    <div><?= esc($patient['status'] ?? 'Active') ?></div>
                </div>
                <div class="col-md-4">
                    <strong>Blood Type:</strong>
                    <div><?= esc($patient['blood_type'] ?? '-') ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-address-book me-1"></i>Contact Details</h5>
                </div>
                <div class="card-body">
                    <p><strong>Phone:</strong><br><?= esc($patient['phone']) ?></p>
                    <p><strong>Email:</strong><br><?= esc($patient['email']) ?></p>
                    <p><strong>Address:</strong><br><?= esc($patient['address']) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-phone-square me-1"></i>Emergency Contact</h5>
                </div>
                <div class="card-body">
                    <p><strong>Contact Name:</strong><br><?= esc($patient['emergency_contact'] ?? '-') ?></p>
                    <p><strong>Emergency Phone:</strong><br><?= esc($patient['emergency_phone'] ?? '-') ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-notes-medical me-1"></i>Medical Information</h5>
        </div>
        <div class="card-body">
            <p><strong>Medical History:</strong></p>
            <p><?= nl2br(esc($patient['medical_history'] ?? 'None recorded')) ?></p>
            <p><strong>Allergies:</strong></p>
            <p><?= nl2br(esc($patient['allergies'] ?? 'None reported')) ?></p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
