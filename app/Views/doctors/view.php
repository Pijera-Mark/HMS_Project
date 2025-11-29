<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Doctor Details</h3>
                    <div class="card-tools">
                        <a href="<?= site_url('doctors') ?>" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <a href="<?= site_url('doctors/edit/' . $doctor['doctor_id']) ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4><?= $doctor['first_name'] ?> <?= $doctor['last_name'] ?></h4>
                            <p class="text-muted"><?= $doctor['specialization'] ?></p>
                            
                            <table class="table table-striped">
                                <tr>
                                    <th width="30%">License Number</th>
                                    <td><?= $doctor['license_number'] ?></td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td><?= $doctor['phone'] ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?= $doctor['email'] ?: 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <th>Department</th>
                                    <td><?= $doctor['department'] ?: 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <th>Qualification</th>
                                    <td><?= $doctor['qualification'] ?: 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <th>Experience</th>
                                    <td><?= $doctor['experience_years'] ?> years</td>
                                </tr>
                                <tr>
                                    <th>Consultation Fee</th>
                                    <td>$<?= number_format($doctor['consultation_fee'], 2) ?></td>
                                </tr>
                                <tr>
                                    <th>Available Days</th>
                                    <td><?= $doctor['available_days'] ?: 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <th>Available Hours</th>
                                    <td><?= $doctor['available_hours'] ?: 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <th>Branch</th>
                                    <td><?= $doctor['branch_name'] ?? 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php if ($doctor['status'] == 'active'): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="<?= site_url('appointments/create?doctor_id=' . $doctor['doctor_id']) ?>" 
                                           class="btn btn-primary">
                                            <i class="fas fa-calendar-plus"></i> Book Appointment
                                        </a>
                                        <a href="<?= site_url('doctors/edit/' . $doctor['doctor_id']) ?>" 
                                           class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Edit Doctor
                                        </a>
                                        <a href="<?= site_url('doctors/delete/' . $doctor['doctor_id']) ?>" 
                                           class="btn btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this doctor?')">
                                            <i class="fas fa-trash"></i> Delete Doctor
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5>Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h4 class="text-primary">0</h4>
                                            <small>Total Appointments</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success">0</h4>
                                            <small>Patients Treated</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
