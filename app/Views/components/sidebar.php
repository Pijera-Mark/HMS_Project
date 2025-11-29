<?php
// Helper function to check user role
function canAccess($userRole, $allowedRoles) {
    return in_array($userRole, $allowedRoles);
}

// Helper function to check if user is admin
function isAdmin($userRole) {
    return $userRole === 'admin';
}

// Helper function to check if user can manage patients
function canManagePatients($userRole) {
    return in_array($userRole, ['admin', 'receptionist', 'nurse', 'doctor']);
}

// Helper function to check if user can manage appointments
function canManageAppointments($userRole) {
    return in_array($userRole, ['admin', 'doctor', 'nurse', 'receptionist']);
}

// Helper function to check if user can manage admissions
function canManageAdmissions($userRole) {
    return in_array($userRole, ['admin', 'doctor', 'nurse']);
}

// Helper function to check if user can access medical records
function canAccessMedicalRecords($userRole) {
    return in_array($userRole, ['admin', 'doctor', 'nurse', 'pharmacist']);
}

// Helper function to check if user can manage prescriptions
function canManagePrescriptions($userRole) {
    return in_array($userRole, ['admin', 'doctor', 'pharmacist']);
}

// Helper function to check if user can access lab tests
function canAccessLabTests($userRole) {
    return in_array($userRole, ['admin', 'lab', 'doctor']);
}

// Helper function to check if user can manage medicines
function canManageMedicines($userRole) {
    return in_array($userRole, ['admin', 'pharmacist']);
}

// Helper function to check if user can manage billing
function canManageBilling($userRole) {
    return in_array($userRole, ['admin', 'accountant', 'receptionist']);
}

// Helper function to check if user can access reports
function canAccessReports($userRole) {
    return in_array($userRole, ['admin', 'doctor', 'accountant']);
}

// Helper function to check if user can access financial reports
function canAccessFinancialReports($userRole) {
    return in_array($userRole, ['admin', 'accountant']);
}

// Helper function to check if user can access patient reports
function canAccessPatientReports($userRole) {
    return in_array($userRole, ['admin', 'doctor']);
}

// Helper function to check if user can manage users
function canManageUsers($userRole) {
    return $userRole === 'admin';
}

// Helper function to check if user can manage branches
function canManageBranches($userRole) {
    return $userRole === 'admin';
}

// Helper function to check if user can manage settings
function canManageSettings($userRole) {
    return $userRole === 'admin';
}

// Helper function to check if user can manage wards
function canManageWards($userRole) {
    return in_array($userRole, ['admin', 'nurse']);
}

// Get current user role
$userRole = $user['role'] ?? 'guest';
?>
<div class="sidebar-container">
    <div class="sidebar-header">
        <div class="logo-section">
            <div class="logo">
                <i class="fas fa-hospital"></i>
            </div>
            <div class="logo-text">
                <h3>HMS</h3>
                <span>Hospital Management</span>
            </div>
        </div>
        <div class="user-profile">
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="user-info">
                <div class="user-name"><?= esc($user['name'] ?? 'User') ?></div>
                <div class="user-role"><?= ucfirst($user['role'] ?? 'Guest') ?></div>
                <?php if (!empty($user['branch_id'])): ?>
                    <div class="branch-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Branch <?= esc($user['branch_id']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="sidebar-menu">
        <div class="menu-section">
            <div class="menu-title">Main Navigation</div>
            
            <a href="/dashboard" class="menu-item <?= (current_url() === site_url('/dashboard')) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <div class="menu-text">Dashboard</div>
                <div class="menu-badge"><?= number_format($stats['today_appointments'] ?? 0) ?></div>
            </a>

            <?php if (canManagePatients($userRole)): ?>
            <a href="/patients" class="menu-item <?= (strpos(current_url(), '/patients') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-user-injured"></i>
                </div>
                <div class="menu-text">Patients</div>
                <div class="menu-badge"><?= number_format($stats['total_patients'] ?? 0) ?></div>
            </a>
            <?php endif; ?>

            <?php if (canManageAppointments($userRole)): ?>
            <a href="/appointments" class="menu-item <?= (strpos(current_url(), '/appointments') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="menu-text">Appointments</div>
                <div class="menu-badge"><?= number_format($stats['today_appointments'] ?? 0) ?></div>
            </a>
            <?php endif; ?>

            <?php if (canManageAdmissions($userRole)): ?>
            <a href="/admissions" class="menu-item <?= (strpos(current_url(), '/admissions') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-hospital-user"></i>
                </div>
                <div class="menu-text">Admissions</div>
                <div class="menu-badge"><?= number_format($stats['active_admissions'] ?? 0) ?></div>
            </a>
            <?php endif; ?>

            <?php if (canAccessMedicalRecords($userRole)): ?>
            <a href="/medical-records" class="menu-item <?= (strpos(current_url(), '/medical-records') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-file-medical"></i>
                </div>
                <div class="menu-text">Medical Records</div>
            </a>
            <?php endif; ?>

            <?php if (canManagePrescriptions($userRole)): ?>
            <a href="/prescriptions" class="menu-item <?= (strpos(current_url(), '/prescriptions') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-prescription"></i>
                </div>
                <div class="menu-text">Prescriptions</div>
            </a>
            <?php endif; ?>

            <?php if (canAccessLabTests($userRole)): ?>
            <a href="/lab-tests" class="menu-item <?= (strpos(current_url(), '/lab-tests') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-vial"></i>
                </div>
                <div class="menu-text">Lab Tests</div>
            </a>
            <?php endif; ?>
        </div>

        <?php if (canManageMedicines($userRole) || canManageBilling($userRole) || canManageWards($userRole)): ?>
        <div class="menu-section">
            <div class="menu-title">Operations</div>
            
            <?php if (canManageMedicines($userRole)): ?>
            <a href="/medicines" class="menu-item <?= (strpos(current_url(), '/medicines') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-pills"></i>
                </div>
                <div class="menu-text">Medicine Inventory</div>
                <?php if (($stats['low_stock_medicines'] ?? 0) > 0): ?>
                <div class="menu-badge danger"><?= number_format($stats['low_stock_medicines'] ?? 0) ?></div>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            <?php if (canManageBilling($userRole)): ?>
            <a href="/invoices" class="menu-item <?= (strpos(current_url(), '/invoices') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="menu-text">Billing & Invoices</div>
                <?php if (($stats['unpaid_invoices'] ?? 0) > 0): ?>
                <div class="menu-badge warning"><?= number_format($stats['unpaid_invoices'] ?? 0) ?></div>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            <?php if (canManageWards($userRole)): ?>
            <a href="/wards" class="menu-item <?= (strpos(current_url(), '/wards') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-bed"></i>
                </div>
                <div class="menu-text">Ward Management</div>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (canManageUsers($userRole) || canManageBranches($userRole) || canManageSettings($userRole)): ?>
        <div class="menu-section">
            <div class="menu-title">Administration</div>
            
            <a href="/doctors" class="menu-item <?= (strpos(current_url(), '/doctors') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="menu-text">Doctors</div>
                <div class="menu-badge"><?= number_format($stats['total_doctors'] ?? 0) ?></div>
            </a>

            <?php if (canManageUsers($userRole)): ?>
            <a href="/users" class="menu-item <?= (strpos(current_url(), '/users') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div class="menu-text">User Management</div>
            </a>
            <?php endif; ?>

            <?php if (canManageBranches($userRole)): ?>
            <a href="/branches" class="menu-item <?= (strpos(current_url(), '/branches') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-code-branch"></i>
                </div>
                <div class="menu-text">Branch Management</div>
            </a>
            <?php endif; ?>

            <?php if (canManageSettings($userRole)): ?>
            <a href="/settings" class="menu-item <?= (strpos(current_url(), '/settings') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <div class="menu-text">System Settings</div>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (canAccessReports($userRole)): ?>
        <div class="menu-section">
            <div class="menu-title">Reports</div>
            
            <?php if (canAccessFinancialReports($userRole)): ?>
            <a href="/reports/financial" class="menu-item <?= (strpos(current_url(), '/reports/financial') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="menu-text">Financial Reports</div>
            </a>
            <?php endif; ?>

            <?php if (canAccessPatientReports($userRole)): ?>
            <a href="/reports/patient" class="menu-item <?= (strpos(current_url(), '/reports/patient') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="menu-text">Patient Reports</div>
            </a>
            <?php endif; ?>

            <?php if (isAdmin($userRole)): ?>
            <a href="/reports/audit" class="menu-item <?= (strpos(current_url(), '/reports/audit') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="menu-text">Audit Trail</div>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="menu-section">
            <div class="menu-title">System</div>
            
            <a href="/profile" class="menu-item <?= (strpos(current_url(), '/profile') !== false && strpos(current_url(), '/profile/') === false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="menu-text">My Profile</div>
            </a>

            <a href="/profile/edit" class="menu-item <?= (strpos(current_url(), '/profile/edit') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <div class="menu-text">Edit Profile</div>
            </a>

            <a href="/profile/security" class="menu-item <?= (strpos(current_url(), '/profile/security') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="menu-text">Security Settings</div>
            </a>

            <a href="/help" class="menu-item <?= (strpos(current_url(), '/help') !== false) ? 'active' : '' ?>">
                <div class="menu-icon">
                    <i class="fas fa-question-circle"></i>
                </div>
                <div class="menu-text">Help & Support</div>
            </a>

            <a href="/logout" class="menu-item logout">
                <div class="menu-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <div class="menu-text">Logout</div>
            </a>
        </div>
    </div>

    <div class="sidebar-footer">
        <div class="system-info">
            <div class="version">HMS v2.0</div>
            <div class="status">
                <span class="status-dot online"></span>
                <span>System Online</span>
            </div>
        </div>
    </div>
</div>

<style>
.sidebar-container {
    width: 280px;
    height: 100vh;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    color: #2c3e50;
    position: fixed;
    left: 0;
    top: 0;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 1000;
    box-shadow: 8px 0 32px rgba(31, 38, 135, 0.15);
    border-right: 1px solid rgba(255, 255, 255, 0.18);
    transition: transform 0.3s ease;
}

.sidebar-header {
    padding: 25px 20px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    position: relative;
    overflow: hidden;
}

.sidebar-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: float 6s ease-in-out infinite;
}

.logo-section {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    position: relative;
    z-index: 1;
}

.logo {
    width: 45px;
    height: 45px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    font-weight: bold;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.logo-text {
    font-size: 1.3rem;
    font-weight: 700;
    color: white;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.logo-text h3 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 700;
    color: white;
}

.logo-text span {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.7);
    display: block;
    margin-top: -2px;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    backdrop-filter: blur(10px);
    color: white;
    transition: all 0.3s ease;
}

.profile-section {
    margin-top: 10px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    overflow: hidden;
}

.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    background: rgba(255, 255, 255, 0.05);
    cursor: pointer;
}

.profile-header h5 {
    margin: 0;
    font-size: 0.9rem;
    color: white;
    font-weight: 600;
}

.toggle-profile {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.7);
    cursor: pointer;
    padding: 2px;
    transition: transform 0.3s ease;
}

.toggle-profile:hover {
    color: white;
}

.toggle-profile.collapsed {
    transform: rotate(-90deg);
}

.profile-content {
    padding: 15px;
    color: white;
}

.profile-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    font-size: 0.85rem;
}

.profile-item label {
    font-weight: 500;
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
}

.profile-item span {
    color: white;
    font-weight: 400;
}

.status-badge {
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.active {
    background: rgba(40, 167, 69, 0.2);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.status-badge.inactive {
    background: rgba(220, 53, 69, 0.2);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.3);
}

.profile-actions {
    display: flex;
    gap: 8px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.profile-actions .btn {
    flex: 1;
    font-size: 0.75rem;
    padding: 6px 10px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.05);
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.profile-actions .btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
    color: white;
    text-decoration: none;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.8);
}

.user-info {
    flex: 1;
}

.user-name {
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 2px;
}

.user-role {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 2px;
}

.branch-info {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.6);
    display: flex;
    align-items: center;
    gap: 4px;
}

.sidebar-menu {
    padding: 20px 0;
}

.menu-section {
    margin-bottom: 25px;
}

.menu-title {
    padding: 0 20px;
    margin-bottom: 10px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #95a5a6;
}

/* Hide empty menu sections */
.menu-section:empty {
    display: none;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    color: #2c3e50;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border-radius: 0 25px 25px 0;
    margin: 2px 0;
}

.menu-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 0;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transition: width 0.3s ease;
    opacity: 0.1;
}

.menu-item:hover {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    padding-left: 25px;
    transform: translateX(5px);
}

.menu-item:hover::before {
    width: 100%;
}

.menu-item.active {
    background: linear-gradient(90deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
    color: #667eea;
    border-left: 4px solid #667eea;
    font-weight: 600;
    transform: translateX(5px);
}

.menu-item.logout {
    color: #e74c3c;
}

.menu-item.logout:hover {
    color: #c0392b;
    background: rgba(231, 76, 60, 0.1);
}

.menu-item.logout:hover::before {
    background: linear-gradient(90deg, #e74c3c, #c0392b);
}

.menu-icon {
    width: 20px;
    text-align: center;
    font-size: 1.1rem;
}

.menu-text {
    flex: 1;
    font-size: 0.9rem;
    font-weight: 500;
}

.menu-badge {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    min-width: 24px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    animation: badgePulse 2s ease-in-out infinite;
}

@keyframes badgePulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.menu-badge.danger {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
}

.menu-badge.warning {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
}

.sidebar-footer {
    padding: 20px;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    margin-top: auto;
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(10px);
}

.system-info {
    text-align: center;
}

.version {
    font-size: 0.8rem;
    color: #7f8c8d;
    margin-bottom: 8px;
    font-weight: 600;
}

.status {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 0.75rem;
    color: #2c3e50;
    font-weight: 500;
}

.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2ecc71, #27ae60);
    box-shadow: 0 0 10px rgba(46, 204, 113, 0.5);
    animation: statusPulse 2s infinite;
}

@keyframes statusPulse {
    0% {
        box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.7);
    }
    70% {
        box-shadow: 0 0 0 8px rgba(46, 204, 113, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(46, 204, 113, 0);
    }
}

/* Mobile Responsive */
.sidebar-toggle {
    display: none;
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 1001;
    background: #4facfe;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 10px;
    cursor: pointer;
    font-size: 1.2rem;
}

@media (max-width: 768px) {
    .sidebar-toggle {
        display: block;
    }
    
    .sidebar-container {
        transform: translateX(-100%);
    }
    
    .sidebar-container.open {
        transform: translateX(0);
    }
    
    .main-content {
        margin-left: 0 !important;
    }
}

/* Scrollbar Styling */
.sidebar-container::-webkit-scrollbar {
    width: 4px;
}

.sidebar-container::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}

.sidebar-container::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
}

.sidebar-container::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile sidebar toggle
    const sidebar = document.querySelector('.sidebar-container');
    const toggle = document.createElement('button');
    toggle.className = 'sidebar-toggle';
    toggle.innerHTML = '<i class="fas fa-bars"></i>';
    toggle.onclick = function() {
        sidebar.classList.toggle('open');
    };
    
    if (window.innerWidth <= 768) {
        document.body.appendChild(toggle);
    }
    
    window.addEventListener('resize', function() {
        if (window.innerWidth <= 768) {
            if (!document.querySelector('.sidebar-toggle')) {
                document.body.appendChild(toggle);
            }
        } else {
            const toggleBtn = document.querySelector('.sidebar-toggle');
            if (toggleBtn) {
                toggleBtn.remove();
            }
            sidebar.classList.remove('open');
        }
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        }
    });
    
    // Profile section toggle
    window.toggleProfileSection = function() {
        const content = document.getElementById('profileContent');
        const toggle = document.querySelector('.toggle-profile');
        
        if (content.style.display === 'none') {
            content.style.display = 'block';
            toggle.classList.remove('collapsed');
        } else {
            content.style.display = 'none';
            toggle.classList.add('collapsed');
        }
    };
});
</script>
