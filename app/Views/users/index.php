<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0"><i class="fas fa-users-cog me-2"></i>Users</h1>
        <a href="/users/new" class="btn btn-primary"><i class="fas fa-user-plus me-1"></i>Add User</a>
    </div>

    <?php if (session()->has('message')): ?>
        <div class="alert alert-success">
            <?= esc(session('message')) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th><i class="fas fa-id-badge me-1"></i>ID</th>
                        <th><i class="fas fa-user me-1"></i>Name</th>
                        <th><i class="fas fa-envelope me-1"></i>Email</th>
                        <th><i class="fas fa-user-tag me-1"></i>Role</th>
                        <th><i class="fas fa-code-branch me-1"></i>Branch ID</th>
                        <th><i class="fas fa-toggle-on me-1"></i>Status</th>
                        <th><i class="fas fa-clock me-1"></i>Last Login</th>
                        <th><i class="fas fa-key me-1"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users) && is_array($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= esc($user['id']) ?></td>
                                <td><?= esc($user['name']) ?></td>
                                <td><?= esc($user['email']) ?></td>
                                <td><?= esc($user['role']) ?></td>
                                <td><?= esc($user['branch_id'] ?? '-') ?></td>
                                <td><?= esc($user['status'] ?? '-') ?></td>
                                <td><?= esc($user['last_login'] ?? '-') ?></td>
                                <td>
                                    <a href="/users/edit/<?= $user['id'] ?>" class="btn btn-sm btn-primary me-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="/users/reset-password/<?= $user['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-key"></i> Reset Password
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-users-cog fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No users found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
