<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Doctors Management</h3>
                    <div class="card-tools">
                        <a href="<?= site_url('doctors/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Doctor
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select name="specialization" id="specialization" class="form-control">
                                <option value="">All Specializations</option>
                                <?php foreach ($specializations as $spec): ?>
                                    <option value="<?= $spec ?>" <?= old('specialization') == $spec ? 'selected' : '' ?>>
                                        <?= $spec ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="branch_id" id="branch_id" class="form-control">
                                <option value="">All Branches</option>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= $branch['id'] ?>" <?= old('branch_id') == $branch['id'] ? 'selected' : '' ?>>
                                        <?= $branch['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" id="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Search doctors..." value="<?= old('search') ?>">
                        </div>
                    </div>

                    <!-- Doctors Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Specialization</th>
                                    <th>License Number</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Branch</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($doctors)): ?>
                                    <?php foreach ($doctors as $doctor): ?>
                                        <tr>
                                            <td>
                                                <?= $doctor['first_name'] ?> <?= $doctor['last_name'] ?>
                                                <br>
                                                <small class="text-muted">Exp: <?= $doctor['experience_years'] ?> years</small>
                                            </td>
                                            <td><?= $doctor['specialization'] ?></td>
                                            <td><?= $doctor['license_number'] ?></td>
                                            <td><?= $doctor['phone'] ?></td>
                                            <td><?= $doctor['email'] ?></td>
                                            <td><?= $doctor['branch_name'] ?? 'N/A' ?></td>
                                            <td>
                                                <?php if ($doctor['status'] == 'active'): ?>
                                                    <span class="badge badge-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= site_url('doctors/view/' . $doctor['doctor_id']) ?>" 
                                                       class="btn btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= site_url('doctors/edit/' . $doctor['doctor_id']) ?>" 
                                                       class="btn btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= site_url('doctors/delete/' . $doctor['doctor_id']) ?>" 
                                                       class="btn btn-danger" title="Delete" 
                                                       onclick="return confirm('Are you sure you want to delete this doctor?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No doctors found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Apply filters on change
    $('#specialization, #branch_id, #status').on('change', function() {
        applyFilters();
    });

    // Search on keyup with delay
    let searchTimeout;
    $('#search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            applyFilters();
        }, 500);
    });

    function applyFilters() {
        const specialization = $('#specialization').val();
        const branchId = $('#branch_id').val();
        const status = $('#status').val();
        const search = $('#search').val();

        let url = '<?= site_url('doctors') ?>?';
        const params = [];

        if (specialization) params.push('specialization=' + encodeURIComponent(specialization));
        if (branchId) params.push('branch_id=' + encodeURIComponent(branchId));
        if (status) params.push('status=' + encodeURIComponent(status));
        if (search) params.push('search=' + encodeURIComponent(search));

        url += params.join('&');
        window.location.href = url;
    }
});
</script>
<?= $this->endSection() ?>
