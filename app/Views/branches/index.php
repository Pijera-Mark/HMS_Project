<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-outline-secondary btn-sm me-3" onclick="window.history.back()">
                <i class="fas fa-arrow-left me-1"></i> Back
            </button>
            <h1 class="mb-0"><i class="fas fa-code-branch me-2"></i>Branches</h1>
        </div>
        <a href="/branches/new" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Branch</a>
    </div>

    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th><i class="fas fa-id-badge me-1"></i>ID</th>
                        <th><i class="fas fa-building me-1"></i>Name</th>
                        <th><i class="fas fa-tag me-1"></i>Code</th>
                        <th><i class="fas fa-phone me-1"></i>Contact</th>
                        <th><i class="fas fa-toggle-on me-1"></i>Status</th>
                        <th><i class="fas fa-cogs me-1"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($branches) && is_array($branches)): ?>
                        <?php foreach ($branches as $branch): ?>
                            <tr>
                                <td><?= esc($branch['id']) ?></td>
                                <td><?= esc($branch['name']) ?></td>
                                <td><?= esc($branch['code']) ?></td>
                                <td><?= esc($branch['contact_number']) ?></td>
                                <td><?= esc($branch['status']) ?></td>
                                <td>
                                    <a href="/branches/edit/<?= $branch['id'] ?>" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="/branches/delete/<?= $branch['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this branch?');"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-code-branch fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No branches found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
