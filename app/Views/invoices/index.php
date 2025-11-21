<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1><i class="fas fa-file-invoice-dollar me-2"></i>Invoices</h1>
		<a href="/invoices/new" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Invoice</a>
	</div>

	<p class="text-muted mb-3">
		Total invoices: <?= is_array($invoices) ? count($invoices) : 0 ?>
	</p>

	<div class="card mb-4">
		<div class="card-body">
			<form method="get" class="row g-3">
				<div class="col-md-4">
					<label class="form-label">Status</label>
					<select name="status" class="form-select">
						<option value="">All</option>
						<?php $statuses = ['unpaid','partially_paid','paid']; ?>
						<?php foreach ($statuses as $s): ?>
							<option value="<?= $s ?>" <?= (isset($filter_status) && $filter_status === $s) ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-2 d-flex align-items-end">
					<button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-filter"></i> Filter</button>
				</div>
			</form>
		</div>
	</div>

	<div class="card">
		<div class="card-body">
			<table class="table table-hover">
				<thead class="table-dark">
					<tr>
						<th>#</th>
						<th>Invoice No.</th>
						<th>Patient</th>
						<th>Total Amount</th>
						<th>Status</th>
						<th>Created At</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($invoices)): ?>
						<?php $i = 1; foreach ($invoices as $invoice): ?>
							<tr>
								<td><?= $i++ ?></td>
								<td><?= esc($invoice['invoice_number']) ?></td>
								<td>
									<?= esc(($invoice['patient_first_name'] ?? '') . ' ' . ($invoice['patient_last_name'] ?? '')) ?>
									<div class="text-muted small">ID: <?= esc($invoice['patient_id']) ?></div>
								</td>
								<td>₱<?= number_format($invoice['total_amount'] ?? 0, 2) ?></td>
								<td>
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
								</td>
								<td><?= isset($invoice['created_at']) ? date('M d, Y h:i A', strtotime($invoice['created_at'])) : '-' ?></td>
								<td>
									<a href="/invoices/show/<?= $invoice['id'] ?>" class="btn btn-sm btn-info me-1" title="View details">
										<i class="fas fa-eye"></i>
									</a>
									<?php if (($invoice['status'] ?? '') !== 'paid'): ?>
										<form action="/invoices/mark-paid/<?= $invoice['id'] ?>" method="post" class="d-inline">
											<?= csrf_field() ?>
											<button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark this invoice as paid?');" title="Mark as paid">
												<i class="fas fa-check"></i>
											</button>
										</form>
									<?php endif; ?>
									<a href="/invoices/delete/<?= $invoice['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this invoice?');" title="Delete">
										<i class="fas fa-trash"></i>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else: ?>
						<tr>
							<td colspan="7" class="text-center py-4">
								<i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
								<p class="text-muted">No invoices found.</p>
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?= $this->endSection() ?>
