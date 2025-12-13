<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user-edit me-2"></i>Edit User</h1>
        <a href="/users" class="btn btn-outline-secondary"><i class="fas removed me-1"></i>Back to Users</a>
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

            <form action="/users/update/<?= esc($user['id']) ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= esc($user['name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?= esc($user['email']) ?>" disabled>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <?php if ($user['role'] === 'admin'): ?>
                        <select class="form-select" id="role" name="role" disabled>
                            <option value="admin" selected>Admin (Protected)</option>
                        </select>
                        <small class="form-text text-muted">Admin roles cannot be modified for security reasons.</small>
                    <?php else: ?>
                        <select class="form-select" id="role" name="role" required>
                            <?php $roles = ['doctor','nurse','receptionist','pharmacist','lab','accountant','it']; ?>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= esc($role) ?>" <?= ($user['role'] === $role) ? 'selected' : '' ?>>
                                    <?= ucfirst($role) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="branch_id" class="form-label">Branch</label>
                    <select class="form-select" id="branch_id" name="branch_id">
                        <option value="">All branches / Global</option>
                        <?php if (!empty($branches) && is_array($branches)): ?>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?= esc($branch['id']) ?>" <?= ($user['branch_id'] ?? null) == $branch['id'] ? 'selected' : '' ?>>
                                    <?= esc($branch['name']) ?> (<?= esc($branch['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active" <?= ($user['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($user['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i>Save Changes</button>
                    <a href="/users" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
