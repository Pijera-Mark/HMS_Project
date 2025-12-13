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
<style>
.completion-badge {
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

<!-- Profile Header -->
<div class="profile-header">
    <div class="container text-center">
        <div class="profile-picture-container">
            <?php if ($profile && !empty($profile['profile_picture'] ?? '')): ?>
                <img src="<?= base_url($profile['profile_picture'] ?? '') ?>" alt="Profile Picture" class="profile-picture">
            <?php else: ?>
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['name'] ?? 'User') ?>&size=150&background=007bff&color=fff" alt="Profile Picture" class="profile-picture">
            <?php endif; ?>
            <label for="profile_picture" class="profile-picture-upload">
                <i class="fas fa-camera"></i>
            </label>
            <input type="file" id="profile_picture" name="profile_picture" class="d-none" accept="image/*">
        </div>
        <h2 class="mt-3"><?= $user['name'] ?? 'User' ?></h2>
        <p class="lead"><?= ucfirst($user['role'] ?? 'user') ?></p>
        <div class="completion-badge">
            <i class="fas fa-check-circle me-2"></i>Profile Completion: <?= $profile_completion ?? 0 ?>%
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
                    <span class="info-value"><?= $user['name'] ?? 'Not available' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value"><?= $user['email'] ?? 'Not available' ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Role</span>
                    <span class="info-value"><?= ucfirst($user['role'] ?? 'user') ?></span>
                </div>
                <?php if (!empty($profile['phone'] ?? '')): ?>
                <div class="info-item">
                    <span class="info-label">Phone</span>
                    <span class="info-value"><?= esc($profile['phone']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($profile['address'] ?? '')): ?>
                <div class="info-item">
                    <span class="info-label">Address</span>
                    <span class="info-value"><?= esc($profile['address']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($profile['city'] ?? '')): ?>
                <div class="info-item">
                    <span class="info-label">City</span>
                    <span class="info-value"><?= esc($profile['city']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($profile['date_of_birth'] ?? '')): ?>
                <div class="info-item">
                    <span class="info-label">Date of Birth</span>
                    <span class="info-value"><?= esc($profile['date_of_birth']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($profile['gender'] ?? '')): ?>
                <div class="info-item">
                    <span class="info-label">Gender</span>
                    <span class="info-value"><?= ucfirst(esc($profile['gender'])) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($profile['blood_group'] ?? '')): ?>
                <div class="info-item">
                    <span class="info-label">Blood Group</span>
                    <span class="info-value"><?= esc($profile['blood_group']) ?></span>
                </div>
                <?php endif; ?>
                
                <div class="action-buttons">
                    <a href="/profile/edit" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i>Edit Profile
                    </a>
                    <a href="/profile/security" class="btn btn-outline-warning">
                        <i class="fas fa-shield-alt me-1"></i>Security Settings
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Profile Stats -->
        <div class="col-lg-4">
            <div class="info-card">
                <h4 class="mb-4">
                    <i class="fas fa-chart-line me-2"></i>Profile Statistics
                </h4>
                <div class="profile-stats">
                    <div class="stat-card">
                        <div class="stat-number"><?= $profile_completion ?? 0 ?>%</div>
                        <div class="stat-label">Profile Complete</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $user['id'] ?? 'N/A' ?></div>
                        <div class="stat-label">User ID</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= !empty($profile['updated_at']) ? date('M j, Y', strtotime($profile['updated_at'])) : 'Not set' ?></div>
                        <div class="stat-label">Last Updated</div>
                    </div>
                </div>
            </div>
            
            <!-- Emergency Contact -->
            <?php if (!empty($profile['emergency_contact_name'] ?? '')): ?>
            <div class="info-card">
                <h4 class="mb-4">
                    <i class="fas fa-phone-alt me-2"></i>Emergency Contact
                </h4>
                <div class="info-item">
                    <span class="info-label">Name</span>
                    <span class="info-value"><?= esc($profile['emergency_contact_name']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone</span>
                    <span class="info-value"><?= esc($profile['emergency_contact_phone']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Relation</span>
                    <span class="info-value"><?= esc($profile['emergency_contact_relation']) ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
