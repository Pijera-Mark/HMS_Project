<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add New Doctor</h3>
                    <div class="card-tools">
                        <a href="<?= site_url('doctors') ?>" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= site_url('doctors') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" id="first_name" class="form-control" 
                                           value="<?= old('first_name') ?>" required>
                                    <?php if (isset($validation) && $validation->getError('first_name')): ?>
                                        <span class="text-danger"><?= $validation->getError('first_name') ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" id="last_name" class="form-control" 
                                           value="<?= old('last_name') ?>" required>
                                    <?php if (isset($validation) && $validation->getError('last_name')): ?>
                                        <span class="text-danger"><?= $validation->getError('last_name') ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="specialization">Specialization <span class="text-danger">*</span></label>
                                    <input type="text" name="specialization" id="specialization" class="form-control" 
                                           value="<?= old('specialization') ?>" required>
                                    <?php if (isset($validation) && $validation->getError('specialization')): ?>
                                        <span class="text-danger"><?= $validation->getError('specialization') ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="license_number">License Number <span class="text-danger">*</span></label>
                                    <input type="text" name="license_number" id="license_number" class="form-control" 
                                           value="<?= old('license_number') ?>" required>
                                    <?php if (isset($validation) && $validation->getError('license_number')): ?>
                                        <span class="text-danger"><?= $validation->getError('license_number') ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" id="phone" class="form-control" 
                                           value="<?= old('phone') ?>" required>
                                    <?php if (isset($validation) && $validation->getError('phone')): ?>
                                        <span class="text-danger"><?= $validation->getError('phone') ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" 
                                           value="<?= old('email') ?>">
                                    <?php if (isset($validation) && $validation->getError('email')): ?>
                                        <span class="text-danger"><?= $validation->getError('email') ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="department">Department</label>
                                    <input type="text" name="department" id="department" class="form-control" 
                                           value="<?= old('department') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qualification">Qualification</label>
                                    <input type="text" name="qualification" id="qualification" class="form-control" 
                                           value="<?= old('qualification') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="experience_years">Experience (Years)</label>
                                    <input type="number" name="experience_years" id="experience_years" 
                                           class="form-control" value="<?= old('experience_years') ?>" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="consultation_fee">Consultation Fee</label>
                                    <input type="number" name="consultation_fee" id="consultation_fee" 
                                           class="form-control" value="<?= old('consultation_fee') ?>" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="branch_id">Branch</label>
                                    <select name="branch_id" id="branch_id" class="form-control">
                                        <option value="">Select Branch</option>
                                        <?php foreach ($branches as $branch): ?>
                                            <option value="<?= $branch['id'] ?>" 
                                                    <?= old('branch_id') == $branch['id'] ? 'selected' : '' ?>>
                                                <?= $branch['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="available_days">Available Days</label>
                                    <input type="text" name="available_days" id="available_days" class="form-control" 
                                           value="<?= old('available_days') ?>" placeholder="e.g., Mon, Wed, Fri">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="available_hours">Available Hours</label>
                                    <input type="text" name="available_hours" id="available_hours" class="form-control" 
                                           value="<?= old('available_hours') ?>" placeholder="e.g., 9:00 AM - 5:00 PM">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea name="address" id="address" class="form-control" rows="3"><?= old('address') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Doctor
                            </button>
                            <a href="<?= site_url('doctors') ?>" class="btn btn-default">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
