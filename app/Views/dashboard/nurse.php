<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<style>
/* Enhanced Nurse Dashboard Styles */
.dashboard-container {
    padding: 20px;
    background: transparent;
    min-height: 100vh;
    width: 100%;
    box-sizing: border-box;
    opacity: 0;
    animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    position: relative;
    margin: 0 auto;
    max-width: 100%;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

/* Animated Background Particles */
.dashboard-container::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(118, 75, 162, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 40% 20%, rgba(240, 147, 251, 0.05) 0%, transparent 50%);
    animation: particleFloat 20s ease-in-out infinite;
    pointer-events: none;
    z-index: -1;
}

@keyframes particleFloat {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    33% { transform: translate(30px, -30px) rotate(120deg); }
    66% { transform: translate(-20px, 20px) rotate(240deg); }
}

.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 24px;
    margin: 0 0 30px 0;
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.25);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 25px;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    width: 100%;
    box-sizing: border-box;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: float 8s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.header-content {
    display: flex;
    align-items: center;
    gap: 25px;
    position: relative;
    z-index: 1;
    flex: 1;
}

.dashboard-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 15px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.dashboard-title i {
    font-size: 2.2rem;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.user-info {
    display: flex;
    align-items: center;
    gap: 15px;
    background: rgba(255, 255, 255, 0.15);
    padding: 15px 20px;
    border-radius: 16px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.user-info:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
}

.user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.user-details {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: 2px;
}

.user-role {
    font-size: 0.85rem;
    opacity: 0.9;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 15px;
    position: relative;
    z-index: 1;
    flex-shrink: 0;
}

.current-date {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.15);
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 600;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.current-date:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
}

.current-date i {
    font-size: 1.1rem;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
    width: 100%;
    position: relative;
}

.stat-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 18px;
    box-shadow: 0 10px 40px rgba(31, 38, 135, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.18);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    min-height: 100px;
    position: relative;
    overflow: hidden;
    cursor: pointer;
    width: 100%;
    box-sizing: border-box;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
    background-size: 300% 100%;
    animation: gradientShift 4s ease-in-out infinite;
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.stat-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 60px rgba(31, 38, 135, 0.25);
}

.stat-icon {
    font-size: 2.5rem;
    color: #667eea;
    position: relative;
    z-index: 1;
    animation: iconFloat 3s ease-in-out infinite;
    transition: transform 0.3s ease;
    flex-shrink: 0;
}

.stat-card:hover .stat-icon {
    transform: scale(1.1) rotate(5deg);
}

@keyframes iconFloat {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-5px); }
}

.stat-content {
    flex: 1;
    min-width: 0;
}

.stat-content h3 {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    color: #2c3e50;
    position: relative;
    z-index: 1;
    transition: color 0.3s ease;
}

.stat-card:hover .stat-content h3 {
    color: #667eea;
}

.stat-content p {
    margin: 6px 0 0 0;
    color: #7f8c8d;
    font-size: 0.9rem;
    font-weight: 500;
    transition: color 0.3s ease;
}

.stat-card:hover .stat-content p {
    color: #95a5a6;
}

/* Content Sections */
.content-sections {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
    width: 100%;
    position: relative;
}

.content-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 10px 40px rgba(31, 38, 135, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.18);
    width: 100%;
    box-sizing: border-box;
}

.section-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    color: #667eea;
    font-size: 1.2rem;
}

/* Task List */
.task-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.task-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: rgba(102, 126, 234, 0.05);
    border-radius: 12px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.task-item:hover {
    background: rgba(102, 126, 234, 0.1);
    transform: translateX(5px);
}

.task-status {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.task-status.pending {
    background: #f39c12;
}

.task-status.completed {
    background: #2ecc71;
}

.task-status.urgent {
    background: #e74c3c;
}

.task-details {
    flex: 1;
}

.task-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 4px;
    font-size: 0.95rem;
}

.task-time {
    font-size: 0.85rem;
    color: #7f8c8d;
}

/* Quick Actions */
.quick-actions-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.action-btn {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
    width: 100%;
    box-sizing: border-box;
    min-height: 44px;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.action-btn.success {
    background: linear-gradient(135deg, #2ecc71, #27ae60);
}

.action-btn.info {
    background: linear-gradient(135deg, #3498db, #2980b9);
}

/* Responsive Design */
@media (max-width: 1200px) {
    .dashboard-container {
        padding: 15px;
    }
    
    .content-sections {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
    }
    
    .dashboard-header {
        padding: 25px;
    }
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 10px;
    }
    
    .dashboard-header {
        padding: 20px;
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .stat-card {
        padding: 20px;
        min-height: 90px;
    }
    
    .stat-icon {
        font-size: 2rem;
    }
    
    .stat-content h3 {
        font-size: 1.5rem;
    }
    
    .quick-actions-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .content-sections {
        gap: 15px;
    }
    
    .dashboard-title {
        font-size: 1.5rem;
    }
    
    .dashboard-title i {
        font-size: 1.8rem;
    }
    
    .section-title {
        font-size: 1.1rem;
    }
}

@media (max-width: 480px) {
    .dashboard-container {
        padding: 5px;
    }
    
    .dashboard-header {
        padding: 15px;
        border-radius: 16px;
    }
    
    .stat-card {
        padding: 15px;
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    .stat-icon {
        font-size: 1.8rem;
    }
    
    .content-card {
        padding: 20px;
        border-radius: 16px;
    }
    
    .action-btn {
        padding: 10px 14px;
        font-size: 0.85rem;
    }
    
    .dashboard-title {
        font-size: 1.3rem;
    }
    
    .dashboard-title i {
        font-size: 1.5rem;
    }
}
</style>
<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <h1 class="dashboard-title">
                <i class="fas fa-user-nurse"></i>
                Nurse Dashboard
            </h1>
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user-nurse"></i>
                </div>
                <div class="user-details">
                    <span class="user-name">Nurse <?= esc($user['name']) ?></span>
                    <span class="user-role">Healthcare Professional</span>
                </div>
            </div>
        </div>
        <div class="header-actions">
            <div class="current-date">
                <i class="fas fa-calendar"></i>
                <span id="currentDate"></span>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card patients">
            <div class="stat-icon">
                <i class="fas fa-user-injured"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?= number_format($total_patients) ?></h3>
                <p class="stat-label">Total Patients</p>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span>+12%</span>
                </div>
            </div>
        </div>

        <div class="stat-card occupied-wards">
            <div class="stat-icon">
                <i class="fas fa-bed"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?= number_format($occupied_wards) ?></h3>
                <p class="stat-label">Occupied Wards</p>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span>+5%</span>
                </div>
            </div>
        </div>

        <div class="stat-card available-wards">
            <div class="stat-icon">
                <i class="fas fa-procedures"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?= number_format($available_wards) ?></h3>
                <p class="stat-label">Available Wards</p>
                <div class="stat-trend">
                    <i class="fas fa-arrow-down"></i>
                    <span>-2%</span>
                </div>
            </div>
        </div>

        <div class="stat-card pending-tests">
            <div class="stat-icon">
                <i class="fas fa-flask"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number"><?= number_format($pending_lab_tests) ?></h3>
                <p class="stat-label">Pending Lab Tests</p>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span>+8%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-grid">
        <!-- Recent Admissions -->
        <div class="dashboard-card recent-admissions">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-hospital-user"></i>
                    Recent Admissions
                </h3>
                <a href="/admissions" class="view-all">View All</a>
            </div>
            <div class="card-content">
                <?php if (!empty($recent_admissions)): ?>
                    <div class="admission-list">
                        <?php foreach ($recent_admissions as $admission): ?>
                            <div class="admission-item">
                                <div class="admission-icon">
                                    <i class="fas fa-hospital-user"></i>
                                </div>
                                <div class="admission-info">
                                    <h4 class="admission-title">Patient #<?= $admission['id'] ?></h4>
                                    <p class="admission-details">
                                        <i class="fas fa-calendar-plus"></i>
                                        Admitted: <?= date('M d, Y', strtotime($admission['admission_date'])) ?>
                                    </p>
                                    <p class="admission-details">
                                        <i class="fas fa-procedures"></i>
                                        Ward: <?= esc($admission['ward_name'] ?? 'Not assigned') ?>
                                    </p>
                                    <div class="admission-status status-active">
                                        Active Admission
                                    </div>
                                </div>
                                <div class="admission-actions">
                                    <a href="/admissions/show/<?= $admission['id'] ?>" class="btn-view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/medical-records/new?patient_id=<?= $admission['id'] ?>" class="btn-record">
                                        <i class="fas fa-file-medical"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-hospital"></i>
                        <p>No recent admissions</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ward Status -->
        <div class="dashboard-card ward-status">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bed"></i>
                    Ward Status
                </h3>
                <a href="/wards" class="view-all">View All</a>
            </div>
            <div class="card-content">
                <div class="ward-stats">
                    <div class="ward-stat">
                        <div class="stat-circle occupied">
                            <span class="stat-number"><?= $occupied_wards ?></span>
                        </div>
                        <div class="stat-info">
                            <h4>Occupied</h4>
                            <p>Wards in use</p>
                        </div>
                    </div>

                    <div class="ward-stat">
                        <div class="stat-circle available">
                            <span class="stat-number"><?= $available_wards ?></span>
                        </div>
                        <div class="stat-info">
                            <h4>Available</h4>
                            <p>Ready for patients</p>
                        </div>
                    </div>

                    <div class="ward-stat">
                        <div class="stat-circle total">
                            <span class="stat-number"><?= $total_wards ?></span>
                        </div>
                        <div class="stat-info">
                            <h4>Total</h4>
                            <p>All wards</p>
                        </div>
                    </div>
                </div>

                <div class="occupancy-rate">
                    <div class="occupancy-label">
                        <span>Occupancy Rate</span>
                        <span class="occupancy-percentage">
                            <?= $total_wards > 0 ? round(($occupied_wards / $total_wards) * 100) : 0 ?>%
                        </span>
                    </div>
                    <div class="occupancy-bar">
                        <div class="occupancy-fill" style="width: <?= $total_wards > 0 ? ($occupied_wards / $total_wards) * 100 : 0 ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-card quick-actions">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-content">
                <div class="action-grid">
                    <a href="/patients/new" class="action-item">
                        <div class="action-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <span class="action-label">Register Patient</span>
                    </a>

                    <a href="/admissions/new" class="action-item">
                        <div class="action-icon">
                            <i class="fas fa-hospital-user"></i>
                        </div>
                        <span class="action-label">New Admission</span>
                    </a>

                    <a href="/lab-tests/new" class="action-item">
                        <div class="action-icon">
                            <i class="fas fa-flask"></i>
                        </div>
                        <span class="action-label">Order Lab Test</span>
                    </a>

                    <a href="/medical-records" class="action-item">
                        <div class="action-icon">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <span class="action-label">Medical Records</span>
                    </a>

                    <a href="/wards" class="action-item">
                        <div class="action-icon">
                            <i class="fas fa-bed"></i>
                        </div>
                        <span class="action-label">Manage Wards</span>
                    </a>

                    <a href="/account" class="action-item">
                        <div class="action-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="action-label">My Profile</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Lab Tests Overview -->
        <div class="dashboard-card lab-tests">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-flask"></i>
                    Lab Tests Overview
                </h3>
                <a href="/lab-tests" class="view-all">View All</a>
            </div>
            <div class="card-content">
                <div class="lab-stats">
                    <div class="lab-stat">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h4><?= number_format($pending_lab_tests) ?></h4>
                            <p>Pending Tests</p>
                        </div>
                    </div>

                    <div class="lab-stat">
                        <div class="stat-icon completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h4><?= number_format($completed_lab_tests) ?></h4>
                            <p>Completed Tests</p>
                        </div>
                    </div>
                </div>

                <div class="test-status-distribution">
                    <h4>Test Status Distribution</h4>
                    <div class="status-bars">
                        <div class="status-bar">
                            <span class="status-label">Pending</span>
                            <div class="status-bar-fill pending" style="width: <?= ($pending_lab_tests + $completed_lab_tests) > 0 ? ($pending_lab_tests / ($pending_lab_tests + $completed_lab_tests)) * 100 : 0 ?>%"></div>
                            <span class="status-percentage"><?= ($pending_lab_tests + $completed_lab_tests) > 0 ? round(($pending_lab_tests / ($pending_lab_tests + $completed_lab_tests)) * 100) : 0 ?>%</span>
                        </div>
                        <div class="status-bar">
                            <span class="status-label">Completed</span>
                            <div class="status-bar-fill completed" style="width: <?= ($pending_lab_tests + $completed_lab_tests) > 0 ? ($completed_lab_tests / ($pending_lab_tests + $completed_lab_tests)) * 100 : 0 ?>%"></div>
                            <span class="status-percentage"><?= ($pending_lab_tests + $completed_lab_tests) > 0 ? round(($completed_lab_tests / ($pending_lab_tests + $completed_lab_tests)) * 100) : 0 ?>%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-container {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-header {
    background: linear-gradient(135deg, #e91e63 0%, #c2185b 100%);
    color: white;
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.dashboard-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-avatar {
    font-size: 3rem;
    color: rgba(255,255,255,0.8);
}

.user-details {
    text-align: right;
}

.user-name {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0;
    display: block;
}

.user-role {
    font-size: 0.9rem;
    opacity: 0.8;
    margin: 5px 0 0 0;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 20px;
}

.current-date {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.1);
    padding: 10px 15px;
    border-radius: 8px;
    font-weight: 500;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-left: 4px solid;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-card.patients { border-left-color: #3498db; }
.stat-card.occupied-wards { border-left-color: #e74c3c; }
.stat-card.available-wards { border-left-color: #27ae60; }
.stat-card.pending-tests { border-left-color: #f39c12; }

.stat-icon {
    font-size: 3rem;
    opacity: 0.8;
}

.stat-card.patients .stat-icon { color: #3498db; }
.stat-card.occupied-wards .stat-icon { color: #e74c3c; }
.stat-card.available-wards .stat-icon { color: #27ae60; }
.stat-card.pending-tests .stat-icon { color: #f39c12; }

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    color: #2c3e50;
}

.stat-label {
    font-size: 1rem;
    color: #7f8c8d;
    margin: 5px 0 10px 0;
    font-weight: 500;
}

.stat-trend {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.9rem;
    font-weight: 600;
}

.stat-trend i.fa-arrow-up { color: #27ae60; }
.stat-trend i.fa-minus { color: #e74c3c; }

.dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
}

.dashboard-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}

.card-header {
    padding: 20px 25px;
    border-bottom: 1px solid #ecf0f1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.view-all {
    color: #3498db;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.view-all:hover {
    color: #2980b9;
}

.card-content {
    padding: 25px;
}

.admission-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.admission-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    transition: background 0.3s ease;
}

.admission-item:hover {
    background: #e9ecef;
}

.admission-icon {
    font-size: 2rem;
    color: #6c757d;
}

.admission-info {
    flex: 1;
}

.admission-title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 5px 0;
    color: #2c3e50;
}

.admission-details {
    font-size: 0.85rem;
    color: #6c757d;
    margin: 2px 0;
    display: flex;
    align-items: center;
    gap: 5px;
}

.admission-actions {
    display: flex;
    gap: 10px;
}

.btn-view, .btn-record {
    color: #3498db;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.btn-view:hover {
    background: #3498db;
    color: white;
}

.btn-record {
    color: #27ae60;
}

.btn-record:hover {
    background: #27ae60;
    color: white;
}

.admission-status {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-top: 5px;
    background: #27ae60;
    color: white;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 1rem;
}

.ward-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.ward-stat {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
}

.stat-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
}

.stat-circle.occupied { background: #e74c3c; }
.stat-circle.available { background: #27ae60; }
.stat-circle.total { background: #3498db; }

.stat-info h4 {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0 0 5px 0;
    color: #2c3e50;
}

.stat-info p {
    font-size: 0.9rem;
    color: #6c757d;
    margin: 0;
}

.occupancy-rate {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
}

.occupancy-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    font-weight: 600;
    color: #2c3e50;
}

.occupancy-percentage {
    color: #3498db;
    font-weight: 700;
}

.occupancy-bar {
    width: 100%;
    height: 8px;
    background: #ecf0f1;
    border-radius: 4px;
    overflow: hidden;
}

.occupancy-fill {
    height: 100%;
    background: linear-gradient(90deg, #27ae60 0%, #3498db 100%);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.action-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.action-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    text-decoration: none;
    color: #2c3e50;
    transition: all 0.3s ease;
}

.action-item:hover {
    background: #e91e63;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(233, 30, 99, 0.3);
}

.action-icon {
    font-size: 1.5rem;
    color: #6c757d;
}

.action-item:hover .action-icon {
    color: white;
}

.action-label {
    font-weight: 500;
}

.lab-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.lab-stat {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
}

.stat-icon.pending {
    font-size: 2rem;
    color: #f39c12;
}

.stat-icon.completed {
    font-size: 2rem;
    color: #27ae60;
}

.test-status-distribution h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 15px 0;
}

.status-bars {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.status-bar {
    display: flex;
    align-items: center;
    gap: 10px;
}

.status-label {
    min-width: 80px;
    font-size: 0.9rem;
    font-weight: 500;
    color: #2c3e50;
}

.status-bar-fill {
    flex: 1;
    height: 8px;
    border-radius: 4px;
}

.status-bar-fill.pending { background: #f39c12; }
.status-bar-fill.completed { background: #27ae60; }

.status-percentage {
    min-width: 40px;
    text-align: right;
    font-size: 0.9rem;
    font-weight: 600;
    color: #6c757d;
}

@media (max-width: 1024px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }

    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }

    .ward-stats {
        grid-template-columns: 1fr;
    }

    .lab-stats {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 15px;
    }

    .dashboard-header {
        padding: 20px;
    }

    .header-content {
        flex-direction: column;
        text-align: center;
    }

    .dashboard-title {
        font-size: 1.5rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .stat-card {
        padding: 20px;
    }

    .stat-number {
        font-size: 2rem;
    }

    .action-grid {
        grid-template-columns: 1fr;
    }

    .card-content {
        padding: 20px;
    }

    .ward-stat {
        padding: 15px;
    }

    .stat-circle {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
}
</style>

<script>
// Update current date
document.addEventListener('DOMContentLoaded', function() {
    const currentDateElement = document.getElementById('currentDate');
    const now = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    currentDateElement.textContent = now.toLocaleDateString('en-US', options);
});
</script>
<?= $this->endSection() ?>
