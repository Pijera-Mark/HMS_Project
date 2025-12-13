<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Ward Details</h5>
                    <a href="<?= site_url('wards') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Wards
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Ward Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td><?= esc($ward['name']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td><?= esc(ucfirst($ward['type'])) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Capacity:</strong></td>
                                    <td><?= $ward['capacity'] ?> beds</td>
                                </tr>
                                <tr>
                                    <td><strong>Occupied:</strong></td>
                                    <td><?= $ward['occupied'] ?? 0 ?> beds</td>
                                </tr>
                                <tr>
                                    <td><strong>Available:</strong></td>
                                    <td><?= ($ward['capacity'] - ($ward['occupied'] ?? 0)) ?> beds</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-<?= $ward['status'] == 'active' ? 'success' : 'danger' ?>">
                                            <?= esc(ucfirst($ward['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Location & Contact</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Floor:</strong></td>
                                    <td><?= esc($ward['floor'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Nurse Station:</strong></td>
                                    <td><?= esc($ward['nurse_station'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td><?= esc($ward['phone'] ?? 'N/A') ?></td>
                                </tr>
                            </table>
                            
                            <?php if (!empty($ward['description'])): ?>
                                <h6 class="mt-3">Description</h6>
                                <p><?= esc($ward['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="<?= site_url('wards/edit/' . $ward['id']) ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Ward
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
