<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="mb-4">
        <h1><i class="fas fa-user-plus me-2"></i>Add Patient</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/patients/create" method="post">
                <?= csrf_field() ?>
                <!-- Personal Information -->
                <div class="accordion" id="patientFormAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="personalInfoHeading">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#personalInfo" aria-expanded="true" aria-controls="personalInfo">
                                <i class="fas fa-user me-2"></i>Personal Information
                            </button>
                        </h2>
                        <div id="personalInfo" class="accordion-collapse collapse show" aria-labelledby="personalInfoHeading" data-bs-parent="#patientFormAccordion">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label"><i class="fas fa-signature me-1"></i>First Name</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?= esc(old('first_name')) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label"><i class="fas fa-signature me-1"></i>Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?= esc(old('last_name')) ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="date_of_birth" class="form-label"><i class="fas fa-birthday-cake me-1"></i>Date of Birth</label>
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?= esc(old('date_of_birth')) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label"><i class="fas fa-venus-mars me-1"></i>Gender</label>
                                        <select class="form-select" id="gender" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male" <?= old('gender') === 'Male' ? 'selected' : '' ?>>Male</option>
                                            <option value="Female" <?= old('gender') === 'Female' ? 'selected' : '' ?>>Female</option>
                                            <option value="Other" <?= old('gender') === 'Other' ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="contactHeading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contact" aria-expanded="false" aria-controls="contact">
                                <i class="fas fa-address-book me-2"></i>Contact Details
                            </button>
                        </h2>
                        <div id="contact" class="accordion-collapse collapse" aria-labelledby="contactHeading" data-bs-parent="#patientFormAccordion">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label"><i class="fas fa-phone me-1"></i>Phone</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" value="<?= esc(old('phone')) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label"><i class="fas fa-envelope me-1"></i>Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email')) ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3"><?= esc(old('address')) ?></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="emergency_contact" class="form-label"><i class="fas fa-user me-1"></i>Emergency Contact Name</label>
                                        <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" value="<?= esc(old('emergency_contact')) ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="emergency_phone" class="form-label"><i class="fas fa-phone-square me-1"></i>Emergency Contact Phone *</label>
                                        <input type="tel" class="form-control" id="emergency_phone" name="emergency_phone" value="<?= esc(old('emergency_phone')) ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Information -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="medicalHeading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#medical" aria-expanded="false" aria-controls="medical">
                                <i class="fas fa-notes-medical me-2"></i>Medical Information
                            </button>
                        </h2>
                        <div id="medical" class="accordion-collapse collapse" aria-labelledby="medicalHeading" data-bs-parent="#patientFormAccordion">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="blood_type" class="form-label"><i class="fas fa-tint me-1"></i>Blood Type</label>
                                        <select class="form-select" id="blood_type" name="blood_type">
                                            <option value="">Select Blood Type</option>
                                            <option value="A+" <?= old('blood_type') === 'A+' ? 'selected' : '' ?>>A+</option>
                                            <option value="A-" <?= old('blood_type') === 'A-' ? 'selected' : '' ?>>A-</option>
                                            <option value="B+" <?= old('blood_type') === 'B+' ? 'selected' : '' ?>>B+</option>
                                            <option value="B-" <?= old('blood_type') === 'B-' ? 'selected' : '' ?>>B-</option>
                                            <option value="AB+" <?= old('blood_type') === 'AB+' ? 'selected' : '' ?>>AB+</option>
                                            <option value="AB-" <?= old('blood_type') === 'AB-' ? 'selected' : '' ?>>AB-</option>
                                            <option value="O+" <?= old('blood_type') === 'O+' ? 'selected' : '' ?>>O+</option>
                                            <option value="O-" <?= old('blood_type') === 'O-' ? 'selected' : '' ?>>O-</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="allergies" class="form-label"><i class="fas fa-allergies me-1"></i>Allergies</label>
                                        <input type="text" class="form-control" id="allergies" name="allergies" value="<?= esc(old('allergies')) ?>" placeholder="e.g., Penicillin, Peanuts">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="medical_history" class="form-label"><i class="fas fa-history me-1"></i>Medical History</label>
                                    <textarea class="form-control" id="medical_history" name="medical_history" rows="4"><?= esc(old('medical_history')) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i>Add Patient</button>
                    <a href="/patients" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
