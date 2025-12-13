<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Audit Reports</h5>
                </div>
                <div class="card-body">
                    <!-- Date Range Filter -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" 
                                       value="<?= esc($filters['date_from'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" 
                                       value="<?= esc($filters['date_to'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="user_id" class="form-label">User</label>
                                <select name="user_id" id="user_id" class="form-select">
                                    <option value="">All Users</option>
                                    <?php if (!empty($users)): ?>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?= $user['id'] ?>" <?= (($filters['user_id'] ?? '') == $user['id']) ? 'selected' : '' ?>>
                                                <?= esc($user['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="action" class="form-label">Action</label>
                                <select name="action" id="action" class="form-select">
                                    <option value="">All Actions</option>
                                    <option value="login" <?= (($filters['action'] ?? '') == 'login') ? 'selected' : '' ?>>Login</option>
                                    <option value="logout" <?= (($filters['action'] ?? '') == 'logout') ? 'selected' : '' ?>>Logout</option>
                                    <option value="create" <?= (($filters['action'] ?? '') == 'create') ? 'selected' : '' ?>>Create</option>
                                    <option value="update" <?= (($filters['action'] ?? '') == 'update') ? 'selected' : '' ?>>Update</option>
                                    <option value="delete" <?= (($filters['action'] ?? '') == 'delete') ? 'selected' : '' ?>>Delete</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <a href="<?= site_url('reports/audit') ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                                <a href="<?= site_url('reports/export/audit') ?>" class="btn btn-outline-success float-end">
                                    <i class="fas fa-download"></i> Export
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Audit Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Entity Type</th>
                                    <th>Entity ID</th>
                                    <th>IP Address</th>
                                    <th>User Agent</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($auditLogs)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No audit logs found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($auditLogs as $log): ?>
                                        <tr>
                                            <td><?= esc($log['created_at']) ?></td>
                                            <td><?= esc($log['user_name'] ?? 'System') ?></td>
                                            <td>
                                                <span class="badge bg-<?= getActionBadgeColor($log['action']) ?>">
                                                    <?= esc(ucfirst($log['action'])) ?>
                                                </span>
                                            </td>
                                            <td><?= esc($log['entity_type'] ?? '-') ?></td>
                                            <td><?= esc($log['entity_id'] ?? '-') ?></td>
                                            <td><?= esc($log['ip_address'] ?? '-') ?></td>
                                            <td class="text-truncate" style="max-width: 200px;" title="<?= esc($log['user_agent'] ?? '') ?>">
                                                <?= esc(substr($log['user_agent'] ?? '', 0, 30)) ?>...
                                            </td>
                                            <td class="text-truncate" style="max-width: 300px;" title="<?= esc($log['details'] ?? '') ?>">
                                                <?= esc(substr($log['details'] ?? '', 0, 50)) ?>...
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if (!empty($auditLogs) && isset($pager)): ?>
                        <div class="d-flex justify-content-center">
                            <?= $pager->links() ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function getActionBadgeColor($action) {
    switch($action) {
        case 'login': return 'success';
        case 'logout': return 'secondary';
        case 'create': return 'primary';
        case 'update': return 'info';
        case 'delete': return 'danger';
        default: return 'light';
    }
}
?>
<?= $this->endSection() ?>
