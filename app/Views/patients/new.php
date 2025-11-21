<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user-plus me-2"></i>Add Patient</h1>
        <a href="/patients" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back to Patients</a>
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
                                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label"><i class="fas fa-signature me-1"></i>Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="date_of_birth" class="form-label"><i class="fas fa-birthday-cake me-1"></i>Date of Birth</label>
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label"><i class="fas fa-venus-mars me-1"></i>Gender</label>
                                        <select class="form-select" id="gender" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
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
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label"><i class="fas fa-envelope me-1"></i>Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="emergency_contact" class="form-label"><i class="fas fa-phone-square me-1"></i>Emergency Contact</label>
                                    <input type="text" class="form-control" id="emergency_contact" name="emergency_contact">
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
                                <div class="mb-3">
                                    <label for="medical_history" class="form-label"><i class="fas fa-history me-1"></i>Medical History</label>
                                    <textarea class="form-control" id="medical_history" name="medical_history" rows="4"></textarea>
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
