<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1><i class="fas fa-file-invoice-dollar me-2"></i>New Invoice</h1>
		<a href="/invoices" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back to Invoices</a>
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

			<form action="/invoices/create" method="post">
				<?= csrf_field() ?>

				<div class="row mb-3">
					<div class="col-md-6">
						<label class="form-label"><i class="fas fa-user-injured me-1"></i>Patient</label>
						<select name="patient_id" class="form-select" required>
							<option value="">Select patient</option>
							<?php foreach ($patients as $patient): ?>
								<?php $pid = $patient['patient_id']; ?>
								<option value="<?= esc($pid) ?>" <?= old('patient_id') == $pid ? 'selected' : '' ?>>
									<?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?> (ID: <?= esc($pid) ?>)
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-6">
						<label class="form-label"><i class="fas fa-hospital-user me-1"></i>Admission ID (optional)</label>
						<input type="number" name="admission_id" class="form-control" value="<?= esc(old('admission_id')) ?>">
						<div class="form-text">Link this invoice to an admission if applicable.</div>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-md-4">
						<label class="form-label"><i class="fas fa-file-invoice me-1"></i>Invoice Number</label>
						<input type="text" name="invoice_number" class="form-control" value="<?= esc(old('invoice_number')) ?>">
						<div class="form-text">Leave blank to auto-generate.</div>
					</div>
					<div class="col-md-4">
						<label class="form-label"><i class="fas fa-coins me-1"></i>Total Amount</label>
						<input type="number" step="0.01" name="total_amount" class="form-control" value="<?= esc(old('total_amount')) ?>" required>
					</div>
					<div class="col-md-4">
						<label class="form-label"><i class="fas fa-info-circle me-1"></i>Status</label>
						<select name="status" class="form-select">
							<?php $statuses = ['unpaid','partially_paid','paid']; ?>
							<?php foreach ($statuses as $s): ?>
								<option value="<?= $s ?>" <?= old('status', 'unpaid') === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="d-flex justify-content-end mt-4">
					<button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i>Save</button>
					<a href="/invoices" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Cancel</a>
				</div>
			</form>
		</div>
	</div>
</div>
<?= $this->endSection() ?>
