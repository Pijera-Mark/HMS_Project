<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset User Password - HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .reset-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .user-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .password-display {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 1.5rem;
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            text-align: center;
            color: #495057;
            margin: 1rem 0;
        }
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .success-box {
            background: #d1e7dd;
            border: 1px solid #0f5132;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-container">
            <!-- User Information -->
            <div class="user-info">
                <h4 class="mb-3">
                    <i class="fas fa-user me-2"></i>Reset Password
                </h4>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Name:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Username:</strong> <?= htmlspecialchars($user['username']) ?>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <strong>Email:</strong> <?= htmlspecialchars($user['email']) ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Role:</strong> <?= ucfirst($user['role']) ?>
                    </div>
                </div>
            </div>

            <!-- Warning Message -->
            <div class="warning-box">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Important:</strong> This will generate a new secure password for the user. 
                The user will need to change their password after first login.
            </div>

            <!-- Success Message (Hidden by default) -->
            <div class="success-box" id="successMessage">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Password Reset Successful!</strong>
                <div id="passwordDetails"></div>
            </div>

            <!-- Reset Form -->
            <form id="resetPasswordForm" method="POST">
                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                
                <div class="text-center mb-4">
                    <button type="button" class="btn btn-warning btn-lg" onclick="generateNewPassword()">
                        <i class="fas fa-key me-2"></i>Generate New Password
                    </button>
                </div>

                <!-- Password Display (Hidden by default) -->
                <div id="passwordSection" style="display: none;">
                    <h5 class="text-center mb-3">New Password Generated</h5>
                    <div class="password-display" id="newPassword">
                        <!-- Password will appear here -->
                    </div>
                    
                    <div class="text-center mb-3">
                        <button type="button" class="btn btn-outline-primary" onclick="copyPassword()">
                            <i class="fas fa-copy me-2"></i>Copy Password
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="downloadCredentials()">
                            <i class="fas fa-download me-2"></i>Download Credentials
                        </button>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Next Steps:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Save this password securely</li>
                            <li>Share it with the user through a secure channel</li>
                            <li>User should change password after first login</li>
                        </ul>
                    </div>
                </div>
            </form>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <a href="/admin/user-management" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to User Management
                </a>
                <button type="button" class="btn btn-danger" onclick="confirmReset()">
                    <i class="fas fa-redo me-2"></i>Reset Again
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPassword = '';
        let resetData = null;

        function generateNewPassword() {
            if (!confirm('Are you sure you want to generate a new password? This will replace the current password.')) {
                return;
            }

            const formData = new FormData();
            formData.append('user_id', <?= $user['id'] ?>);

            fetch('/admin/user-management/reset-password/<?= $user['id'] ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentPassword = data.data.new_password;
                    resetData = data.data;
                    
                    // Display the password
                    document.getElementById('newPassword').textContent = currentPassword;
                    document.getElementById('passwordSection').style.display = 'block';
                    
                    // Show success message
                    const successDiv = document.getElementById('successMessage');
                    successDiv.style.display = 'block';
                    document.getElementById('passwordDetails').innerHTML = `
                        <div class="mt-2">
                            <strong>Username:</strong> ${data.data.username}<br>
                            <strong>New Password:</strong> <span style="font-family: monospace;">${data.data.new_password}</span><br>
                            <strong>Reset Time:</strong> ${data.data.reset_time}
                        </div>
                    `;
                    
                    // Hide the generate button
                    document.querySelector('button[onclick="generateNewPassword()"]').style.display = 'none';
                    
                    // Show notification
                    showNotification('Password reset successfully!', 'success');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        function copyPassword() {
            if (currentPassword) {
                navigator.clipboard.writeText(currentPassword).then(() => {
                    showNotification('Password copied to clipboard!', 'info');
                }).catch(() => {
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = currentPassword;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    showNotification('Password copied to clipboard!', 'info');
                });
            }
        }

        function downloadCredentials() {
            if (!resetData) return;

            const credentials = `User Credentials - HMS System
====================================
Username: ${resetData.username}
Password: ${resetData.new_password}
Reset Time: ${resetData.reset_time}

Important:
- Change password after first login
- Keep this information secure
- Do not share with unauthorized personnel

Generated on: ${new Date().toLocaleString()}
`;

            const blob = new Blob([credentials], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `credentials_${resetData.username}_${Date.now()}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            showNotification('Credentials downloaded successfully!', 'success');
        }

        function confirmReset() {
            if (confirm('Generate a new password again? The current password will be replaced.')) {
                // Reset the form
                document.getElementById('passwordSection').style.display = 'none';
                document.getElementById('successMessage').style.display = 'none';
                document.querySelector('button[onclick="generateNewPassword()"]').style.display = 'inline-block';
                currentPassword = '';
                resetData = null;
                
                // Generate new password
                generateNewPassword();
            }
        }

        function showNotification(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 5000);
        }

        // Prevent accidental navigation away
        window.addEventListener('beforeunload', function(e) {
            if (currentPassword) {
                e.preventDefault();
                e.returnValue = 'You have generated a new password. Are you sure you want to leave?';
                return e.returnValue;
            }
        });
    </script>
</body>
</html>
