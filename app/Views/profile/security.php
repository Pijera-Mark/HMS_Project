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
<body class="sidebar-layout">
<!-- Sidebar -->
<?php if (session()->get('user')): ?>
<?= view('components/sidebar', ['user' => session()->get('user'), 'stats' => $stats ?? []]) ?>
<?php endif; ?>

<!-- Main Content -->
<div class="main-content" style="margin-left: 280px; min-height: 100vh; padding: 20px;">
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
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="preferences[security_alerts]" value="1"
                                                   <?= ($notification_preferences['security_alerts'] ?? true) ? 'checked' : '' ?>>
                                            <label class="form-check-label">Security Alerts</label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="preferences[maintenance_notices]" value="1"
                                                   <?= ($notification_preferences['maintenance_notices'] ?? true) ? 'checked' : '' ?>>
                                            <label class="form-check-label">Maintenance Notices</label>
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
                                            <strong>Account Active</strong>
                                            <div class="text-muted">Your account is in good standing</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Session Management -->
                        <div class="mb-5">
                            <h5 class="mb-3">
                                <i class="fas fa-laptop me-2"></i>Active Sessions
                            </h5>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Manage your active sessions and sign out from other devices.
                            </div>
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">Current Session</div>
                                        <small class="text-muted">This browser - <?= date('M j, Y, g:i A') ?></small>
                                    </div>
                                    <span class="badge bg-success">Active</span>
                                </div>
                                <!-- Additional sessions would be listed here -->
                            </div>
                            <button class="btn btn-outline-danger btn-sm mt-3" disabled>
                                <i class="fas fa-sign-out-alt me-2"></i>Sign Out All Other Sessions
                            </button>
                            <small class="text-muted d-block mt-2">Session management feature coming soon</small>
                        </div>

                        <hr>

                        <!-- Login Activity -->
                        <div class="mb-5">
                            <h5 class="mb-3">
                                <i class="fas fa-history me-2"></i>Recent Login Activity
                            </h5>
                            <div class="list-group">
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold">Current Login</div>
                                            <small class="text-muted">IP: <?= $_SERVER['REMOTE_ADDR'] ?? 'Unknown' ?> - <?= date('M j, Y, g:i A') ?></small>
                                        </div>
                                        <span class="badge bg-success">Current</span>
                                    </div>
                                </div>
                            </div>
                            <a href="/reports/audit" class="btn btn-outline-info btn-sm mt-3">
                                <i class="fas fa-chart-line me-2"></i>View Full Activity Log
                            </a>
                        </div>

                        <hr>

                        <!-- Account Recovery -->
                        <div class="mb-5">
                            <h5 class="mb-3">
                                <i class="fas fa-life-ring me-2"></i>Account Recovery Options
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card border-secondary">
                                        <div class="card-body text-center">
                                            <i class="fas fa-envelope fa-2x text-primary mb-3"></i>
                                            <h6 class="card-title">Email Recovery</h6>
                                            <p class="card-text small">Recover your account via email verification</p>
                                            <div class="badge bg-success">Configured</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-secondary">
                                        <div class="card-body text-center">
                                            <i class="fas fa-phone fa-2x text-muted mb-3"></i>
                                            <h6 class="card-title">Phone Recovery</h6>
                                            <p class="card-text small">Recover your account via SMS verification</p>
                                            <div class="badge bg-secondary">Not Available</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength checker
        document.getElementById('new_password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strength = checkPasswordStrength(password);
            
            // Update password strength indicator if needed
            if (password.length > 0) {
                if (strength >= 3) {
                    e.target.classList.remove('is-invalid');
                    e.target.classList.add('is-valid');
                } else {
                    e.target.classList.remove('is-valid');
                    e.target.classList.add('is-invalid');
                }
            }
        });

        // Password confirmation checker
        document.getElementById('confirm_password').addEventListener('input', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = e.target.value;
            
            if (confirmPassword.length > 0) {
                if (newPassword === confirmPassword) {
                    e.target.classList.remove('is-invalid');
                    e.target.classList.add('is-valid');
                } else {
                    e.target.classList.remove('is-valid');
                    e.target.classList.add('is-invalid');
                }
            }
        });

        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            return strength;
        }

        // Form validation
        (function () {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</div>
</body>
</html>
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
