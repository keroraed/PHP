<?php
$pageTitle = 'Login';
$bodyClass = 'login-page';
include __DIR__ . '/../layouts/head.php';
?>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>☕</h1>
                <h2>Welcome Back</h2>
                <p>Sign in to your Premium Cafeteria account</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    <?= htmlspecialchars($_GET['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i>
                    <?= htmlspecialchars($_GET['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="/login" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label-modern">📧 Email Address</label>
                    <input type="email" id="email" name="email" class="form-control-modern"
                           placeholder="your@email.com" required autocomplete="email">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label-modern">🔒 Password</label>
                    <input type="password" id="password" name="password" class="form-control-modern"
                           placeholder="••••••••" required autocomplete="current-password">
                </div>

                <button type="submit" class="btn-login mt-2">
                    <i class="fas fa-sign-in-alt me-1"></i> Login
                </button>
            </form>

            <div class="login-footer mt-3">
                Don't have an account? <a href="/register">Create one</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
