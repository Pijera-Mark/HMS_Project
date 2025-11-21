<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div class="d-flex align-items-center">
			<button type="button" class="btn btn-outline-secondary btn-sm me-3" onclick="window.history.back()">
				<i class="fas fa-arrow-left me-1"></i> Back
			</button>
			<h1 class="mb-0"><i class="fas fa-hospital-user me-2"></i>Admissions</h1>
		</div>
		<a href="/admissions/new" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Admission</a>
	</div>

	<p class="text-muted mb-3">
		Total admissions: <?= is_array($admissions) ? count($admissions) : 0 ?>
	</p>

	<div class="card mb-4">
		<div class="card-body">
			<form method="get" class="row g-3">
				<div class="col-md-4">
					<label class="form-label">Status</label>
					<select name="status" class="form-select">
						<option value="">All</option>
						<?php $statuses = ['admitted','discharged','transferred']; ?>
						<?php foreach ($statuses as $s): ?>
							<option value="<?= $s ?>" <?= (isset($filter_status) && $filter_status === $s) ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-2 d-flex align-items-end">
					<button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-filter"></i> Filter</button>
				</div>
			</form>

			<?php if (!empty($filter_status)): ?>
				<div class="small text-muted mt-2">
					Showing admissions with status <strong><?= ucfirst(esc($filter_status)) ?></strong>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="card">
		<div class="card-body">
			<table class="table table-hover">
				<thead class="table-dark">
					<tr>
						<th>ID</th>
						<th>Patient</th>
						<th>Doctor</th>
						<th>Ward / Bed</th>
						<th>Admission Date</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($admissions)): ?>
						<?php foreach ($admissions as $admission): ?>
							<tr>
								<td><?= esc($admission['id']) ?></td>
								<td>
									<?= esc(($admission['patient_first_name'] ?? '') . ' ' . ($admission['patient_last_name'] ?? '')) ?>
									<div class="text-muted small">ID: <?= esc($admission['patient_id']) ?></div>
								</td>
								<td>
									<?= esc(($admission['doctor_first_name'] ?? '') . ' ' . ($admission['doctor_last_name'] ?? '')) ?>
									<?php if (!empty($admission['assigned_doctor_id'])): ?>
										<div class="text-muted small">ID: <?= esc($admission['assigned_doctor_id']) ?></div>
									<?php endif; ?>
								</td>
								<td>
									<?= esc($admission['ward_name'] ?? 'Not assigned') ?>
									<?php if (!empty($admission['room_number']) || !empty($admission['bed_number'])): ?>
										<div class="text-muted small">
											Room <?= esc($admission['room_number'] ?? '-') ?>,
											Bed <?= esc($admission['bed_number'] ?? '-') ?>
										</div>
									<?php endif; ?>
								</td>
								<td><?= isset($admission['admission_date']) ? date('M d, Y h:i A', strtotime($admission['admission_date'])) : '-' ?></td>
								<td>
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
								</td>
								<td>
									<a href="/admissions/show/<?= $admission['id'] ?>" class="btn btn-sm btn-info" title="View details">
										<i class="fas fa-eye"></i>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else: ?>
						<tr>
							<td colspan="7" class="text-center py-4">
								<i class="fas fa-hospital fa-3x text-muted mb-3"></i>
								<p class="text-muted">No admissions found.</p>
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?= $this->endSection() ?>
