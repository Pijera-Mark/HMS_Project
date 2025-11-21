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
						$quickActions = [];

						if (in_array($role, ['admin', 'doctor', 'nurse', 'receptionist'], true)) {
							$quickActions[] = [
								'href'  => '/patients',
								'icon'  => 'fas fa-user-injured',
								'label' => 'Patients',
							];
							$quickActions[] = [
								'href'  => '/appointments',
								'icon'  => 'fas fa-calendar-check',
								'label' => 'Appointments',
							];
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

<?php // Reuse the same CSS and JS from the admin dashboard for consistent styling ?>
<?php // Inherit styles & scripts by including admin dashboard CSS/JS if needed ?>
<?= $this->endSection() ?>
