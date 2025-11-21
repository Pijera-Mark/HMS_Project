<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="dashboard-container">
	<div class="dashboard-header">
		<div class="header-content">
			<h1 class="dashboard-title">
				<i class="fas fa-tachometer-alt"></i>
				Dashboard
			</h1>
			<div class="user-info">
				<div class="user-avatar">
					<i class="fas fa-user-circle"></i>
				</div>
				<div class="user-details">
					<span class="user-name"><?= esc($user['name'] ?? 'User') ?></span>
					<span class="user-role"><?= ucfirst($user['role'] ?? 'user') ?></span>
				</div>
			</div>
		</div>
		<div class="header-actions">
			<div class="current-date">
				<i class="fas fa-calendar"></i>
				<span id="currentDate"></span>
			</div>
			<a href="/logout" class="btn btn-light btn-sm">
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

	<div class="dashboard-grid">
		<div class="dashboard-sidebar">
			<div class="dashboard-card">
				<div class="card-header">
					<h3 class="card-title">
						<i class="fas fa-bolt"></i>
						Quick Actions
					</h3>
				</div>
				<div class="card-content">
					<?php
						$role = $user['role'] ?? '';
						$allActions = [
							'patients' => [
								'href'  => '/patients',
								'icon'  => 'fas fa-user-injured',
								'label' => 'Patients',
							],
							'appointments' => [
								'href'  => '/appointments',
								'icon'  => 'fas fa-calendar-check',
								'label' => 'Appointments',
							],
							'admissions' => [
								'href'  => '/admissions',
								'icon'  => 'fas fa-hospital-user',
								'label' => 'Admissions',
							],
							'invoices' => [
								'href'  => '/invoices',
								'icon'  => 'fas fa-file-invoice-dollar',
								'label' => 'Billing & Invoices',
							],
							'medicines' => [
								'href'  => '/medicines',
								'icon'  => 'fas fa-pills',
								'label' => 'Medicine Inventory',
							],
						];

						$keys = [];
						switch ($role) {
							case 'doctor':
								$keys = ['patients', 'appointments', 'admissions'];
								break;
							case 'nurse':
								$keys = ['patients', 'appointments', 'admissions'];
								break;
							case 'receptionist':
								$keys = ['patients', 'appointments', 'admissions', 'invoices'];
								break;
							case 'pharmacist':
								$keys = ['medicines', 'invoices'];
								break;
							case 'lab':
								// No dedicated web modules yet; fall through to default
								break;
							case 'accountant':
								$keys = ['invoices'];
								break;
							case 'it':
								// No quick actions yet
								break;
							default:
								$keys = ['patients', 'appointments'];
								break;
						}

						$quickActions = [];
						foreach ($keys as $key) {
							if (isset($allActions[$key])) {
								$quickActions[] = $allActions[$key];
							}
						}
					?>

					<?php if (! empty($quickActions)): ?>
						<div class="action-grid">
							<?php foreach ($quickActions as $action): ?>
								<a href="<?= esc($action['href']) ?>" class="action-item">
									<div class="action-icon">
										<i class="<?= esc($action['icon']) ?>"></i>
									</div>
									<span class="action-label"><?= esc($action['label']) ?></span>
								</a>
							<?php endforeach; ?>
						</div>
					<?php else: ?>
						<p class="text-muted mb-0">No quick actions available for your role yet.</p>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="dashboard-main">
			<div class="dashboard-card">
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

			<div class="dashboard-card">
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

		<div class="dashboard-side">
			<div class="dashboard-card">
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

			<div class="dashboard-card">
				<div class="card-header">
					<h3 class="card-title">
						<i class="fas fa-pills"></i>
						Inventory Alerts
					</h3>
				</div>
				<div class="card-content">
					<ul class="inventory-list">
						<li>
							<span>Low stock medicines</span>
							<span class="badge bg-danger"><?= number_format($stats['low_stock_medicines'] ?? 0) ?></span>
						</li>
						<li>
							<span>Expiring in 30 days</span>
							<span class="badge bg-warning text-dark"><?= number_format($stats['expiring_medicines'] ?? 0) ?></span>
						</li>
					</ul>
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
	color: rgba(255, 255, 255, 0.85);
}

.user-details {
	display: flex;
	flex-direction: column;
}

.user-name {
	font-size: 1.2rem;
	font-weight: 600;
}

.user-role {
	font-size: 0.9rem;
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

.stats-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
	gap: 20px;
	margin-bottom: 30px;
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

.dashboard-grid {
	display: grid;
	grid-template-columns: 260px 2fr 1.2fr;
	gap: 20px;
}

.dashboard-card {
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

.dashboard-side {
	display: flex;
	flex-direction: column;
	gap: 20px;
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

.inventory-list {
	list-style: none;
	padding: 0;
	margin: 0;
	display: flex;
	flex-direction: column;
	gap: 10px;
}

.inventory-list li {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 10px 12px;
	background: #f8f9fa;
	border-radius: 8px;
}

.action-grid {
	display: flex;
	flex-direction: column;
	gap: 12px;
}

.action-item {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 12px 14px;
	border-radius: 10px;
	text-decoration: none;
	background: #f8f9fa;
	color: #2c3e50;
	transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
}

.action-item:hover {
	background: #4facfe;
	color: #fff;
	transform: translateY(-2px);
	box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
}

.action-icon {
	font-size: 1.4rem;
	color: #4facfe;
}

.action-item:hover .action-icon {
	color: #fff;
}

.action-label {
	font-size: 0.95rem;
	font-weight: 500;
}

@media (max-width: 1024px) {
	.dashboard-grid {
		grid-template-columns: 1fr;
	}
}

@media (max-width: 768px) {
	.dashboard-container {
		padding: 15px;
	}

	.dashboard-header {
		flex-direction: column;
		align-items: flex-start;
	}

	.header-content {
		flex-direction: column;
		align-items: flex-start;
	}

	.stats-grid {
		grid-template-columns: 1fr;
	}

	.action-grid {
		grid-template-columns: 1fr;
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

<?= $this->endSection() ?>
