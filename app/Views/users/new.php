<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="mb-4">
        <h1><i class="fas fa-user-plus me-2"></i>Create New User</h1>
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
						<?php foreach (session('errors') as $error): ?>
							<li><?= esc($error) ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<form action="/users/create" method="post">
				<?= csrf_field() ?>

				<div class="row mb-3">
					<div class="col-md-6">
						<label class="form-label">Name</label>
						<input type="text" name="name" class="form-control" value="<?= esc(old('name')) ?>" required>
					</div>
					<div class="col-md-6">
						<label class="form-label">Email</label>
						<input type="email" name="email" class="form-control" value="<?= esc(old('email')) ?>" required>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-md-6">
						<label class="form-label">Phone (optional)</label>
						<input type="text" name="phone" class="form-control" value="<?= esc(old('phone')) ?>">
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-md-6">
						<label class="form-label">Password</label>
						<input type="password" name="password" class="form-control" required minlength="8">
					</div>
					<div class="col-md-6">
						<label class="form-label">Confirm Password</label>
						<input type="password" name="password_confirm" class="form-control" required minlength="8">
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-md-4">
						<label class="form-label">Role</label>
						<select name="role" class="form-select" required>
							<?php $roles = ['admin','doctor','nurse','receptionist','pharmacist','lab','accountant','it']; ?>
							<option value="">Select role</option>
							<?php foreach ($roles as $role): ?>
								<option value="<?= esc($role) ?>" <?= old('role') === $role ? 'selected' : '' ?>>
									<?= ucfirst($role) ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-4">
						<label class="form-label">Branch</label>
						<select name="branch_id" class="form-select">
							<option value="">All branches / Global</option>
							<?php if (!empty($branches) && is_array($branches)): ?>
								<?php foreach ($branches as $branch): ?>
									<option value="<?= esc($branch['id']) ?>" <?= old('branch_id') == $branch['id'] ? 'selected' : '' ?>>
										<?= esc($branch['name']) ?> (<?= esc($branch['code']) ?>)
									</option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
					<div class="col-md-4">
						<label class="form-label">Status</label>
						<select name="status" class="form-select">
							<option value="active" <?= old('status', 'active') === 'active' ? 'selected' : '' ?>>Active</option>
							<option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
						</select>
					</div>
				</div>

				<div class="d-flex justify-content-end mt-4">
					<button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i>Create User</button>
					<a href="/users" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Cancel</a>
				</div>
			</form>
		</div>
	</div>
</div>
<?= $this->endSection() ?>
