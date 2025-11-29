<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css" rel="stylesheet">
    <link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
    <style>
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
            margin-bottom: 1rem;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .timeline-item {
            position: relative;
            padding-left: 40px;
            margin-bottom: 1.5rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #007bff;
        }
        .timeline-item::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 12px;
            width: 2px;
            height: calc(100% - 12px);
            background: #e9ecef;
        }
        .timeline-item:last-child::after {
            display: none;
        }
        .progress-ring {
            transform: rotate(-90deg);
        }
        .progress-ring-circle {
            transition: stroke-dashoffset 0.35s;
            stroke: #28a745;
            stroke-width: 8;
            fill: transparent;
        }
        .progress-ring-bg {
            stroke: #e9ecef;
            stroke-width: 8;
            fill: transparent;
        }
    </style>
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
                        <a class="nav-link active" href="<?= base_url('profile/statistics') ?>">
                            <i class="fas fa-chart-bar me-1"></i>Statistics
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
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2>
                    <i class="fas fa-chart-line me-2"></i>Profile Statistics
                </h2>
                <p class="text-muted">Track your activity and account statistics</p>
            </div>
        </div>

        <!-- Overview Stats -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-number"><?= $stats['profile_completion'] ?>%</div>
                    <div class="stat-label">Profile Completion</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <div class="stat-number"><?= $stats['login_count'] ?></div>
                    <div class="stat-label">Total Logins</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-number"><?= $stats['appointments_today'] ?? 0 ?></div>
                    <div class="stat-label">Today's Appointments</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number"><?= $stats['last_login'] ? 'Today' : 'Never' ?></div>
                    <div class="stat-label">Last Login</div>
                </div>
            </div>
        </div>

        <!-- Detailed Statistics -->
        <div class="row">
            <div class="col-lg-8">
                <!-- Profile Completion Chart -->
                <div class="chart-container">
                    <h5 class="mb-4">Profile Completion</h5>
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center">
                            <svg width="150" height="150" class="progress-ring">
                                <circle class="progress-ring-bg" cx="75" cy="75" r="65"/>
                                <circle class="progress-ring-circle" cx="75" cy="75" r="65"
                                    stroke-dasharray="<?= $stats['profile_completion'] * 4.08 ?> 408"/>
                            </svg>
                            <div class="mt-2">
                                <strong><?= $stats['profile_completion'] ?>%</strong>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6>Missing Information</h6>
                            <?php 
                            $missingFields = [];
                            if (empty($profile['address'])) $missingFields[] = 'Address';
                            if (empty($profile['city'])) $missingFields[] = 'City';
                            if (empty($profile['date_of_birth'])) $missingFields[] = 'Date of Birth';
                            if (empty($profile['gender'])) $missingFields[] = 'Gender';
                            if (empty($profile['blood_group'])) $missingFields[] = 'Blood Group';
                            if (empty($profile['emergency_contact_name'])) $missingFields[] = 'Emergency Contact';
                            
                            if (!empty($missingFields)): ?>
                                <ul class="list-unstyled">
                                    <?php foreach ($missingFields as $field): ?>
                                        <li><i class="fas fa-times text-danger me-2"></i><?= $field ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <a href="<?= base_url('profile/edit') ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit me-1"></i>Complete Profile
                                </a>
                            <?php else: ?>
                                <p class="text-success"><i class="fas fa-check-circle me-2"></i>Your profile is complete!</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Activity Timeline -->
                <div class="chart-container">
                    <h5 class="mb-4">Recent Activity</h5>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>Account Created</strong>
                                    <p class="text-muted mb-0">Your HMS account was created</p>
                                </div>
                                <small class="text-muted"><?= date('M d, Y', strtotime($stats['account_created'])) ?></small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>Last Login</strong>
                                    <p class="text-muted mb-0">You logged into your account</p>
                                </div>
                                <small class="text-muted"><?= $stats['last_login'] ? date('M d, Y H:i', strtotime($stats['last_login'])) : 'Never' ?></small>
                            </div>
                        </div>
                        <?php if ($user['role'] === 'doctor' && isset($stats['total_patients'])): ?>
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>Total Patients</strong>
                                    <p class="text-muted mb-0">You have <?= $stats['total_patients'] ?> patients</p>
                                </div>
                                <small class="text-muted">Current</small>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($user['role'] === 'patient' && isset($stats['medical_records'])): ?>
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>Medical Records</strong>
                                    <p class="text-muted mb-0">You have <?= $stats['medical_records'] ?> medical records</p>
                                </div>
                                <small class="text-muted">Current</small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Account Info -->
                <div class="chart-container">
                    <h5 class="mb-4">Account Information</h5>
                    <div class="mb-3">
                        <label class="text-muted">User ID</label>
                        <div class="fw-bold">#<?= $user['id'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Role</label>
                        <div class="fw-bold"><?= ucfirst($user['role']) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Status</label>
                        <div class="fw-bold">
                            <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'danger' ?>">
                                <?= ucfirst($user['status']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Member Since</label>
                        <div class="fw-bold"><?= date('M d, Y', strtotime($stats['account_created'])) ?></div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <?php if ($user['role'] === 'doctor'): ?>
                <div class="chart-container">
                    <h5 class="mb-4">Doctor Statistics</h5>
                    <div class="mb-3">
                        <label class="text-muted">Today's Appointments</label>
                        <div class="fw-bold"><?= $stats['appointments_today'] ?? 0 ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Total Patients</label>
                        <div class="fw-bold"><?= $stats['total_patients'] ?? 0 ?></div>
                    </div>
                </div>
                <?php elseif ($user['role'] === 'patient'): ?>
                <div class="chart-container">
                    <h5 class="mb-4">Patient Statistics</h5>
                    <div class="mb-3">
                        <label class="text-muted">Upcoming Appointments</label>
                        <div class="fw-bold"><?= $stats['upcoming_appointments'] ?? 0 ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Medical Records</label>
                        <div class="fw-bold"><?= $stats['medical_records'] ?? 0 ?></div>
                    </div>
                </div>
                <?php elseif ($user['role'] === 'receptionist'): ?>
                <div class="chart-container">
                    <h5 class="mb-4">Receptionist Statistics</h5>
                    <div class="mb-3">
                        <label class="text-muted">Today's Appointments</label>
                        <div class="fw-bold"><?= $stats['appointments_today'] ?? 0 ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Pending Registrations</label>
                        <div class="fw-bold"><?= $stats['pending_registrations'] ?? 0 ?></div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Security Overview -->
                <div class="chart-container">
                    <h5 class="mb-4">Security Overview</h5>
                    <div class="mb-3">
                        <label class="text-muted">Password Strength</label>
                        <div class="fw-bold text-success">
                            <i class="fas fa-check-circle me-1"></i>Strong
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Two-Factor Auth</label>
                        <div class="fw-bold text-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i>Not Enabled
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Email Verified</label>
                        <div class="fw-bold text-success">
                            <i class="fas fa-check-circle me-1"></i>Verified
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <script>
        // Animate progress ring on page load
        window.addEventListener('load', function() {
            const progressCircle = document.querySelector('.progress-ring-circle');
            if (progressCircle) {
                const radius = progressCircle.r.baseVal.value;
                const circumference = radius * 2 * Math.PI;
                progressCircle.style.strokeDasharray = `${circumference} ${circumference}`;
                progressCircle.style.strokeDashoffset = circumference;
                
                setTimeout(() => {
                    const percent = <?= $stats['profile_completion'] ?> / 100;
                    const offset = circumference - percent * circumference;
                    progressCircle.style.strokeDashoffset = offset;
                }, 100);
            }
        });
    </script>
</body>
</html>
