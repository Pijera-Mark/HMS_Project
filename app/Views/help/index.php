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
        <div class="main-help-section">
            <!-- Quick Search -->
            <div class="content-card mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-search"></i>
                        Quick Search
                    </h3>
                </div>
                <div class="card-content">
                    <div class="search-container">
                        <input type="text" id="helpSearch" class="form-control search-input" placeholder="Search for help topics...">
                        <div id="searchResults" class="search-results"></div>
                    </div>
                </div>
            </div>

            <!-- User Guide -->
            <div class="content-card mb-4">
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
                                <li>Update your profile information in the Profile section</li>
                            </ul>
                        </div>

                        <div class="help-section">
                            <h4><i class="fas fa-user-injured"></i> Patient Management</h4>
                            <ul>
                                <li>Register new patients with complete information</li>
                                <li>View and edit patient records</li>
                                <li>Track patient history and treatments</li>
                                <li>Manage patient appointments and visits</li>
                            </ul>
                        </div>

                        <div class="help-section">
                            <h4><i class="fas fa-calendar-check"></i> Appointments</h4>
                            <ul>
                                <li>Schedule patient appointments with doctors</li>
                                <li>View daily appointment calendar</li>
                                <li>Manage appointment status (scheduled, confirmed, completed)</li>
                                <li>Send appointment reminders to patients</li>
                            </ul>
                        </div>

                        <div class="help-section">
                            <h4><i class="fas fa-file-medical"></i> Medical Records</h4>
                            <ul>
                                <li>Create and maintain patient medical records</li>
                                <li>Add diagnoses and treatment plans</li>
                                <li>Upload medical documents and images</li>
                                <li>Generate medical reports</li>
                            </ul>
                        </div>

                        <div class="help-section">
                            <h4><i class="fas fa-pills"></i> Medicine Inventory</h4>
                            <ul>
                                <li>Track medicine stock levels</li>
                                <li>Receive alerts for low stock medicines</li>
                                <li>Manage medicine suppliers and purchases</li>
                                <li>Generate inventory reports</li>
                            </ul>
                        </div>

                        <div class="help-section">
                            <h4><i class="fas fa-vial"></i> Laboratory Tests</h4>
                            <ul>
                                <li>Order laboratory tests for patients</li>
                                <li>Record test results and reports</li>
                                <li>Manage test scheduling</li>
                                <li>Track test history</li>
                            </ul>
                        </div>

                        <div class="help-section">
                            <h4><i class="fas fa-file-invoice-dollar"></i> Billing & Invoices</h4>
                            <ul>
                                <li>Create patient invoices for services rendered</li>
                                <li>Track payment status</li>
                                <li>Generate financial reports</li>
                                <li>Manage insurance claims</li>
                            </ul>
                        </div>

                        <div class="help-section">
                            <h4><i class="fas fa-chart-bar"></i> Reports & Analytics</h4>
                            <ul>
                                <li>Generate patient reports</li>
                                <li>View financial analytics</li>
                                <li>Create appointment statistics</li>
                                <li>Export data for external analysis</li>
                            </ul>
                        </div>

                        <div class="help-section">
                            <h4><i class="fas fa-users-cog"></i> User Management</h4>
                            <ul>
                                <li>Add and manage system users</li>
                                <li>Assign roles and permissions</li>
                                <li>Monitor user activity</li>
                                <li>Manage user accounts and access</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Frequently Asked Questions -->
            <div class="content-card mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-question"></i>
                        Frequently Asked Questions
                    </h3>
                </div>
                <div class="card-content">
                    <div class="faq-section">
                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fas fa-chevron-right"></i>
                                How do I reset my password?
                            </div>
                            <div class="faq-answer">
                                Go to Profile > Security Settings and click on "Change Password". Enter your current password and new password to update it.
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fas fa-chevron-right"></i>
                                How do I schedule an appointment?
                            </div>
                            <div class="faq-answer">
                                Navigate to Appointments > New Appointment, select the patient, doctor, date and time, then click "Schedule".
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fas fa-chevron-right"></i>
                                How do I add a new patient?
                            </div>
                            <div class="faq-answer">
                                Go to Patients > Add Patient, fill in all required information including personal details and contact information, then click "Save".
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fas fa-chevron-right"></i>
                                How do I generate reports?
                            </div>
                            <div class="faq-answer">
                                Navigate to Reports section, select the type of report you need, set the date range and filters, then click "Generate Report".
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fas fa-chevron-right"></i>
                                How do I update medicine inventory?
                            </div>
                            <div class="faq-answer">
                                Go to Medicine > Inventory, select the medicine, update the quantity, and save changes. You can also add new medicines from the same section.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                    </div>

        <div class="sidebar-help">
            <!-- Contact Support -->
            <div class="content-card mb-4">
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
                                <small>Response within 24 hours</small>
                            </div>
                        </div>
                        <div class="support-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <strong>Phone Support</strong>
                                <p>+1-800-HMS-HELP</p>
                                <small>Immediate assistance</small>
                            </div>
                        </div>
                        <div class="support-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Support Hours</strong>
                                <p>Monday - Friday: 8:00 AM - 6:00 PM</p>
                                <p>Saturday: 9:00 AM - 2:00 PM</p>
                                <small>Sunday: Closed</small>
                            </div>
                        </div>
                        <div class="support-item">
                            <i class="fas fa-comments"></i>
                            <div>
                                <strong>Live Chat</strong>
                                <p>Available during business hours</p>
                                <button class="btn btn-primary btn-sm mt-2">Start Chat</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="content-card mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-link"></i>
                        Quick Links
                    </h3>
                </div>
                <div class="card-content">
                    <div class="quick-links">
                        <a href="/dashboard" class="quick-link">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                        <a href="/profile" class="quick-link">
                            <i class="fas fa-user"></i>
                            My Profile
                        </a>
                        <a href="/patients" class="quick-link">
                            <i class="fas fa-user-injured"></i>
                            Patients
                        </a>
                        <a href="/appointments" class="quick-link">
                            <i class="fas fa-calendar"></i>
                            Appointments
                        </a>
                        <a href="/reports" class="quick-link">
                            <i class="fas fa-chart-bar"></i>
                            Reports
                        </a>
                        <a href="/users" class="quick-link">
                            <i class="fas fa-users"></i>
                            User Management
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="content-card mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-server"></i>
                        System Status
                    </h3>
                </div>
                <div class="card-content">
                    <div class="system-status">
                        <div class="status-item">
                            <div class="status-indicator online"></div>
                            <div>
                                <strong>All Systems Operational</strong>
                                <p>No issues reported</p>
                            </div>
                        </div>
                        <div class="status-item">
                            <div class="status-indicator online"></div>
                            <div>
                                <strong>Database</strong>
                                <p>Normal performance</p>
                            </div>
                        </div>
                        <div class="status-item">
                            <div class="status-indicator online"></div>
                            <div>
                                <strong>API Services</strong>
                                <p>Running normally</p>
                            </div>
                        </div>
                        <div class="status-item">
                            <div class="status-indicator online"></div>
                            <div>
                                <strong>Backup Systems</strong>
                                <p>Last backup: <?= date('M j, Y, g:i A') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Resources -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-download"></i>
                        Help Resources
                    </h3>
                </div>
                <div class="card-content">
                    <div class="help-resources">
                        <a href="#" class="resource-item">
                            <i class="fas fa-file-pdf"></i>
                            <div>
                                <strong>User Manual</strong>
                                <p>Complete PDF guide</p>
                            </div>
                        </a>
                        <a href="#" class="resource-item">
                            <i class="fas fa-file-excel"></i>
                            <div>
                                <strong>Quick Reference</strong>
                                <p>Cheat sheet for common tasks</p>
                            </div>
                        </a>
                        <a href="#" class="resource-item">
                            <i class="fas fa-file-powerpoint"></i>
                            <div>
                                <strong>Training Materials</strong>
                                <p>Staff training presentation</p>
                            </div>
                        </a>
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

.main-help-section {
    display: flex;
    flex-direction: column;
}

.sidebar-help {
    display: flex;
    flex-direction: column;
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.card-title {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-content {
    padding: 25px;
}

/* Search Styles */
.search-container {
    position: relative;
}

.search-input {
    padding: 12px 20px;
    border: 2px solid #e9ecef;
    border-radius: 25px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    margin-top: 5px;
    max-height: 300px;
    overflow-y: auto;
    display: none;
    z-index: 1000;
}

/* Help Sections */
.help-sections {
    display: grid;
    gap: 20px;
}

.help-section {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #667eea;
}

.help-section h4 {
    color: #2c3e50;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.1rem;
}

.help-section ul {
    margin: 0;
    padding-left: 20px;
}

.help-section li {
    margin-bottom: 8px;
    color: #495057;
    line-height: 1.5;
}

/* FAQ Styles */
.faq-section {
    display: grid;
    gap: 15px;
}

.faq-item {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.faq-question {
    padding: 15px 20px;
    background: #f8f9fa;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    color: #2c3e50;
    transition: all 0.3s ease;
}

.faq-question:hover {
    background: #e9ecef;
}

.faq-question i {
    transition: transform 0.3s ease;
}

.faq-question.active i {
    transform: rotate(90deg);
}

.faq-answer {
    padding: 15px 20px;
    background: white;
    color: #495057;
    line-height: 1.6;
    display: none;
}

.faq-answer.active {
    display: block;
}


/* Support Info */
.support-info {
    display: grid;
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
    color: #667eea;
    font-size: 1.2rem;
    margin-top: 2px;
}

.support-item strong {
    color: #2c3e50;
    display: block;
    margin-bottom: 5px;
}

.support-item p {
    margin: 0 0 5px 0;
    color: #495057;
}

.support-item small {
    color: #6c757d;
    font-size: 0.85rem;
}

/* Quick Links */
.quick-links {
    display: grid;
    gap: 10px;
}

.quick-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 15px;
    background: #f8f9fa;
    border-radius: 8px;
    text-decoration: none;
    color: #495057;
    transition: all 0.3s ease;
}

.quick-link:hover {
    background: #667eea;
    color: white;
    transform: translateX(5px);
}

.quick-link i {
    font-size: 1rem;
}

/* System Status */
.system-status {
    display: grid;
    gap: 15px;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
}

.status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.status-indicator.online {
    background: #28a745;
    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.2);
}

.status-indicator.warning {
    background: #ffc107;
    box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.2);
}

.status-indicator.error {
    background: #dc3545;
    box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.2);
}

.status-item strong {
    color: #2c3e50;
    display: block;
    font-size: 0.9rem;
}

.status-item p {
    margin: 0;
    color: #6c757d;
    font-size: 0.8rem;
}

/* Help Resources */
.help-resources {
    display: grid;
    gap: 15px;
}

.resource-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    text-decoration: none;
    color: #495057;
    transition: all 0.3s ease;
}

.resource-item:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
}

.resource-item i {
    font-size: 1.5rem;
    color: #667eea;
}

.resource-item:hover i {
    color: white;
}

.resource-item strong {
    color: inherit;
    display: block;
    margin-bottom: 3px;
}

.resource-item p {
    margin: 0;
    color: inherit;
    font-size: 0.85rem;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .help-content {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .help-sections {
        gap: 15px;
    }
    
    .help-section {
        padding: 15px;
    }
    
    .support-item {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Accordion
    const faqQuestions = document.querySelectorAll('.faq-question');
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const answer = this.nextElementSibling;
            const isActive = this.classList.contains('active');
            
            // Close all other FAQs
            faqQuestions.forEach(q => {
                q.classList.remove('active');
                q.nextElementSibling.classList.remove('active');
            });
            
            // Toggle current FAQ
            if (!isActive) {
                this.classList.add('active');
                answer.classList.add('active');
            }
        });
    });
    
    // Search Functionality
    const searchInput = document.getElementById('helpSearch');
    const searchResults = document.getElementById('searchResults');
    
    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }
            
            // Simple search implementation (can be enhanced)
            const searchableContent = [
                { title: 'Getting Started', content: 'Login, dashboard, navigation, profile' },
                { title: 'Patient Management', content: 'Register patients, view records, track history' },
                { title: 'Appointments', content: 'Schedule, calendar, manage status, reminders' },
                { title: 'Medical Records', content: 'Create records, diagnoses, treatments, documents' },
                { title: 'Medicine Inventory', content: 'Stock levels, alerts, suppliers, purchases' },
                { title: 'Laboratory Tests', content: 'Order tests, results, scheduling, history' },
                { title: 'Billing & Invoices', content: 'Create invoices, payments, reports, insurance' },
                { title: 'Reports & Analytics', content: 'Patient reports, financial analytics, statistics' },
                { title: 'User Management', content: 'Add users, roles, permissions, activity' },
                { title: 'Password Reset', content: 'Security settings, change password, profile' }
            ];
            
            const results = searchableContent.filter(item => 
                item.title.toLowerCase().includes(query) || 
                item.content.toLowerCase().includes(query)
            );
            
            if (results.length > 0) {
                searchResults.innerHTML = results.map(item => `
                    <div class="search-result-item" style="padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #f8f9fa;">
                        <div style="font-weight: 600; color: #2c3e50;">${item.title}</div>
                        <div style="font-size: 0.9rem; color: #6c757d;">${item.content}</div>
                    </div>
                `).join('');
                searchResults.style.display = 'block';
            } else {
                searchResults.innerHTML = '<div style="padding: 15px; text-align: center; color: #6c757d;">No results found</div>';
                searchResults.style.display = 'block';
            }
        });
        
        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    }
    
    // Live Chat Button (placeholder)
    const chatButton = document.querySelector('button:contains("Start Chat")');
    if (chatButton) {
        chatButton.addEventListener('click', function() {
            alert('Live chat feature coming soon! Please contact support via email or phone.');
        });
    }
});
</script>

<?= $this->endSection() ?>
