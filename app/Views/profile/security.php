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
                            <i class="fas fa-shield-alt me-2"></i>Security Settings
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Password Change Section -->
                        <div class="mb-5">
                            <h5 class="mb-3">
                                <i class="fas fa-key me-2"></i>Change Password
                            </h5>
                            <?= form_open('profile/updatePassword', ['class' => 'needs-validation', 'novalidate']) ?>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        <div class="invalid-feedback">Please enter your current password</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <div class="invalid-feedback">Please enter a new password</div>
                                        <small class="text-muted">Minimum 8 characters with letters, numbers, and special characters</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <div class="invalid-feedback">Passwords do not match</div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-lock me-2"></i>Change Password
                                </button>
                            <?= form_close() ?>
                        </div>

                        <hr>

                        <!-- Two-Factor Authentication -->
                        <div class="mb-5">
                            <h5 class="mb-3">
                                <i class="fas fa-mobile-alt me-2"></i>Two-Factor Authentication
                            </h5>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Two-factor authentication adds an extra layer of security to your account.
                                <strong>Feature coming soon!</strong>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="two_factor_auth" disabled>
                                <label class="form-check-label" for="two_factor_auth">
                                    Enable Two-Factor Authentication
                                </label>
                            </div>
                            <small class="text-muted">This feature is currently under development.</small>
                        </div>

                        <hr>

                        <!-- Notification Preferences -->
                        <div class="mb-5">
                            <h5 class="mb-3">
                                <i class="fas fa-bell me-2"></i>Notification Preferences
                            </h5>
                            <?= form_open('profile/updateNotifications') ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">Email Notifications</h6>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="preferences[email_notifications]" value="1" 
                                                   <?= ($notification_preferences['email_notifications'] ?? true) ? 'checked' : '' ?>>
                                            <label class="form-check-label">General Email Notifications</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="preferences[appointment_reminders]" value="1"
                                                   <?= ($notification_preferences['appointment_reminders'] ?? true) ? 'checked' : '' ?>>
                                            <label class="form-check-label">Appointment Reminders</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="preferences[test_results]" value="1"
                                                   <?= ($notification_preferences['test_results'] ?? true) ? 'checked' : '' ?>>
                                            <label class="form-check-label">Test Results</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="preferences[medication_reminders]" value="1"
                                                   <?= ($notification_preferences['medication_reminders'] ?? true) ? 'checked' : '' ?>>
                                            <label class="form-check-label">Medication Reminders</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="preferences[billing_alerts]" value="1"
                                                   <?= ($notification_preferences['billing_alerts'] ?? true) ? 'checked' : '' ?>>
                                            <label class="form-check-label">Billing Alerts</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">Other Notifications</h6>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="preferences[sms_notifications]" value="1"
                                                   <?= ($notification_preferences['sms_notifications'] ?? false) ? 'checked' : '' ?>>
                                            <label class="form-check-label">SMS Notifications</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="preferences[system_updates]" value="1"
                                                   <?= ($notification_preferences['system_updates'] ?? false) ? 'checked' : '' ?>>
                                            <label class="form-check-label">System Updates</label>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Preferences
                                </button>
                            <?= form_close() ?>
                        </div>

                        <hr>

                        <!-- Account Security Status -->
                        <div class="mb-5">
                            <h5 class="mb-3">
                                <i class="fas fa-info-circle me-2"></i>Account Security Status
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-check-circle text-success me-3"></i>
                                        <div>
                                            <strong>Password Strength</strong>
                                            <div class="text-muted">Your password meets security requirements</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-exclamation-triangle text-warning me-3"></i>
                                        <div>
                                            <strong>Two-Factor Authentication</strong>
                                            <div class="text-muted">Not enabled (coming soon)</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-check-circle text-success me-3"></i>
                                        <div>
                                            <strong>Email Verified</strong>
                                            <div class="text-muted">Your email is verified</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-check-circle text-success me-3"></i>
                                        <div>
                                            <strong>Active Sessions</strong>
                                            <div class="text-muted">1 active session</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Danger Zone -->
                        <?php if (($user['role'] ?? 'user') !== 'admin'): ?>
                        <div class="mb-3">
                            <h5 class="mb-3 text-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                            </h5>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Warning:</strong> Deleting your account is permanent and cannot be undone.
                                All your data will be removed from the system.
                            </div>
                            <button class="btn btn-outline-danger" onclick="confirmDeleteAccount()">
                                <i class="fas fa-trash me-2"></i>Delete Account
                            </button>
                        </div>
                        <?php endif; ?>
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

        // Password strength validation
        document.getElementById('new_password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strength = checkPasswordStrength(password);
            
            // Update password indicator
            let strengthText = '';
            let strengthClass = '';
            
            if (password.length === 0) {
                strengthText = '';
            } else if (strength < 3) {
                strengthText = 'Weak';
                strengthClass = 'text-danger';
            } else if (strength < 4) {
                strengthText = 'Fair';
                strengthClass = 'text-warning';
            } else {
                strengthText = 'Strong';
                strengthClass = 'text-success';
            }
            
            // Update UI (you can add a strength indicator element)
            console.log('Password strength:', strengthText);
        });

        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = e.target.value;
            
            if (confirmPassword && newPassword !== confirmPassword) {
                e.target.setCustomValidity('Passwords do not match');
            } else {
                e.target.setCustomValidity('');
            }
        });

        function checkPasswordStrength(password) {
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            
            // Character variety checks
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            return strength;
        }

        function confirmDeleteAccount() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone and all your data will be permanently removed.')) {
                const password = prompt('Please enter your password to confirm account deletion:');
                
                if (password) {
                    fetch('<?= base_url('api/profile') ?>', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + localStorage.getItem('token')
                        },
                        body: JSON.stringify({ password: password })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Account deleted successfully. Redirecting to login...');
                            localStorage.removeItem('token');
                            window.location.href = '<?= base_url('login') ?>';
                        } else {
                            alert('Error deleting account: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting account');
                    });
                }
            }
        }
    </script>
</body>
</html>
