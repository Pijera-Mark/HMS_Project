<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-question-circle"></i>
            Help & Support
        </h1>
        <div class="page-breadcrumb">
            <a href="/dashboard" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Help & Support</span>
        </div>
    </div>

    <div class="help-content">
        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-book"></i>
                    HMS User Guide
                </h3>
            </div>
            <div class="card-content">
                <div class="help-sections">
                    <div class="help-section">
                        <h4><i class="fas fa-sign-in-alt"></i> Getting Started</h4>
                        <ul>
                            <li>Login with your credentials provided by the administrator</li>
                            <li>Use the sidebar navigation to access different modules</li>
                            <li>Dashboard shows key statistics and recent activities</li>
                        </ul>
                    </div>

                    <div class="help-section">
                        <h4><i class="fas fa-user-injured"></i> Patient Management</h4>
                        <ul>
                            <li>Register new patients with complete information</li>
                            <li>View and edit patient records</li>
                            <li>Track patient history and treatments</li>
                        </ul>
                    </div>

                    <div class="help-section">
                        <h4><i class="fas fa-calendar-check"></i> Appointments</h4>
                        <ul>
                            <li>Schedule patient appointments with doctors</li>
                            <li>View daily appointment calendar</li>
                            <li>Manage appointment status (scheduled, confirmed, completed)</li>
                        </ul>
                    </div>

                    <div class="help-section">
                        <h4><i class="fas fa-pills"></i> Medicine Inventory</h4>
                        <ul>
                            <li>Track medicine stock levels</li>
                            <li>Receive alerts for low stock medicines</li>
                            <li>Manage medicine suppliers and purchases</li>
                        </ul>
                    </div>

                    <div class="help-section">
                        <h4><i class="fas fa-file-invoice-dollar"></i> Billing & Invoices</h4>
                        <ul>
                            <li>Create patient invoices for services rendered</li>
                            <li>Track payment status</li>
                            <li>Generate financial reports</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-phone"></i>
                    Contact Support
                </h3>
            </div>
            <div class="card-content">
                <div class="support-info">
                    <div class="support-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email Support</strong>
                            <p>support@hms.com</p>
                        </div>
                    </div>
                    <div class="support-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong>Phone Support</strong>
                            <p>+1-800-HMS-HELP</p>
                        </div>
                    </div>
                    <div class="support-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Support Hours</strong>
                            <p>Monday - Friday: 8:00 AM - 6:00 PM</p>
                            <p>Saturday: 9:00 AM - 2:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-container {
    padding: 20px;
    min-height: 100vh;
    background: #f8f9fa;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e9ecef;
}

.page-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.page-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
}

.breadcrumb-link {
    color: #4facfe;
    text-decoration: none;
}

.breadcrumb-link:hover {
    text-decoration: underline;
}

.breadcrumb-separator {
    color: #6c757d;
}

.breadcrumb-current {
    color: #6c757d;
    font-weight: 500;
}

.help-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
}

.content-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}

.card-header {
    padding: 20px 25px;
    border-bottom: 1px solid #e9ecef;
}

.card-title {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-content {
    padding: 25px;
}

.help-sections {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.help-section h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.help-section ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.help-section li {
    padding: 8px 0;
    color: #6c757d;
    position: relative;
    padding-left: 20px;
}

.help-section li::before {
    content: '•';
    color: #4facfe;
    font-weight: bold;
    position: absolute;
    left: 0;
}

.support-info {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.support-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
}

.support-item i {
    font-size: 1.5rem;
    color: #4facfe;
    margin-top: 2px;
}

.support-item strong {
    display: block;
    color: #2c3e50;
    margin-bottom: 5px;
}

.support-item p {
    margin: 0;
    color: #6c757d;
    font-size: 0.9rem;
}

@media (max-width: 1200px) {
    .page-container {
        padding: 15px;
    }
    
    .help-content {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .page-title {
        font-size: 1.5rem;
    }
}
</style>

<?= $this->endSection() ?>
