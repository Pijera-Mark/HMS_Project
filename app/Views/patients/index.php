<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-users me-2"></i>Patients</h1>
        <a href="/patients/new" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Patient</a>
    </div>

    <!-- Search Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or ID" value="<?= esc($_GET['search'] ?? '') ?>">
                </div>
                <div class="col-md-2">
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
                        <th><i class="fas fa-id-badge me-1"></i>ID</th>
                        <th><i class="fas fa-user me-1"></i>Name</th>
                        <th><i class="fas fa-birthday-cake me-1"></i>Date of Birth</th>
                        <th><i class="fas fa-venus-mars me-1"></i>Gender</th>
                        <th><i class="fas fa-phone me-1"></i>Phone</th>
                        <th><i class="fas fa-envelope me-1"></i>Email</th>
                        <th><i class="fas fa-cogs me-1"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($patients) && is_array($patients)): ?>
                        <?php foreach ($patients as $patient): ?>
                            <tr>
                                <td><?= esc($patient['patient_id']) ?></td>
                                <td><?= esc($patient['first_name']) ?> <?= esc($patient['last_name']) ?></td>
                                <td><?= esc($patient['date_of_birth']) ?></td>
                                <td><?= esc($patient['gender']) ?></td>
                                <td><?= esc($patient['phone']) ?></td>
                                <td><?= esc($patient['email']) ?></td>
                                <td>
                                    <a href="/patients/show/<?= $patient['patient_id'] ?>" class="btn btn-sm btn-info me-1"><i class="fas fa-eye"></i> View</a>
                                    <a href="/patients/edit/<?= $patient['patient_id'] ?>" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="/patients/delete/<?= $patient['patient_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this patient?');"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No patients found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
