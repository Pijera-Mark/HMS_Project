<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-history"></i>
            Audit Trail
        </h1>
        <div class="page-breadcrumb">
            <a href="/dashboard" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Audit Trail</span>
        </div>
    </div>

    <div class="content-card">
        <div class="card-body text-center py-5">
            <div class="coming-soon-icon">
                <i class="fas fa-tools"></i>
            </div>
            <h2 class="coming-soon-title">Feature Coming Soon</h2>
            <p class="coming-soon-message"><?= esc($message) ?></p>
            <div class="coming-soon-actions">
                <a href="/dashboard" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
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

.content-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}

.card-body {
    padding: 40px;
}

.coming-soon-icon {
    font-size: 4rem;
    color: #4facfe;
    margin-bottom: 20px;
}

.coming-soon-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 15px;
}

.coming-soon-message {
    font-size: 1.1rem;
    color: #6c757d;
    margin-bottom: 30px;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.coming-soon-actions {
    margin-top: 30px;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);
    color: white;
}

@media (max-width: 1200px) {
    .page-container {
        padding: 15px;
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
