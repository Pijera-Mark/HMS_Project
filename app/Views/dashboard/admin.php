<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<style>
/* Clean Dashboard Styles */
.dashboard-wrapper {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 16px;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.dashboard-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-stats {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.stat-item {
    text-align: center;
    background: rgba(255, 255, 255, 0.15);
    padding: 12px 20px;
    border-radius: 12px;
    backdrop-filter: blur(10px);
}

.stat-number {
    display: block;
    font-size: 1.4rem;
    font-weight: 700;
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
}

.current-date {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.stat-card.primary {
    border-left: 4px solid #667eea;
}

.stat-card.success {
    border-left: 4px solid #28a745;
}

.stat-card.info {
    border-left: 4px solid #17a2b8;
}

.stat-card.warning {
    border-left: 4px solid #ffc107;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
}

.stat-card.primary .stat-icon {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.stat-card.success .stat-icon {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.stat-card.info .stat-icon {
    background: linear-gradient(135deg, #17a2b8, #007bff);
}

.stat-card.warning .stat-icon {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
}

.stat-content h3 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 5px 0;
    color: #2c3e50;
}

.stat-content p {
    margin: 0;
    color: #6c757d;
    font-weight: 500;
}

.dashboard-sections {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
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

.activity-feed, .quick-actions {
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
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
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.activity-icon.success {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.activity-icon.info {
    background: linear-gradient(135deg, #17a2b8, #007bff);
}

.activity-icon.warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
}

.activity-icon.danger {
    background: linear-gradient(135deg, #dc3545, #e74c3c);
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 4px;
}

.activity-time {
    font-size: 0.85rem;
    color: #6c757d;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.quick-action-btn {
    padding: 20px;
    background: #f8f9fa;
    border: 2px solid transparent;
    border-radius: 12px;
    text-decoration: none;
    color: #2c3e50;
    transition: all 0.3s ease;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.quick-action-btn:hover {
    background: white;
    border-color: #667eea;
    color: #667eea;
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
}

.quick-action-btn i {
    font-size: 1.5rem;
}

.quick-action-btn span {
    font-weight: 600;
    font-size: 0.9rem;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .dashboard-sections {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
}

@media (max-width: 768px) {
    .dashboard-wrapper {
        padding: 15px;
    }
    
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
        padding: 20px;
    }
    
    .header-stats {
        width: 100%;
        justify-content: space-between;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions-grid {
        grid-template-columns: 1fr;
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
						<div class="activity-title">Medicine inventory updated</div>
						<div class="activity-time">1 hour ago</div>
					</div>
				</div>
				<div class="activity-item">
					<div class="activity-icon danger">
						<i class="fas fa-file-invoice"></i>
					</div>
					<div class="activity-content">
						<div class="activity-title">New invoice generated</div>
						<div class="activity-time">2 hours ago</div>
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
			<div class="quick-actions-grid">
				<a href="/patients/create" class="quick-action-btn">
					<i class="fas fa-user-plus"></i>
					<span>Add Patient</span>
				</a>
				<a href="/appointments/create" class="quick-action-btn">
					<i class="fas fa-calendar-plus"></i>
					<span>New Appointment</span>
				</a>
				<a href="/patients" class="quick-action-btn">
					<i class="fas fa-search"></i>
					<span>View Patients</span>
				</a>
				<a href="/reports" class="quick-action-btn">
					<i class="fas fa-chart-bar"></i>
					<span>Reports</span>
				</a>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set current date
    const currentDate = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('currentDate').textContent = currentDate.toLocaleDateString('en-US', options);
});
</script>

<?= $this->endSection() ?>
