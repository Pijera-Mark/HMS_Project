<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>">
                <i class="fas fa-hospital-alt me-2"></i>HMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('profile') ?>">
                            <i class="fas fa-user me-1"></i>Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('logout') ?>">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </h4>
                    </div>
                    <div class="card-body">
                        <?= form_open('profile/update', ['class' => 'needs-validation', 'novalidate']) ?>
                            
                            <!-- Basic Information -->
                            <h5 class="mb-3">Basic Information</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= old('name', $user['name']) ?>" required>
                                    <div class="invalid-feedback">Please provide your full name</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" 
                                           value="<?= $user['email'] ?>" readonly>
                                    <small class="text-muted">Email cannot be changed</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?= old('phone', $user['phone']) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                           value="<?= old('date_of_birth', $profile['date_of_birth'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <?php foreach ($genders as $gender): ?>
                                            <option value="<?= $gender ?>" <?= old('gender', $profile['gender'] ?? '') == $gender ? 'selected' : '' ?>>
                                                <?= ucfirst($gender) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="blood_group" class="form-label">Blood Group</label>
                                    <select class="form-select" id="blood_group" name="blood_group">
                                        <option value="">Select Blood Group</option>
                                        <?php foreach ($blood_groups as $bg): ?>
                                            <option value="<?= $bg ?>" <?= old('blood_group', $profile['blood_group'] ?? '') == $bg ? 'selected' : '' ?>>
                                                <?= $bg ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Address Information -->
                            <h5 class="mb-3">Address Information</h5>
                            <div class="mb-3">
                                <label for="address" class="form-label">Street Address</label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="<?= old('address', $profile['address'] ?? '') ?>">
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" 
                                           value="<?= old('city', $profile['city'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="state" class="form-label">State</label>
                                    <input type="text" class="form-control" id="state" name="state" 
                                           value="<?= old('state', $profile['state'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="postal_code" class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                           value="<?= old('postal_code', $profile['postal_code'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control" id="country" name="country" 
                                       value="<?= old('country', $profile['country'] ?? '') ?>">
                            </div>

                            <hr class="my-4">

                            <!-- Emergency Contact -->
                            <h5 class="mb-3">Emergency Contact</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="emergency_contact_name" class="form-label">Contact Name</label>
                                    <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" 
                                           value="<?= old('emergency_contact_name', $profile['emergency_contact_name'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="emergency_contact_phone" class="form-label">Contact Phone</label>
                                    <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" 
                                           value="<?= old('emergency_contact_phone', $profile['emergency_contact_phone'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="emergency_contact_relation" class="form-label">Relationship</label>
                                    <input type="text" class="form-control" id="emergency_contact_relation" name="emergency_contact_relation" 
                                           value="<?= old('emergency_contact_relation', $profile['emergency_contact_relation'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('profile') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>

                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 1050;">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 1050;">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-warning alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 1050;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function() {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();

        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            var value = e.target.value.replace(/\D/g, '');
            var formattedValue = '';
            
            if (value.length > 0) {
                if (value.length <= 3) {
                    formattedValue = value;
                } else if (value.length <= 6) {
                    formattedValue = value.slice(0, 3) + '-' + value.slice(3);
                } else {
                    formattedValue = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
                }
            }
            
            e.target.value = formattedValue;
        });

        // Emergency phone formatting
        document.getElementById('emergency_contact_phone').addEventListener('input', function(e) {
            var value = e.target.value.replace(/\D/g, '');
            var formattedValue = '';
            
            if (value.length > 0) {
                if (value.length <= 3) {
                    formattedValue = value;
                } else if (value.length <= 6) {
                    formattedValue = value.slice(0, 3) + '-' + value.slice(3);
                } else {
                    formattedValue = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
                }
            }
            
            e.target.value = formattedValue;
        });
    </script>
</body>
</html>
