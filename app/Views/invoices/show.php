<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1>
			<i class="fas fa-file-invoice-dollar me-2"></i>
			Invoice <?= esc($invoice['invoice_number']) ?>
		</h1>
		<a href="/invoices" class="btn btn-outline-secondary"><i class="fas removed me-1"></i>Back to Invoices</a>
	</div>

	<div class="row g-4">
		<div class="col-lg-8">
			<div class="card mb-4">
				<div class="card-header">
					<h5 class="mb-0"><i class="fas fa-user-injured me-1"></i>Patient Information</h5>
				</div>
				<div class="card-body">
					<p class="mb-1"><strong>Patient:</strong>
						<?= esc(($invoice['patient_first_name'] ?? '') . ' ' . ($invoice['patient_last_name'] ?? '')) ?>
					</p>
					<p class="mb-1 text-muted">ID: <?= esc($invoice['patient_id']) ?></p>
				</div>
			</div>

			<div class="card mb-4">
				<div class="card-header">
					<h5 class="mb-0"><i class="fas fa-file-invoice me-1"></i>Invoice Details</h5>
				</div>
				<div class="card-body">
					<p class="mb-2"><strong>Invoice Number:</strong> <?= esc($invoice['invoice_number']) ?></p>
					<p class="mb-2"><strong>Total Amount:</strong> ₱<?= number_format($invoice['total_amount'] ?? 0, 2) ?></p>
					<p class="mb-2"><strong>Admission ID:</strong> <?= esc($invoice['admission_id'] ?? 'N/A') ?></p>
					<p class="mb-2"><strong>Status:</strong>
						<?php
							$status = $invoice['status'] ?? 'unpaid';
							$badgeClass = match ($status) {
								'paid'           => 'bg-success',
								'partially_paid' => 'bg-warning',
								'unpaid'         => 'bg-danger',
								default          => 'bg-secondary',
							};
						?>
						<span class="badge <?= $badgeClass ?>"><?= ucfirst(str_replace('_', ' ', $status)) ?></span>
					</p>
					<p class="mb-0"><strong>Created At:</strong> <?= isset($invoice['created_at']) ? date('M d, Y h:i A', strtotime($invoice['created_at'])) : '-' ?></p>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<?php if (($invoice['status'] ?? 'unpaid') !== 'paid'): ?>
				<div class="card mb-4">
					<div class="card-header bg-success text-white">
						<h5 class="mb-0"><i class="fas fa-check me-1"></i>Mark as Paid</h5>
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

						<form action="/invoices/mark-paid/<?= $invoice['id'] ?>" method="post">
							<?= csrf_field() ?>
							<p class="mb-3">Confirm that this invoice has been fully paid.</p>
							<button type="submit" class="btn btn-success w-100" onclick="return confirm('Mark this invoice as fully paid?');">
								<i class="fas fa-check me-1"></i>Mark as Paid
							</button>
						</form>
					</div>
				</div>
			<?php else: ?>
				<div class="card">
					<div class="card-body text-center text-muted">
						<i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
						<p class="mb-0">This invoice is already paid.</p>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?= $this->endSection() ?>
