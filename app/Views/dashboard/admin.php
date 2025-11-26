<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<style>
/* Force override any conflicting styles */
.main-content {
    margin-left: 280px !important;
    padding: 20px !important;
    position: relative !important;
    width: calc(100% - 280px) !important;
    box-sizing: border-box !important;
}

/* Ensure sidebar is properly positioned */
.sidebar-container {
    position: fixed !important;
    left: 0 !important;
    top: 0 !important;
    width: 280px !important;
    z-index: 1000 !important;
}

/* Force dashboard positioning */
section {
    width: 100% !important;
    max-width: 100% !important;
    position: relative !important;
}

/* Enhanced UI Elements with Advanced Features */
.dashboard-wrapper {
    background: transparent;
    min-height: calc(100vh - 40px);
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box;
    opacity: 0;
    animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    position: relative;
    margin: 0 !important;
    padding: 0 !important;
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

/* Animated Background Particles */
.dashboard-wrapper::before {
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
    margin-bottom: 30px;
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

.dashboard-header::after {
    content: '';
    position: absolute;
    bottom: -20px;
    left: -20px;
    width: 100px;
    height: 100px;
    background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
    border-radius: 50%;
    animation: pulse 4s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.2); opacity: 0.8; }
}

.header-content {
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
    z-index: 1;
    flex: 1;
}

.dashboard-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: relative;
    z-index: 1;
}

.dashboard-title i {
    font-size: 2rem;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.header-stats {
    display: flex;
    gap: 25px;
    position: relative;
    z-index: 1;
    flex-wrap: wrap;
}

.stat-item {
    text-align: center;
    background: rgba(255, 255, 255, 0.15);
    padding: 12px 20px;
    border-radius: 12px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.stat-item:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
}

.stat-number {
    display: block;
    font-size: 1.4rem;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-label {
    font-size: 0.85rem;
    opacity: 0.9;
    font-weight: 500;
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

.main-dashboard {
    display: block;
    width: 100% !important;
    max-width: 100% !important;
    position: relative;
    margin: 0 !important;
    box-sizing: border-box;
}

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
    contain: layout style paint;
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

.stat-card::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(102, 126, 234, 0.05) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.stat-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 60px rgba(31, 38, 135, 0.25);
}

.stat-card:hover::after {
    opacity: 1;
}

.stat-card.primary::before {
    background: linear-gradient(90deg, #667eea, #764ba2);
}

.stat-card.success::before {
    background: linear-gradient(90deg, #2ecc71, #27ae60);
}

.stat-card.info::before {
    background: linear-gradient(90deg, #3498db, #2980b9);
}

.stat-card.warning::before {
    background: linear-gradient(90deg, #f39c12, #e67e22);
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

.stat-card.success .stat-icon {
    color: #2ecc71;
}

.stat-card.info .stat-icon {
    color: #3498db;
}

.stat-card.warning .stat-icon {
    color: #f39c12;
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

/* New Dashboard Sections */
.dashboard-sections {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
    margin-bottom: 30px;
    width: 100% !important;
    max-width: 100% !important;
    position: relative;
    box-sizing: border-box;
}

/* Activity Feed */
.activity-feed {
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

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 0;
    width: 100%;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    width: 100%;
    box-sizing: border-box;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-item:hover {
    background: rgba(102, 126, 234, 0.05);
    margin: 0 -10px;
    padding: 15px 10px;
    border-radius: 10px;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.activity-icon.success {
    background: linear-gradient(135deg, #2ecc71, #27ae60);
    color: white;
}

.activity-icon.info {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
}

.activity-icon.warning {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: white;
}

.activity-content {
    flex: 1;
    min-width: 0;
}

.activity-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 4px;
    font-size: 0.95rem;
}

.activity-time {
    font-size: 0.85rem;
    color: #7f8c8d;
}

/* Quick Actions */
.quick-actions {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 10px 40px rgba(31, 38, 135, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.18);
    width: 100%;
    box-sizing: border-box;
}

.action-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    width: 100%;
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

.action-btn.warning {
    background: linear-gradient(135deg, #f39c12, #e67e22);
}

/* Charts Section */
.charts-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 30px;
    width: 100% !important;
    max-width: 100% !important;
    position: relative;
    box-sizing: border-box;
}

.chart-container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 10px 40px rgba(31, 38, 135, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.18);
    width: 100%;
    box-sizing: border-box;
}

.chart-placeholder {
    height: 200px;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #667eea;
    font-weight: 600;
    font-size: 1.1rem;
    border: 2px dashed rgba(102, 126, 234, 0.3);
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    box-sizing: border-box;
}

.chart-placeholder:hover {
    background: rgba(102, 126, 234, 0.15);
    border-color: #667eea;
}

/* Dashboard Content Container */
.dashboard-content {
    width: 100%;
    position: relative;
    box-sizing: border-box;
}

.content-row {
    display: flex;
    gap: 25px;
    margin-bottom: 30px;
    width: 100% !important;
    max-width: 100% !important;
    flex-wrap: wrap;
    position: relative;
    box-sizing: border-box;
}

.content-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 10px 40px rgba(31, 38, 135, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.18);
    flex: 1;
    min-width: 300px;
    box-sizing: border-box;
}

.card-header {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.card-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-title i {
    color: #667eea;
    font-size: 1.1rem;
}

.card-content {
    width: 100%;
    box-sizing: border-box;
}

.overview-grid,
.financial-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    width: 100%;
}

.overview-item,
.financial-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    background: rgba(102, 126, 234, 0.05);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.overview-item:hover,
.financial-item:hover {
    background: rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
}

.overview-label,
.financial-label {
    font-size: 0.9rem;
    color: #7f8c8d;
    font-weight: 500;
}

.overview-value,
.financial-value {
    font-size: 1rem;
    font-weight: 700;
    color: #2c3e50;
}

.text-danger {
    color: #e74c3c !important;
}

.text-warning {
    color: #f39c12 !important;
}

.text-success {
    color: #2ecc71 !important;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .dashboard-header {
        padding: 25px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
    }
    
    .dashboard-sections {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .charts-section {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}

@media (max-width: 768px) {
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
    
    .header-stats {
        justify-content: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .header-actions {
        flex-direction: column;
        width: 100%;
        justify-content: center;
        gap: 10px;
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
    
    .action-buttons {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .dashboard-sections {
        gap: 15px;
    }
    
    .charts-section {
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
    .dashboard-header {
        padding: 15px;
        border-radius: 16px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 15px;
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
    
    .activity-feed,
    .quick-actions,
    .chart-container {
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

<div class="dashboard-wrapper">
	<div class="dashboard-header">
		<div class="header-content">
			<h1 class="dashboard-title">
				<i class="fas fa-tachometer-alt"></i>
				<?= (($user['role'] ?? null) === 'admin') ? 'Admin Dashboard' : 'Dashboard' ?>
			</h1>
			<div class="header-stats">
				<div class="stat-item">
					<span class="stat-number"><?= number_format($stats['today_appointments'] ?? 0) ?></span>
					<span class="stat-label">Today's Appointments</span>
				</div>
				<div class="stat-item">
					<span class="stat-number"><?= number_format($stats['active_admissions'] ?? 0) ?></span>
					<span class="stat-label">Active Admissions</span>
				</div>
			</div>
		</div>
		<div class="header-actions">
			<div class="current-date">
				<i class="fas fa-calendar"></i>
				<span id="currentDate"></span>
			</div>
			<a href="/logout" class="btn btn-outline-light btn-sm">
				<i class="fas fa-sign-out-alt"></i>
				Logout
			</a>
		</div>
	</div>

	<div class="main-dashboard">
		<div class="stats-grid">
			<div class="stat-card primary">
				<div class="stat-icon">
					<i class="fas fa-user-injured"></i>
				</div>
				<div class="stat-content">
					<h3 class="stat-number"><?= number_format($stats['total_patients'] ?? 0) ?></h3>
					<p class="stat-label">Total Patients</p>
				</div>
			</div>

			<div class="stat-card success">
				<div class="stat-icon">
					<i class="fas fa-user-md"></i>
				</div>
				<div class="stat-content">
					<h3 class="stat-number"><?= number_format($stats['total_doctors'] ?? 0) ?></h3>
					<p class="stat-label">Total Doctors</p>
				</div>
			</div>

			<div class="stat-card info">
				<div class="stat-icon">
					<i class="fas fa-calendar-check"></i>
				</div>
				<div class="stat-content">
					<h3 class="stat-number"><?= number_format($stats['today_appointments'] ?? 0) ?></h3>
					<p class="stat-label">Today's Appointments</p>
				</div>
			</div>

			<div class="stat-card warning">
				<div class="stat-icon">
					<i class="fas fa-hospital-user"></i>
				</div>
				<div class="stat-content">
					<h3 class="stat-number"><?= number_format($stats['active_admissions'] ?? 0) ?></h3>
					<p class="stat-label">Active Admissions</p>
				</div>
			</div>
		</div>

		<!-- New Enhanced Dashboard Sections -->
		<div class="dashboard-sections">
			<!-- Activity Feed -->
			<div class="activity-feed">
				<h3 class="section-title">
					<i class="fas fa-stream"></i>
					Recent Activity
				</h3>
				<div class="activity-list">
					<div class="activity-item">
						<div class="activity-icon success">
							<i class="fas fa-user-plus"></i>
						</div>
						<div class="activity-content">
							<div class="activity-title">New patient registered</div>
							<div class="activity-time">2 minutes ago</div>
						</div>
					</div>
					<div class="activity-item">
						<div class="activity-icon info">
							<i class="fas fa-calendar-check"></i>
						</div>
						<div class="activity-content">
							<div class="activity-title">Appointment scheduled</div>
							<div class="activity-time">15 minutes ago</div>
						</div>
					</div>
					<div class="activity-item">
						<div class="activity-icon warning">
							<i class="fas fa-pills"></i>
						</div>
						<div class="activity-content">
							<div class="activity-title">Low stock alert: Paracetamol</div>
							<div class="activity-time">1 hour ago</div>
						</div>
					</div>
					<div class="activity-item">
						<div class="activity-icon success">
							<i class="fas fa-file-invoice-dollar"></i>
						</div>
						<div class="activity-content">
							<div class="activity-title">Payment received</div>
							<div class="activity-time">2 hours ago</div>
						</div>
					</div>
					<div class="activity-item">
						<div class="activity-icon info">
							<i class="fas fa-hospital-user"></i>
						</div>
						<div class="activity-content">
							<div class="activity-title">Patient admitted</div>
							<div class="activity-time">3 hours ago</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Quick Actions -->
			<div class="quick-actions">
				<h3 class="section-title">
					<i class="fas fa-bolt"></i>
					Quick Actions
				</h3>
				<div class="action-buttons">
					<a href="/patients/new" class="action-btn success">
						<i class="fas fa-user-plus"></i>
						Add Patient
					</a>
					<a href="/appointments/new" class="action-btn info">
						<i class="fas fa-calendar-plus"></i>
						Book Appointment
					</a>
					<a href="/medicines" class="action-btn warning">
						<i class="fas fa-pills"></i>
						Medicine Inventory
					</a>
					<a href="/invoices/new" class="action-btn">
						<i class="fas fa-file-invoice"></i>
						Create Invoice
					</a>
				</div>
			</div>
		</div>

		<!-- Charts Section -->
		<div class="charts-section">
			<div class="chart-container">
				<h3 class="section-title">
					<i class="fas fa-chart-line"></i>
					Patient Statistics
				</h3>
				<div class="chart-placeholder">
					<i class="fas fa-chart-area"></i>
					Patient Growth Chart
				</div>
			</div>
			<div class="chart-container">
				<h3 class="section-title">
					<i class="fas fa-chart-pie"></i>
					Department Overview
				</h3>
				<div class="chart-placeholder">
					<i class="fas fa-chart-pie"></i>
					Department Distribution
				</div>
			</div>
		</div>

		<div class="dashboard-content">
			<div class="content-row">
				<div class="content-card">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-hospital"></i>
							Hospital Overview
						</h3>
					</div>
					<div class="card-content overview-grid">
						<div class="overview-item">
							<span class="overview-label">Total Appointments</span>
							<span class="overview-value"><?= number_format($stats['total_appointments'] ?? 0) ?></span>
						</div>
						<div class="overview-item">
							<span class="overview-label">Low Stock Medicines</span>
							<span class="overview-value text-danger"><?= number_format($stats['low_stock_medicines'] ?? 0) ?></span>
						</div>
						<div class="overview-item">
							<span class="overview-label">Expiring (30 days)</span>
							<span class="overview-value text-warning"><?= number_format($stats['expiring_medicines'] ?? 0) ?></span>
						</div>
						<div class="overview-item">
							<span class="overview-label">Unpaid Invoices</span>
							<span class="overview-value text-danger"><?= number_format($stats['unpaid_invoices'] ?? 0) ?></span>
						</div>
					</div>
				</div>

				<div class="content-card">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-coins"></i>
							Financial Overview
						</h3>
					</div>
					<div class="card-content financial-grid">
						<div class="financial-item">
							<span class="financial-label">Total Revenue</span>
							<span class="financial-value text-success">₱<?= number_format($stats['total_revenue'] ?? 0, 2) ?></span>
						</div>
						<div class="financial-item">
							<span class="financial-label">Pending Amount</span>
							<span class="financial-value text-warning">₱<?= number_format($stats['pending_amount'] ?? 0, 2) ?></span>
						</div>
						<div class="financial-item">
							<span class="financial-label">Unpaid Invoices</span>
							<span class="financial-value"><?= number_format($stats['unpaid_invoices'] ?? 0) ?></span>
						</div>
					</div>
				</div>
			</div>

			<div class="content-row">
				<div class="content-card full-width">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-history"></i>
							Recent Appointments
						</h3>
					</div>
					<div class="card-content">
						<?php if (!empty($recent_appointments)): ?>
							<div class="appointment-list">
								<?php foreach ($recent_appointments as $appointment): ?>
									<div class="appointment-item">
										<div class="appointment-info">
											<h4 class="appointment-title">
												Patient #<?= esc($appointment['patient_id'] ?? '-') ?>
												<span class="appointment-sub">Doctor #<?= esc($appointment['doctor_id'] ?? '-') ?></span>
											</h4>
											<p class="appointment-details">
												<i class="fas fa-clock"></i>
												<?= isset($appointment['scheduled_at']) ? date('M d, Y h:i A', strtotime($appointment['scheduled_at'])) : 'Not scheduled' ?>
											</p>
											<?php if (!empty($appointment['reason'])): ?>
												<p class="appointment-details">
													<i class="fas fa-notes-medical"></i>
													<?= esc($appointment['reason']) ?>
												</p>
											<?php endif; ?>
										</div>
										<div class="appointment-status-badge status-<?= esc($appointment['status'] ?? 'scheduled') ?>">
											<?= ucfirst($appointment['status'] ?? 'scheduled') ?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						<?php else: ?>
							<div class="empty-state">
								<i class="fas fa-calendar-times"></i>
								<p>No recent appointments</p>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
.dashboard-wrapper {
	padding: 20px;
	margin-left: 280px;
	min-height: 100vh;
	background: #f8f9fa;
}

.dashboard-header {
	background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
	color: white;
	padding: 25px;
	border-radius: 15px;
	margin-bottom: 30px;
	box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
	display: flex;
	justify-content: space-between;
	align-items: center;
	flex-wrap: wrap;
	gap: 20px;
}

.header-content {
	display: flex;
	align-items: center;
	gap: 30px;
}

.dashboard-title {
	font-size: 2rem;
	font-weight: 700;
	margin: 0;
	display: flex;
	align-items: center;
	gap: 10px;
}

.header-stats {
	display: flex;
	gap: 30px;
}

.stat-item {
	text-align: center;
}

.stat-item .stat-number {
	display: block;
	font-size: 1.5rem;
	font-weight: 700;
}

.stat-item .stat-label {
	font-size: 0.85rem;
	opacity: 0.85;
}

.header-actions {
	display: flex;
	align-items: center;
	gap: 15px;
}

.current-date {
	display: flex;
	align-items: center;
	gap: 8px;
	background: rgba(255, 255, 255, 0.1);
	padding: 10px 15px;
	border-radius: 8px;
	font-weight: 500;
}

.main-dashboard {
	display: flex;
	flex-direction: column;
	gap: 25px;
}

.stats-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
	gap: 20px;
}

.stat-card {
	background: white;
	border-radius: 15px;
	padding: 20px;
	display: flex;
	align-items: center;
	gap: 15px;
	box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
	border-left: 4px solid #4facfe;
	transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
	transform: translateY(-3px);
	box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.stat-card.primary {
	border-left-color: #4facfe;
}

.stat-card.success {
	border-left-color: #2ecc71;
}

.stat-card.info {
	border-left-color: #3498db;
}

.stat-card.warning {
	border-left-color: #f39c12;
}

.stat-icon {
	font-size: 2.4rem;
	color: #4facfe;
}

.stat-card.success .stat-icon {
	color: #2ecc71;
}

.stat-card.info .stat-icon {
	color: #3498db;
}

.stat-card.warning .stat-icon {
	color: #f39c12;
}

.stat-number {
	font-size: 2rem;
	font-weight: 700;
	margin: 0;
	color: #2c3e50;
}

.stat-label {
	margin: 4px 0 0 0;
	color: #7f8c8d;
	font-size: 0.95rem;
}

.dashboard-content {
	display: flex;
	flex-direction: column;
	gap: 25px;
}

.content-row {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 25px;
}

.content-card.full-width {
	grid-column: 1 / -1;
}

.content-card {
	background: white;
	border-radius: 15px;
	box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
	overflow: hidden;
}

.card-header {
	padding: 18px 22px;
	border-bottom: 1px solid #ecf0f1;
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.card-title {
	margin: 0;
	font-size: 1.15rem;
	font-weight: 600;
	color: #2c3e50;
	display: flex;
	align-items: center;
	gap: 10px;
}

.card-content {
	padding: 20px 22px;
}

.overview-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
	gap: 15px;
}

.overview-item {
	background: #f8f9fa;
	border-radius: 10px;
	padding: 15px;
}

.overview-label {
	display: block;
	font-size: 0.9rem;
	color: #7f8c8d;
	margin-bottom: 4px;
}

.overview-value {
	font-size: 1.3rem;
	font-weight: 600;
	color: #2c3e50;
}

.appointment-list {
	display: flex;
	flex-direction: column;
	gap: 12px;
}

.appointment-item {
	display: flex;
	justify-content: space-between;
	align-items: center;
	gap: 15px;
	padding: 12px 14px;
	border-radius: 10px;
	background: #f8f9fa;
}

.appointment-title {
	margin: 0 0 4px 0;
	font-size: 0.98rem;
	font-weight: 600;
	color: #2c3e50;
}

.appointment-sub {
	font-size: 0.85rem;
	color: #95a5a6;
	margin-left: 6px;
}

.appointment-details {
	margin: 0;
	font-size: 0.85rem;
	color: #7f8c8d;
	display: flex;
	align-items: center;
	gap: 6px;
}

.appointment-status-badge {
	padding: 4px 10px;
	border-radius: 999px;
	font-size: 0.78rem;
	font-weight: 600;
	text-transform: uppercase;
}

.appointment-status-badge.status-scheduled {
	background: #f1c40f;
	color: #fff;
}

.appointment-status-badge.status-confirmed {
	background: #27ae60;
	color: #fff;
}

.appointment-status-badge.status-completed {
	background: #3498db;
	color: #fff;
}

.appointment-status-badge.status-cancelled {
	background: #e74c3c;
	color: #fff;
}

.empty-state {
	text-align: center;
	padding: 25px 10px;
	color: #7f8c8d;
}

.empty-state i {
	font-size: 2.2rem;
	margin-bottom: 10px;
	opacity: 0.5;
}

.financial-grid {
	display: flex;
	flex-direction: column;
	gap: 10px;
}

.financial-item {
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.financial-label {
	font-size: 0.9rem;
	color: #7f8c8d;
}

.financial-value {
	font-size: 1.1rem;
	font-weight: 600;
}

.text-success {
	color: #27ae60 !important;
}

.text-warning {
	color: #f39c12 !important;
}

.text-danger {
	color: #e74c3c !important;
}

@media (max-width: 1200px) {
	.dashboard-wrapper {
		margin-left: 0;
		padding: 15px;
	}
	
	.content-row {
		grid-template-columns: 1fr;
	}
}

@media (max-width: 768px) {
	.dashboard-header {
		flex-direction: column;
		align-items: flex-start;
	}

	.header-content {
		flex-direction: column;
		align-items: flex-start;
		gap: 15px;
	}

	.stats-grid {
		grid-template-columns: 1fr;
	}

	.header-stats {
		gap: 20px;
	}
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var currentDateElement = document.getElementById('currentDate');
	if (currentDateElement) {
		var now = new Date();
		var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
		currentDateElement.textContent = now.toLocaleDateString('en-US', options);
	}
});
</script>

<script>
// Enhanced Dashboard Interactive Features
document.addEventListener('DOMContentLoaded', function() {
    // Set current date with dynamic updates
    const currentDateElement = document.getElementById('currentDate');
    if (currentDateElement) {
        updateDateTime();
        setInterval(updateDateTime, 60000); // Update every minute
    }
    
    function updateDateTime() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        currentDateElement.textContent = now.toLocaleDateString('en-US', options);
    }
    
    // Enhanced stat card interactions
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.style.minHeight = '100px';
        
        // Add click interaction
        card.addEventListener('click', function() {
            // Simulate loading state
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 200);
        });
        
        // Add ripple effect
        card.addEventListener('mouseenter', function(e) {
            createRipple(e, this);
        });
    });
    
    // Create ripple effect
    function createRipple(e, element) {
        const ripple = document.createElement('div');
        ripple.style.position = 'absolute';
        ripple.style.borderRadius = '50%';
        ripple.style.background = 'rgba(255, 255, 255, 0.5)';
        ripple.style.width = ripple.style.height = '20px';
        ripple.style.marginLeft = ripple.style.marginTop = '-10px';
        ripple.style.animation = 'ripple 0.6s ease-out';
        ripple.style.pointerEvents = 'none';
        
        const rect = element.getBoundingClientRect();
        ripple.style.left = (e.clientX - rect.left) + 'px';
        ripple.style.top = (e.clientY - rect.top) + 'px';
        
        element.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    }
    
    // Enhanced number animations with easing
    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(element => {
        const finalValue = parseInt(element.textContent.replace(/,/g, ''));
        if (!isNaN(finalValue)) {
            animateValue(element, 0, finalValue, 1500, 'easeOutQuart');
        }
    });
    
    function animateValue(element, start, end, duration, easing = 'linear') {
        const range = end - start;
        const startTime = performance.now();
        
        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const easedProgress = easingFunctions[easing](progress);
            const current = start + (range * easedProgress);
            
            element.textContent = Math.floor(current).toLocaleString();
            
            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }
        
        requestAnimationFrame(update);
    }
    
    // Easing functions
    const easingFunctions = {
        linear: t => t,
        easeOutQuart: t => 1 - Math.pow(1 - t, 4),
        easeOutCubic: t => 1 - Math.pow(1 - t, 3),
        easeOutBounce: t => {
            if (t < 1/2.75) {
                return 7.5625 * t * t;
            } else if (t < 2/2.75) {
                t -= 1.5/2.75;
                return 7.5625 * t * t + 0.75;
            } else if (t < 2.5/2.75) {
                t -= 2.25/2.75;
                return 7.5625 * t * t + 0.9375;
            } else {
                t -= 2.625/2.75;
                return 7.5625 * t * t + 0.984375;
            }
        }
    };
    
    // Activity feed interactions
    const activityItems = document.querySelectorAll('.activity-item');
    activityItems.forEach((item, index) => {
        // Staggered animation on load
        item.style.opacity = '0';
        item.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
            item.style.transition = 'all 0.5s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }, 100 * (index + 1));
        
        // Add click feedback
        item.addEventListener('click', function() {
            this.style.background = 'rgba(102, 126, 234, 0.1)';
            setTimeout(() => {
                this.style.background = '';
            }, 300);
        });
    });
    
    // Quick action buttons with ripple effect
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            createRipple(e, this);
            
            // Simulate navigation feedback
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 200);
        });
    });
    
    // Chart placeholder interactions
    const chartPlaceholders = document.querySelectorAll('.chart-placeholder');
    chartPlaceholders.forEach(placeholder => {
        placeholder.addEventListener('click', function() {
            // Simulate chart loading
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading chart data...';
            
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-chart-area"></i> Chart loaded successfully!';
                this.style.background = 'rgba(102, 126, 234, 0.1)';
                this.style.borderColor = '#667eea';
            }, 1500);
        });
    });
    
    // Notification system
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${getNotificationIcon(type)}"></i>
            <span>${message}</span>
            <button class="notification-close"><i class="fas fa-times"></i></button>
        `;
        
        // Add notification styles if not exists
        if (!document.querySelector('#notification-styles')) {
            const style = document.createElement('style');
            style.id = 'notification-styles';
            style.textContent = `
                .notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: white;
                    padding: 15px 20px;
                    border-radius: 12px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    z-index: 10000;
                    animation: slideIn 0.3s ease;
                    max-width: 300px;
                }
                .notification-info { border-left: 4px solid #3498db; }
                .notification-success { border-left: 4px solid #2ecc71; }
                .notification-warning { border-left: 4px solid #f39c12; }
                .notification-error { border-left: 4px solid #e74c3c; }
                .notification-close {
                    background: none;
                    border: none;
                    cursor: pointer;
                    margin-left: auto;
                    color: #7f8c8d;
                }
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(style);
        }
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
        
        // Manual close
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.remove();
        });
    }
    
    function getNotificationIcon(type) {
        const icons = {
            info: 'info-circle',
            success: 'check-circle',
            warning: 'exclamation-triangle',
            error: 'times-circle'
        };
        return icons[type] || 'info-circle';
    }
    
    // Simulate real-time updates
    setInterval(() => {
        const randomUpdates = [
            'New patient registered',
            'Appointment scheduled',
            'Payment received',
            'Medicine stock updated',
            'Lab test completed'
        ];
        
        const randomUpdate = randomUpdates[Math.floor(Math.random() * randomUpdates.length)];
        const types = ['info', 'success', 'warning'];
        const randomType = types[Math.floor(Math.random() * types.length)];
        
        // Uncomment to enable notifications
        // showNotification(randomUpdate, randomType);
    }, 30000); // Every 30 seconds
    
    // Loading overlay management
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        setTimeout(() => {
            loadingOverlay.style.opacity = '0';
            setTimeout(() => {
                loadingOverlay.style.display = 'none';
            }, 300);
        }, 500);
    }
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K for quick search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            showNotification('Quick search coming soon!', 'info');
        }
        
        // Ctrl/Cmd + N for new patient
        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            window.location.href = '/patients/new';
        }
    });
    
    // Performance monitoring
    if ('performance' in window) {
        window.addEventListener('load', function() {
            setTimeout(() => {
                const perfData = performance.getEntriesByType('navigation')[0];
                const loadTime = perfData.loadEventEnd - perfData.loadEventStart;
                console.log(`Dashboard loaded in ${loadTime}ms`);
            }, 0);
        });
    }
    
    // Add CSS for ripple animation
    const rippleStyle = document.createElement('style');
    rippleStyle.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(rippleStyle);
});
</script>

<?= $this->endSection() ?>
