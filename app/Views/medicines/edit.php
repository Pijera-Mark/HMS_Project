<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1><i class="fas fa-pills me-2"></i>Edit Medicine</h1>
		<a href="/medicines" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back to Medicines</a>
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

			<form action="/medicines/update/<?= $medicine['id'] ?>" method="post">
				<?= csrf_field() ?>

				<div class="row mb-3">
					<div class="col-md-6">
						<label class="form-label"><i class="fas fa-tag me-1"></i>Name</label>
						<input type="text" name="name" class="form-control" value="<?= esc(old('name', $medicine['name'] ?? '')) ?>" required>
					</div>
					<div class="col-md-6">
						<label class="form-label"><i class="fas fa-barcode me-1"></i>SKU</label>
						<input type="text" name="sku" class="form-control" value="<?= esc(old('sku', $medicine['sku'] ?? '')) ?>" required>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-md-4">
						<label class="form-label"><i class="fas fa-hashtag me-1"></i>Batch Number</label>
						<input type="text" name="batch_number" class="form-control" value="<?= esc(old('batch_number', $medicine['batch_number'] ?? '')) ?>">
					</div>
					<div class="col-md-4">
						<label class="form-label"><i class="fas fa-calendar-alt me-1"></i>Expiry Date</label>
						<input type="date" name="expiry_date" class="form-control" value="<?= esc(old('expiry_date', $medicine['expiry_date'] ?? '')) ?>">
					</div>
					<div class="col-md-4">
						<label class="form-label"><i class="fas fa-industry me-1"></i>Supplier</label>
						<input type="text" name="supplier" class="form-control" value="<?= esc(old('supplier', $medicine['supplier'] ?? '')) ?>">
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-md-4">
						<label class="form-label"><i class="fas fa-coins me-1"></i>Purchase Price</label>
						<input type="number" step="0.01" name="purchase_price" class="form-control" value="<?= esc(old('purchase_price', $medicine['purchase_price'] ?? '')) ?>">
					</div>
					<div class="col-md-4">
						<label class="form-label"><i class="fas fa-coins me-1"></i>Sale Price</label>
						<input type="number" step="0.01" name="sale_price" class="form-control" value="<?= esc(old('sale_price', $medicine['sale_price'] ?? '')) ?>">
					</div>
					<div class="col-md-4">
						<label class="form-label"><i class="fas fa-boxes me-1"></i>Stock Quantity</label>
						<input type="number" name="stock_quantity" class="form-control" value="<?= esc(old('stock_quantity', $medicine['stock_quantity'] ?? '')) ?>" required>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-md-4">
						<label class="form-label"><i class="fas fa-exclamation-triangle me-1"></i>Min Stock Threshold</label>
						<input type="number" name="min_stock_threshold" class="form-control" value="<?= esc(old('min_stock_threshold', $medicine['min_stock_threshold'] ?? '')) ?>">
					</div>
				</div>

				<div class="d-flex justify-content-end mt-4">
					<button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i>Update</button>
					<a href="/medicines" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Cancel</a>
				</div>
			</form>
		</div>
	</div>
</div>
<?= $this->endSection() ?>
