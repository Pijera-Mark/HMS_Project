<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="mb-4">
        <h1><i class="fas fa-key me-2"></i>Reset Password</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-4">
                Reset password for <strong><?= esc($user['name']) ?></strong>
                (<?= esc($user['email']) ?>)
            </h5>

            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/users/reset-password/<?= esc($user['id']) ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                </div>

                <div class="mb-3">
                    <label for="new_password_confirm" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="new_password_confirm" name="new_password_confirm" required minlength="8">
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i>Update Password</button>
                    <a href="/users" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
