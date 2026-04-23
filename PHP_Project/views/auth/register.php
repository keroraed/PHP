<?php
$pageTitle = 'Registration';
$bodyClass = 'login-page';
include __DIR__ . '/../layouts/head.php';
?>
    <div class="login-container" style="max-width: 560px;">
        <div class="login-card">
            <div class="login-header">
                <h1><i class="fas fa-user-lock"></i></h1>
                <h2>Registration Disabled</h2>
                <p>Only admin can create new user accounts.</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    <?= htmlspecialchars($_GET['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-1"></i>
                To create a new account, please contact your system administrator.
            </div>

            <div class="d-grid gap-2 mt-3">
                <a href="/contact" class="btn-login text-center" style="text-decoration: none;">
                    <i class="fas fa-envelope me-1"></i> Contact Admin
                </a>
                <a href="/login" class="btn btn-outline-secondary">
                    <i class="fas fa-sign-in-alt me-1"></i> Back to Login
                </a>
            </div>

            <div class="login-footer mt-3">
                Need access? Please ask admin to create your account.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
