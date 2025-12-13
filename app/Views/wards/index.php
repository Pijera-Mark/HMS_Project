<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Wards</h5>
                    <a href="<?= site_url('wards/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Ward
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Ward Name</th>
                                    <th>Type</th>
                                    <th>Capacity</th>
                                    <th>Occupied</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($wards)): ?>
                                    <tr><td colspan="6" class="text-center">No wards found</td></tr>
                                <?php else: ?>
                                    <?php foreach ($wards as $ward): ?>
                                        <tr>
                                            <td><?= esc($ward['name']) ?></td>
                                            <td><?= esc($ward['type']) ?></td>
                                            <td><?= $ward['capacity'] ?></td>
                                            <td><?= $ward['occupied'] ?? 0 ?></td>
                                            <td>
                                                <span class="badge bg-<?= $ward['status'] == 'active' ? 'success' : 'danger' ?>">
                                                    <?= esc(ucfirst($ward['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= site_url('wards/' . $ward['id']) ?>" class="btn btn-sm btn-primary">View</a>
                                                <a href="<?= site_url('wards/edit/' . $ward['id']) ?>" class="btn btn-sm btn-secondary">Edit</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
