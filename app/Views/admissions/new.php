<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1><i class="fas fa-hospital-user me-2"></i>New Admission</h1>
		<a href="/admissions" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back to Admissions</a>
	</div>

	<div class="card">
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

			<form action="/admissions/create" method="post">
				<?= csrf_field() ?>

				<div class="row mb-3">
					<div class="col-md-6">
						<label class="form-label"><i class="fas fa-user-injured me-1"></i>Patient</label>
						<select name="patient_id" class="form-select" required>
							<option value="">Select patient</option>
							<?php foreach ($patients as $patient): ?>
								<?php $pid = $patient['patient_id']; ?>
								<option value="<?= esc($pid) ?>" <?= (old('patient_id', $selected_patient_id ?? '') == $pid) ? 'selected' : '' ?>>
									<?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?> (ID: <?= esc($pid) ?>)
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-6">
						<label class="form-label"><i class="fas fa-user-md me-1"></i>Assigned Doctor</label>
						<select name="assigned_doctor_id" class="form-select" required>
							<option value="">Select doctor</option>
							<?php foreach ($doctors as $doctor): ?>
								<?php $did = $doctor['doctor_id']; ?>
								<option value="<?= esc($did) ?>" <?= old('assigned_doctor_id') == $did ? 'selected' : '' ?>
								>
									<?= esc($doctor['first_name'] . ' ' . $doctor['last_name']) ?> (ID: <?= esc($did) ?>)
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-md-4">
						<label class="form-label"><i class="fas fa-procedures me-1"></i>Ward</label>
						<select name="ward_id" class="form-select">
							<option value="">Not assigned</option>
							<?php foreach ($wards as $ward): ?>
								<option value="<?= esc($ward['id']) ?>" <?= old('ward_id') == $ward['id'] ? 'selected' : '' ?>>
									<?= esc($ward['name']) ?> (<?= esc($ward['ward_type']) ?>)
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-4">
						<label class="form-label"><i class="fas fa-door-open me-1"></i>Room Number</label>
						<input type="text" name="room_number" class="form-control" value="<?= esc(old('room_number')) ?>">
					</div>
					<div class="col-md-4">
						<label class="form-label"><i class="fas fa-bed me-1"></i>Bed Number</label>
						<input type="text" name="bed_number" class="form-control" value="<?= esc(old('bed_number')) ?>">
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-md-6">
						<label class="form-label"><i class="fas fa-calendar-plus me-1"></i>Admission Date &amp; Time</label>
						<input type="datetime-local" name="admission_date" class="form-control" value="<?= esc(old('admission_date')) ?>">
						<div class="form-text">Leave blank to use the current date and time.</div>
					</div>
					<div class="col-md-6">
						<label class="form-label"><i class="fas fa-info-circle me-1"></i>Status</label>
						<select name="status" class="form-select">
							<?php $statuses = ['admitted','transferred']; ?>
							<?php foreach ($statuses as $s): ?>
								<option value="<?= $s ?>" <?= old('status', 'admitted') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="mb-3">
					<label class="form-label"><i class="fas fa-notes-medical me-1"></i>Notes</label>
					<textarea name="notes" class="form-control" rows="4"><?= esc(old('notes')) ?></textarea>
				</div>

				<div class="d-flex justify-content-end mt-4">
					<button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i>Admit Patient</button>
					<a href="/admissions" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Cancel</a>
				</div>
			</form>
		</div>
	</div>
</div>
<?= $this->endSection() ?>
