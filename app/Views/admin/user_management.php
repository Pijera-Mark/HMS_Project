<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .user-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .user-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .user-card.active {
            border-left-color: #28a745;
        }
        .user-card.inactive {
            border-left-color: #dc3545;
        }
        .role-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .search-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        .action-buttons .btn {
            margin: 0.25rem;
        }
        .password-display {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            font-family: monospace;
            font-size: 1.1rem;
            color: #495057;
        }
        .activity-log {
            max-height: 400px;
            overflow-y: auto;
        }
        .activity-item {
            border-left: 3px solid #007bff;
            padding-left: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-0">
                    <i class="fas fa-users-cog me-2"></i>User Management
                </h2>
                <p class="text-muted">Manage system users, roles, and permissions</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <h3><?= $stats['total_users'] ?></h3>
                    <p class="mb-0">Total Users</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <h3><?= $stats['active_users'] ?></h3>
                    <p class="mb-0">Active Users</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);">
                    <h3><?= $stats['inactive_users'] ?></h3>
                    <p class="mb-0">Inactive Users</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);">
                    <h3><?= count($stats['by_role']) ?></h3>
                    <p class="mb-0">User Roles</p>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="search-section">
            <div class="row">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Users</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="search" placeholder="Search by name, email, username...">
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="roleFilter" class="form-label">Role</label>
                    <select class="form-select" id="roleFilter">
                        <option value="">All Roles</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role ?>"><?= ucfirst($role) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="branchFilter" class="form-label">Branch</label>
                    <select class="form-select" id="branchFilter">
                        <option value="">All Branches</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label d-block">&nbsp;</label>
                    <button class="btn btn-primary w-100" onclick="searchUsers()">
                        <i class="fas fa-filter me-2"></i>Apply Filters
                    </button>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button class="btn btn-success" onclick="showCreateUserModal()">
                        <i class="fas fa-user-plus me-2"></i>Add New User
                    </button>
                    <button class="btn btn-info" onclick="exportUsers('csv')">
                        <i class="fas fa-file-csv me-2"></i>Export CSV
                    </button>
                    <button class="btn btn-info" onclick="exportUsers('excel')">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </button>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div class="row" id="usersContainer">
            <?php foreach ($users as $user): ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card user-card <?= $user['status'] ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h5>
                                    <p class="text-muted mb-1">@<?= htmlspecialchars($user['username']) ?></p>
                                    <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                </div>
                                <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'danger' ?> role-badge">
                                    <?= ucfirst($user['status']) ?>
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <span class="badge bg-primary role-badge me-1">
                                    <i class="fas fa-user-tag me-1"></i><?= ucfirst($user['role']) ?>
                                </span>
                                <?php if (!empty($user['branch_name'])): ?>
                                    <span class="badge bg-info role-badge">
                                        <i class="fas fa-hospital me-1"></i><?= htmlspecialchars($user['branch_name']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($user['phone'])): ?>
                                <div class="mb-2">
                                    <small><i class="fas fa-phone me-2"></i><?= htmlspecialchars($user['phone']) ?></small>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-2"></i>Joined: <?= date('M j, Y', strtotime($user['created_at'])) ?>
                                </small>
                                <?php if (!empty($user['last_login'])): ?>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-sign-in-alt me-2"></i>Last login: <?= date('M j, Y H:i', strtotime($user['last_login'])) ?>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <div class="action-buttons">
                                <button class="btn btn-sm btn-outline-primary" onclick="editUser(<?= $user['user_id'] ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-warning" onclick="resetPassword(<?= $user['user_id'] ?>)">
                                    <i class="fas fa-key"></i> Reset Password
                                </button>
                                <button class="btn btn-sm btn-outline-info" onclick="viewActivity(<?= $user['user_id'] ?>)">
                                    <i class="fas fa-history"></i> Activity
                                </button>
                                <button class="btn btn-sm btn-outline-<?= $user['status'] === 'active' ? 'secondary' : 'success' ?>" 
                                        onclick="toggleStatus(<?= $user['user_id'] ?>)">
                                    <i class="fas fa-<?= $user['status'] === 'active' ? 'pause' : 'play' ?>"></i> 
                                    <?= $user['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                </button>
                                <?php if ($user['user_id'] != session()->get('user')['user_id']): ?>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?= $user['user_id'] ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Create User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createUserForm">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Username *</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name *</label>
                                <input type="text" class="form-control" name="first_name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name *</label>
                                <input type="text" class="form-control" name="last_name" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Password *</label>
                                <input type="password" class="form-control" name="password" required>
                                <small class="text-muted">Min 8 chars, include uppercase, lowercase, number & special char</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" name="phone">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Role *</label>
                                <select class="form-select" name="role" required>
                                    <option value="">Select Role</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role ?>"><?= ucfirst($role) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Branch *</label>
                                <select class="form-select" name="branch_id" required>
                                    <option value="">Select Branch</option>
                                    <?php foreach ($branches as $branch): ?>
                                        <option value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createUser()">Create User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset User Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Generate a new secure password for this user?</p>
                    <div id="newPasswordDisplay" class="password-display d-none">
                        <div class="d-flex justify-content-between align-items-center">
                            <span id="passwordText"></span>
                            <button class="btn btn-sm btn-outline-secondary" onclick="copyPassword()">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" onclick="confirmResetPassword()">
                        <i class="fas fa-key me-2"></i>Generate New Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Modal -->
    <div class="modal fade" id="activityModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Activity Log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="activityContent" class="activity-log">
                        <!-- Activity will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentUserId = null;

        function showCreateUserModal() {
            const modal = new bootstrap.Modal(document.getElementById('createUserModal'));
            modal.show();
        }

        function createUser() {
            const form = document.getElementById('createUserForm');
            const formData = new FormData(form);
            
            fetch('/admin/user-management/create', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User created successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        function editUser(userId) {
            window.location.href = '/admin/user-management/edit/' + userId;
        }

        function resetPassword(userId) {
            currentUserId = userId;
            const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
            modal.show();
        }

        function confirmResetPassword() {
            fetch('/admin/user-management/reset-password/' + currentUserId, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('passwordText').textContent = data.data.new_password;
                    document.getElementById('newPasswordDisplay').classList.remove('d-none');
                    
                    // Store password info for user
                    const userInfo = {
                        username: data.data.username,
                        password: data.data.new_password,
                        resetTime: data.data.reset_time
                    };
                    
                    // Show success message
                    alert('Password reset successfully!\n\nUsername: ' + userInfo.username + 
                          '\nNew Password: ' + userInfo.password + 
                          '\nReset Time: ' + userInfo.resetTime + 
                          '\n\nPlease save this password securely.');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        function copyPassword() {
            const passwordText = document.getElementById('passwordText').textContent;
            navigator.clipboard.writeText(passwordText).then(() => {
                alert('Password copied to clipboard!');
            });
        }

        function toggleStatus(userId) {
            if (confirm('Are you sure you want to change this user\'s status?')) {
                fetch('/admin/user-management/change-status/' + userId, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('User status changed successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
            }
        }

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                fetch('/admin/user-management/delete/' + userId, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('User deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
            }
        }

        function viewActivity(userId) {
            currentUserId = userId;
            
            fetch('/admin/user-management/activity/' + userId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let activityHtml = '';
                    
                    if (data.data.activities.length === 0) {
                        activityHtml = '<p class="text-muted">No activity recorded for this user.</p>';
                    } else {
                        data.data.activities.forEach(activity => {
                            activityHtml += `
                                <div class="activity-item">
                                    <div class="d-flex justify-content-between">
                                        <strong>${activity.action}</strong>
                                        <small class="text-muted">${activity.created_at}</small>
                                    </div>
                                    <p class="mb-1">${activity.description}</p>
                                    <small class="text-muted">IP: ${activity.ip_address}</small>
                                </div>
                            `;
                        });
                    }
                    
                    document.getElementById('activityContent').innerHTML = activityHtml;
                    
                    const modal = new bootstrap.Modal(document.getElementById('activityModal'));
                    modal.show();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        function searchUsers() {
            const search = document.getElementById('search').value;
            const role = document.getElementById('roleFilter').value;
            const branch = document.getElementById('branchFilter').value;
            const status = document.getElementById('statusFilter').value;
            
            const params = new URLSearchParams({
                search: search,
                role: role,
                branch: branch,
                status: status
            });
            
            fetch('/admin/user-management/users?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update users container with new data
                    updateUsersDisplay(data.data);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        function updateUsersDisplay(users) {
            const container = document.getElementById('usersContainer');
            container.innerHTML = '';
            
            users.forEach(user => {
                const userCard = `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card user-card ${user.status}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="card-title mb-1">${user.first_name} ${user.last_name}</h5>
                                        <p class="text-muted mb-1">@${user.username}</p>
                                        <small class="text-muted">${user.email}</small>
                                    </div>
                                    <span class="badge bg-${user.status === 'active' ? 'success' : 'danger'} role-badge">
                                        ${user.status}
                                    </span>
                                </div>
                                
                                <div class="mb-3">
                                    <span class="badge bg-primary role-badge me-1">
                                        <i class="fas fa-user-tag me-1"></i>${user.role}
                                    </span>
                                    ${user.branch_name ? `<span class="badge bg-info role-badge">
                                        <i class="fas fa-hospital me-1"></i>${user.branch_name}
                                    </span>` : ''}
                                </div>

                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editUser(${user.user_id})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" onclick="resetPassword(${user.user_id})">
                                        <i class="fas fa-key"></i> Reset Password
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewActivity(${user.user_id})">
                                        <i class="fas fa-history"></i> Activity
                                    </button>
                                    <button class="btn btn-sm btn-outline-${user.status === 'active' ? 'secondary' : 'success'}" 
                                            onclick="toggleStatus(${user.user_id})">
                                        <i class="fas fa-${user.status === 'active' ? 'pause' : 'play'}"></i> 
                                        ${user.status === 'active' ? 'Deactivate' : 'Activate'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += userCard;
            });
        }

        function exportUsers(format) {
            const params = new URLSearchParams({
                format: format,
                search: document.getElementById('search').value,
                role: document.getElementById('roleFilter').value,
                branch: document.getElementById('branchFilter').value,
                status: document.getElementById('statusFilter').value
            });
            
            window.open('/admin/user-management/export?' + params.toString());
        }

        // Auto-search on input
        document.getElementById('search').addEventListener('input', function() {
            if (this.value.length === 0 || this.value.length >= 3) {
                searchUsers();
            }
        });

        // Filter change handlers
        document.getElementById('roleFilter').addEventListener('change', searchUsers);
        document.getElementById('branchFilter').addEventListener('change', searchUsers);
        document.getElementById('statusFilter').addEventListener('change', searchUsers);
    </script>
</body>
</html>
