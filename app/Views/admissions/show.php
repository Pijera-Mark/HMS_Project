<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1>
			<i class="fas fa-hospital-user me-2"></i>
			Admission #<?= esc($admission['id']) ?>
		</h1>
		<a href="/admissions" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back to Admissions</a>
	</div>

	<div class="row g-4">
		<div class="col-lg-8">
			<div class="card mb-4">
				<div class="card-header">
					<h5 class="mb-0"><i class="fas fa-user-injured me-1"></i>Patient Information</h5>
				</div>
				<div class="card-body">
					<p class="mb-1"><strong>Patient:</strong>
						<?= esc(($admission['patient_first_name'] ?? '') . ' ' . ($admission['patient_last_name'] ?? '')) ?>
					</p>
					<p class="mb-1 text-muted">ID: <?= esc($admission['patient_id']) ?></p>
				</div>
			</div>

			<div class="card mb-4">
				<div class="card-header">
					<h5 class="mb-0"><i class="fas fa-procedures me-1"></i>Admission Details</h5>
				</div>
				<div class="card-body">
					<div class="row mb-2">
						<div class="col-md-6">
							<strong>Assigned Doctor:</strong><br>
							<?= esc(($admission['doctor_first_name'] ?? '') . ' ' . ($admission['doctor_last_name'] ?? '')) ?>
							<?php if (!empty($admission['assigned_doctor_id'])): ?>
								<div class="text-muted small">ID: <?= esc($admission['assigned_doctor_id']) ?></div>
							<?php endif; ?>
						</div>
						<div class="col-md-6">
							<strong>Ward / Bed:</strong><br>
							<?= esc($admission['ward_name'] ?? 'Not assigned') ?><br>
							<?php if (!empty($admission['room_number']) || !empty($admission['bed_number'])): ?>
								<span class="text-muted small">Room <?= esc($admission['room_number'] ?? '-') ?>, Bed <?= esc($admission['bed_number'] ?? '-') ?></span>
							<?php endif; ?>
						</div>
					</div>

					<div class="row mb-2">
						<div class="col-md-6">
							<strong>Admission Date:</strong><br>
							<?= isset($admission['admission_date']) ? date('M d, Y h:i A', strtotime($admission['admission_date'])) : '-' ?>
						</div>
						<div class="col-md-6">
							<strong>Discharge Date:</strong><br>
							<?= !empty($admission['discharge_date']) ? date('M d, Y h:i A', strtotime($admission['discharge_date'])) : '-' ?>
						</div>
					</div>

					<div class="mb-2">
						<strong>Status:</strong>
						<?php
							$status = $admission['status'] ?? 'admitted';
							$badgeClass = match ($status) {
								'admitted'   => 'bg-success',
								'discharged' => 'bg-secondary',
								'transferred'=> 'bg-warning',
								default      => 'bg-secondary',
							};
						?>
						<span class="badge <?= $badgeClass ?>"><?= ucfirst($status) ?></span>
					</div>

					<div class="mt-3">
						<strong>Notes:</strong>
						<p class="mb-0"><?= nl2br(esc($admission['notes'] ?? 'No notes recorded.')) ?></p>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<?php if (($admission['status'] ?? 'admitted') === 'admitted'): ?>
				<div class="card mb-4">
					<div class="card-header bg-warning text-dark">
						<h5 class="mb-0"><i class="fas fa-door-open me-1"></i>Discharge Patient</h5>
					</div>
					<div class="card-body">
						<?php if (session()->has('errors')): ?>
							<div class="alert alert-danger">
								<ul class="mb-0">
									<?php foreach (session('errors') as $error): ?>
										<li><?= esc($error) ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>

						<form action="/admissions/discharge/<?= $admission['id'] ?>" method="post">
							<?= csrf_field() ?>
							<div class="mb-3">
								<label class="form-label"><i class="fas fa-calendar-minus me-1"></i>Discharge Date &amp; Time</label>
								<input type="datetime-local" name="discharge_date" class="form-control">
								<div class="form-text">Leave blank to use the current date and time.</div>
							</div>
							<div class="mb-3">
								<label class="form-label"><i class="fas fa-notes-medical me-1"></i>Notes</label>
								<textarea name="notes" class="form-control" rows="4"></textarea>
							</div>
							<button type="submit" class="btn btn-warning w-100" onclick="return confirm('Discharge this patient from admission?');">
								<i class="fas fa-door-open me-1"></i>Discharge Patient
							</button>
						</form>
					</div>
				</div>
			<?php else: ?>
				<div class="card">
					<div class="card-body text-center text-muted">
						<i class="fas fa-info-circle fa-2x mb-2"></i>
						<p class="mb-0">This admission has already been <?= esc($admission['status']) ?>.</p>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?= $this->endSection() ?>
