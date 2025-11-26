<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc(get_setting('hospital_name', 'Hospital Management System')) ?></title>
    <meta name="description" content="<?= esc(get_setting('hospital_name', 'Hospital Management System')) ?> - Integrated Hospital Management">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/custom.css">

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <style>
            .flash-error {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
            }
        </style>
    <?php endif; ?>

</head>
<body class="sidebar-layout">

<!-- Sidebar -->
<?php if (session()->get('user')): ?>
<?= view('components/sidebar', ['user' => session()->get('user'), 'stats' => $stats ?? []]) ?>
<?php endif; ?>

<!-- Main Content -->
<div class="main-content" style="margin-left: 280px; min-height: 100vh; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); padding: 20px; transition: margin-left 0.3s ease; position: relative;">
    <!-- Background Pattern -->
    <div style="position: fixed; top: 0; left: 280px; right: 0; bottom: 0; background-image: radial-gradient(circle at 20% 80%, rgba(102, 126, 234, 0.1) 0%, transparent 50%), radial-gradient(circle at 80% 20%, rgba(118, 75, 162, 0.1) 0%, transparent 50%); pointer-events: none; z-index: 0;"></div>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 280px; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.95); z-index: 9998; justify-content: center; align-items: center; backdrop-filter: blur(10px);">
        <div style="text-align: center;">
            <div style="width: 50px; height: 50px; border: 4px solid #f3f3f3; border-top: 4px solid #667eea; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
            <div style="color: #667eea; font-weight: 600; font-size: 1.1rem;">Loading...</div>
        </div>
    </div>
    
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show flash-error" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show flash-success" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <section style="position: relative; z-index: 1;">
        <?= $this->renderSection('content') ?>
    </section>
</div>

<style>
/* Loading animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Prevent layout shift */
.sidebar-layout {
    overflow-x: hidden;
}

.main-content {
    will-change: margin-left;
}

/* Smooth transitions */
* {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .main-content {
        margin-left: 0 !important;
        padding: 15px;
    }
    
    .sidebar-container {
        transform: translateX(-100%);
    }
    
    .sidebar-container.mobile-open {
        transform: translateX(0);
    }
    
    #loadingOverlay {
        left: 0 !important;
    }
}
</style>

<script>
// Prevent layout shifts and smooth loading
document.addEventListener('DOMContentLoaded', function() {
    // Show loading overlay when navigating
    const loadingOverlay = document.getElementById('loadingOverlay');
    const links = document.querySelectorAll('a[href]');
    
    links.forEach(link => {
        if (!link.href.includes('#') && !link.href.includes('logout') && link.hostname === window.location.hostname) {
            link.addEventListener('click', function(e) {
                if (!e.ctrlKey && !e.metaKey) {
                    loadingOverlay.style.display = 'flex';
                }
            });
        }
    });
    
    // Hide loading overlay when page is fully loaded
    window.addEventListener('load', function() {
        loadingOverlay.style.display = 'none';
    });
    
    // Hide loading overlay immediately if content is ready
    if (document.readyState === 'complete') {
        loadingOverlay.style.display = 'none';
    }
    
    // Mobile sidebar toggle
    if (window.innerWidth <= 1200) {
        const toggleBtn = document.createElement('button');
        toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
        toggleBtn.className = 'mobile-sidebar-toggle';
        toggleBtn.style.cssText = `
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: #4facfe;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 15px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
        `;
        
        document.body.appendChild(toggleBtn);
        
        toggleBtn.addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar-container');
            if (sidebar) {
                sidebar.classList.toggle('mobile-open');
            }
        });
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar-container');
            const toggle = document.querySelector('.mobile-sidebar-toggle');
            
            if (sidebar && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });
    }
    
    // Smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-hide flash messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });
});
</script>

</body>
</html>