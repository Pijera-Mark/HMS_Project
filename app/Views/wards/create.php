<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Create Ward</h5>
                    <a href="<?= site_url('wards') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Wards
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= site_url('wards') ?>">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Ward Name *</label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Ward Type *</label>
                                    <select name="type" id="type" class="form-select" required>
                                        <option value="">Select Type</option>
                                        <option value="general">General</option>
                                        <option value="icu">ICU</option>
                                        <option value="private">Private</option>
                                        <option value="maternity">Maternity</option>
                                        <option value="pediatric">Pediatric</option>
                                        <option value="surgical">Surgical</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="capacity" class="form-label">Capacity *</label>
                                    <input type="number" name="capacity" id="capacity" class="form-control" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="floor" class="form-label">Floor</label>
                                    <input type="text" name="floor" id="floor" class="form-control" placeholder="e.g., Ground Floor">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="maintenance">Under Maintenance</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nurse_station" class="form-label">Nurse Station</label>
                                    <input type="text" name="nurse_station" id="nurse_station" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Extension</label>
                                    <input type="text" name="phone" id="phone" class="form-control" placeholder="e.g., 101">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= site_url('wards') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Ward
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
