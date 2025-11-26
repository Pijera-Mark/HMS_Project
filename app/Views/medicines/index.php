<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div class="d-flex align-items-center">
			<button type="button" class="btn btn-outline-secondary btn-sm me-3" onclick="">
				<i class="fas removed me-1"></i> Back
			</button>
			<h1 class="mb-0"><i class="fas fa-pills me-2"></i>Medicines</h1>
		</div>
		<a href="/medicines/new" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Medicine</a>
	</div>

	<p class="text-muted mb-3">
		Total medicines: <?= is_array($medicines) ? count($medicines) : 0 ?>
	</p>

	<div class="card mb-4">
		<div class="card-body">
			<form method="get" class="row g-3">
				<div class="col-md-4">
					<label class="form-label">Search</label>
					<input type="text" name="search" class="form-control" placeholder="Search by name, SKU, or batch" value="<?= esc($search ?? '') ?>">
				</div>
				<div class="col-md-2 d-flex align-items-end">
					<button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-search"></i> Search</button>
				</div>
			</form>
		</div>
	</div>

	<div class="card">
		<div class="card-body">
			<table class="table table-hover">
				<thead class="table-dark">
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>SKU</th>
						<th>Batch</th>
						<th>Expiry Date</th>
						<th>Stock</th>
						<th>Min Threshold</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($medicines)): ?>
						<?php foreach ($medicines as $medicine): ?>
							<tr>
								<td><?= esc($medicine['id']) ?></td>
								<td><?= esc($medicine['name']) ?></td>
								<td><?= esc($medicine['sku']) ?></td>
								<td><?= esc($medicine['batch_number'] ?? '') ?></td>
								<td><?= esc($medicine['expiry_date'] ?? '') ?></td>
								<td><?= esc($medicine['stock_quantity']) ?></td>
								<td><?= esc($medicine['min_stock_threshold'] ?? '') ?></td>
								<td>
									<a href="/medicines/edit/<?= $medicine['id'] ?>" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i> Edit</a>
									<a href="/medicines/delete/<?= $medicine['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this medicine?');"><i class="fas fa-trash"></i> Delete</a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else: ?>
						<tr>
							<td colspan="8" class="text-center py-4">
								<i class="fas fa-pills fa-3x text-muted mb-3"></i>
								<p class="text-muted">No medicines found.</p>
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?= $this->endSection() ?>
