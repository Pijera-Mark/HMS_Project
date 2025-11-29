<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .profile-picture-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }
        .profile-picture {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .profile-picture-upload {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #007bff;
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .profile-picture-upload:hover {
            background: #0056b3;
        }
        .completion-badge {
            background: #28a745;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f8f9fa;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
        }
        .info-value {
            color: #2c3e50;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .progress-ring {
            transform: rotate(-90deg);
        }
        .progress-ring-circle {
            transition: stroke-dashoffset 0.35s;
            stroke: #28a745;
            stroke-width: 4;
            fill: transparent;
        }
        .progress-ring-bg {
            stroke: #e9ecef;
            stroke-width: 4;
            fill: transparent;
        }
        .profile-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
        }
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #007bff;
        }
        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 0.25rem;
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
                        <a class="nav-link active" href="<?= base_url('profile') ?>">
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

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container text-center">
            <div class="profile-picture-container">
                <?php if ($profile && !empty($profile['profile_picture'])): ?>
                    <img src="<?= base_url($profile['profile_picture']) ?>" alt="Profile Picture" class="profile-picture">
                <?php else: ?>
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['name']) ?>&size=150&background=007bff&color=fff" alt="Profile Picture" class="profile-picture">
                <?php endif; ?>
                <label for="profile_picture" class="profile-picture-upload">
                    <i class="fas fa-camera"></i>
                </label>
                <input type="file" id="profile_picture" name="profile_picture" class="d-none" accept="image/*">
            </div>
            <h2 class="mt-3"><?= $user['name'] ?></h2>
            <p class="lead"><?= ucfirst($user['role']) ?></p>
            <div class="completion-badge">
                <i class="fas fa-check-circle me-2"></i>Profile Completion: <?= $profile_completion ?>%
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Profile Information -->
            <div class="col-lg-8">
                <div class="info-card">
                    <h4 class="mb-4">
                        <i class="fas fa-user-circle me-2"></i>Personal Information
                    </h4>
                    <div class="info-item">
                        <span class="info-label">Full Name</span>
                        <span class="info-value"><?= $user['name'] ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email Address</span>
                        <span class="info-value"><?= $user['email'] ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone Number</span>
                        <span class="info-value"><?= $user['phone'] ?: 'Not provided' ?></span>
                    </div>
                    <?php if ($profile): ?>
                        <div class="info-item">
                            <span class="info-label">Address</span>
                            <span class="info-value"><?= $profile['address'] ?: 'Not provided' ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">City</span>
                            <span class="info-value"><?= $profile['city'] ?: 'Not provided' ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Date of Birth</span>
                            <span class="info-value"><?= $profile['date_of_birth'] ? date('M d, Y', strtotime($profile['date_of_birth'])) : 'Not provided' ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Gender</span>
                            <span class="info-value"><?= $profile['gender'] ? ucfirst($profile['gender']) : 'Not provided' ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Blood Group</span>
                            <span class="info-value"><?= $profile['blood_group'] ?: 'Not provided' ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Emergency Contact</span>
                            <span class="info-value"><?= $profile['emergency_contact_name'] ?: 'Not provided' ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Emergency Contact -->
                <?php if ($profile && !empty($profile['emergency_contact_name'])): ?>
                <div class="info-card">
                    <h4 class="mb-4">
                        <i class="fas fa-phone-alt me-2"></i>Emergency Contact
                    </h4>
                    <div class="info-item">
                        <span class="info-label">Contact Name</span>
                        <span class="info-value"><?= $profile['emergency_contact_name'] ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Contact Phone</span>
                        <span class="info-value"><?= $profile['emergency_contact_phone'] ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Relationship</span>
                        <span class="info-value"><?= $profile['emergency_contact_relation'] ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Profile Completion -->
                <div class="info-card text-center">
                    <h5>Profile Completion</h5>
                    <div class="mt-3">
                        <svg width="120" height="120" class="progress-ring">
                            <circle class="progress-ring-bg" cx="60" cy="60" r="52"/>
                            <circle class="progress-ring-circle" cx="60" cy="60" r="52"
                                stroke-dasharray="<?= $profile_completion * 3.27 ?> 327"/>
                        </svg>
                        <div class="mt-2">
                            <strong><?= $profile_completion ?>%</strong>
                        </div>
                    </div>
                    <?php if ($profile_completion < 100): ?>
                        <p class="text-muted mt-3">Complete your profile to get the most out of HMS</p>
                        <a href="<?= base_url('profile/edit') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Complete Profile
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Quick Actions -->
                <div class="info-card">
                    <h5>Quick Actions</h5>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('profile/edit') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                        <a href="<?= base_url('profile/security') ?>" class="btn btn-outline-warning">
                            <i class="fas fa-shield-alt me-2"></i>Security Settings
                        </a>
                        <a href="<?= base_url('profile/statistics') ?>" class="btn btn-outline-info">
                            <i class="fas fa-chart-bar me-2"></i>View Statistics
                        </a>
                    </div>
                </div>

                <!-- Account Status -->
                <div class="info-card">
                    <h5>Account Status</h5>
                    <div class="profile-stats">
                        <div class="stat-card">
                            <div class="stat-number"><?= $user['status'] === 'active' ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>' ?></div>
                            <div class="stat-label">Status</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= $user['role'] ?></div>
                            <div class="stat-label">Role</div>
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
        // Profile picture upload
        document.getElementById('profile_picture').addEventListener('change', function(e) {
            if (e.target.files[0]) {
                const formData = new FormData();
                formData.append('profile_picture', e.target.files[0]);
                
                fetch('<?= base_url('profile/uploadPicture') ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error uploading profile picture');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error uploading profile picture');
                });
            }
        });
    </script>
</body>
</html>
