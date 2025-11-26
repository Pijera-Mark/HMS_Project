<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<style>
.dashboard-wrapper {
    padding: 0;
    background: transparent;
    min-height: 100vh;
    width: 100%;
    box-sizing: border-box;
    opacity: 0;
    animation: fadeIn 0.5s ease forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Enhanced UI Elements */
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px;
    border-radius: 20px;
    margin: 0 0 25px 0;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    position: relative;
    overflow: hidden;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.header-content {
    display: flex;
    align-items: center;
    gap: 20px;
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
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.stat-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.18);
    transition: all 0.3s ease;
    min-height: 90px;
    contain: layout style paint;
    position: relative;
    overflow: hidden;
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
    animation: gradientShift 3s ease-in-out infinite;
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.stat-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 12px 40px rgba(31, 38, 135, 0.25);
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
    font-size: 2.2rem;
    color: #667eea;
    position: relative;
    z-index: 1;
    animation: iconFloat 3s ease-in-out infinite;
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

.stat-content h3 {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0;
    color: #2c3e50;
    position: relative;
    z-index: 1;
}

.stat-content p {
    margin: 4px 0 0 0;
    color: #7f8c8d;
    font-size: 0.9rem;
    font-weight: 500;
}

@media (max-width: 1200px) {
    .dashboard-wrapper {
        padding: 0 10px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
    }
    
    .stat-card {
        padding: 12px;
        gap: 10px;
    }
    
    .stat-icon {
        font-size: 1.8rem;
    }
    
    .stat-content h3 {
        font-size: 1.3rem;
    }
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
        padding: 15px;
    }

    .header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .dashboard-title {
        font-size: 1.3rem;
    }
    
    .header-stats {
        gap: 15px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .stat-card {
        padding: 15px;
    }
}

@media (max-width: 480px) {
    .dashboard-wrapper {
        padding: 0 5px;
    }
    
    .dashboard-header {
        padding: 12px;
    }
    
    .dashboard-title {
        font-size: 1.2rem;
    }
    
    .stat-card {
        padding: 12px;
        gap: 8px;
    }
    
    .stat-icon {
        font-size: 1.5rem;
    }
    
    .stat-content h3 {
        font-size: 1.2rem;
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
// Ensure stable dashboard loading
document.addEventListener('DOMContentLoaded', function() {
    // Set current date immediately
    const currentDateElement = document.getElementById('currentDate');
    if (currentDateElement) {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        currentDateElement.textContent = now.toLocaleDateString('en-US', options);
    }
    
    // Prevent layout shifts by setting minimum heights
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.style.minHeight = '80px';
    });
    
    // Smooth number animations
    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(element => {
        const finalValue = parseInt(element.textContent.replace(/,/g, ''));
        if (!isNaN(finalValue)) {
            animateValue(element, 0, finalValue, 1000);
        }
    });
    
    function animateValue(element, start, end, duration) {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                element.textContent = end.toLocaleString();
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current).toLocaleString();
            }
        }, 16);
    }
    
    // Hide loading overlay when dashboard is ready
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        setTimeout(() => {
            loadingOverlay.style.display = 'none';
        }, 300);
    }
});
</script>

<?= $this->endSection() ?>
