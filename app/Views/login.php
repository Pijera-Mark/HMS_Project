<?= $this->extend('auth/layout') ?>

<?= $this->section('content') ?>
<div class="login-container">
    <div class="login-wrapper">
        <div class="login-left">
            <div class="welcome-content">
                <div class="logo-section">
                    <i class="fas fa-hospital-alt main-icon"></i>
                    <h1><?= esc(get_setting('hospital_name', 'Hospital Management System')) ?></h1>
                    <p class="subtitle">Advanced Healthcare Solutions</p>
                </div>
                <div class="features">
                    <div class="feature-item">
                        <i class="fas fa-user-md"></i>
                        <span>Patient Management</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>Appointment Scheduling</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-pills"></i>
                        <span>Medicine Inventory</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Analytics & Reports</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="login-right">
            <div class="login-form-container">
                <div class="login-header">
                    <h2>Welcome Back</h2>
                    <p>Please sign in to your account</p>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form action="/login" method="post" class="login-form">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i>
                            Email Address
                        </label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i>
                            Password
                        </label>
                        <div class="password-input">
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="password-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span class="checkmark"></span>
                            Remember me
                        </label>
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>

                    <button type="submit" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </button>
                </form>

                <div class="login-footer">
                    <p>Don't have an account? <a href="#" class="signup-link">Contact Administrator</a></p>
                    <p class="mt-2" style="font-size: 0.85rem; color: #6c757d;">
                        <?= esc(get_setting('hospital_email', '')) ?>
                        <?php if (get_setting('hospital_phone', '')): ?>
                            &middot; <?= esc(get_setting('hospital_phone')) ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    position: relative;
    overflow: hidden;
}

.login-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%), radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.login-wrapper {
    display: flex;
    width: 100%;
    max-width: 1200px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    box-shadow: 0 25px 80px rgba(31, 38, 135, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.18);
    overflow: hidden;
    min-height: 600px;
    position: relative;
    z-index: 1;
}

.login-left {
    flex: 1;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    position: relative;
    overflow: hidden;
}

.login-left::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: float 8s ease-in-out infinite;
}

.welcome-content {
    text-align: center;
    color: white;
    position: relative;
    z-index: 1;
}

.main-icon {
    font-size: 4.5rem;
    margin-bottom: 25px;
    color: white;
    text-shadow: 0 4px 8px rgba(0,0,0,0.2);
    animation: iconPulse 3s ease-in-out infinite;
}

@keyframes iconPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.welcome-content h1 {
    font-size: 2.5rem;
    margin-bottom: 10px;
    font-weight: 700;
}

.subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 40px;
}

.features {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    max-width: 300px;
    margin: 0 auto;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 18px;
    background: rgba(255,255,255,0.15);
    border-radius: 12px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    transition: all 0.3s ease;
}

.feature-item:hover {
    background: rgba(255,255,255,0.25);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.feature-item i {
    font-size: 1.2rem;
    color: white;
}

.feature-item span {
    font-weight: 500;
}

.login-right {
    flex: 1;
    padding: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(248, 249, 250, 0.8);
    backdrop-filter: blur(10px);
}

.login-form-container {
    width: 100%;
    max-width: 400px;
}

.login-header {
    text-align: center;
    margin-bottom: 30px;
}

.login-header h2 {
    color: #2c3e50;
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.login-header p {
    color: #6c757d;
    margin: 0;
}

.alert-error {
    background: #fee;
    color: #c33;
    padding: 12px 16px;
    border-radius: 8px;
    border-left: 4px solid #e74c3c;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.login-form {
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-group input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.form-group input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.password-input {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    padding: 4px;
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 0.9rem;
    color: #6c757d;
}

.remember-me input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.checkmark {
    width: 18px;
    height: 18px;
    border: 2px solid #e9ecef;
    border-radius: 4px;
    position: relative;
    background: white;
}

.remember-me input:checked + .checkmark {
    background: #667eea;
    border-color: #667eea;
}

.remember-me input:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: -2px;
    left: 2px;
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.forgot-password {
    color: #667eea;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
}

.forgot-password:hover {
    text-decoration: underline;
}

.login-btn {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.login-footer {
    text-align: center;
    margin-top: 20px;
}

.login-footer p {
    color: #6c757d;
    margin: 0;
}

.signup-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
}

.signup-link:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .login-wrapper {
        flex-direction: column;
        min-height: auto;
    }

    .login-left {
        padding: 30px 20px;
    }

    .welcome-content h1 {
        font-size: 2rem;
    }

    .features {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .login-right {
        padding: 30px 20px;
    }

    .login-header h2 {
        font-size: 1.5rem;
    }
}
</style>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('password-icon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        passwordIcon.className = 'fas fa-eye';
    }
}
</script>
<?= $this->endSection() ?>
