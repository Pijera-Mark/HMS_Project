<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-code-branch me-2"></i>Add Branch</h1>
        <a href="/branches" class="btn btn-outline-secondary"><i class="fas removed me-1"></i>Back to Branches</a>
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

            <form action="/branches/create" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="name" class="form-label"><i class="fas fa-building me-1"></i>Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= esc(old('name')) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="code" class="form-label"><i class="fas fa-tag me-1"></i>Code</label>
                    <input type="text" class="form-control" id="code" name="code" value="<?= esc(old('code')) ?>">
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?= esc(old('address')) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="contact_number" class="form-label"><i class="fas fa-phone me-1"></i>Contact Number</label>
                    <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?= esc(old('contact_number')) ?>">
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label"><i class="fas fa-toggle-on me-1"></i>Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active" <?= old('status') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i>Add Branch</button>
                    <a href="/branches" class="btn btn-secondary"><i class="fas fa-times me-1"></i>Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
