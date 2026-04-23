<?php
?>

<nav class="navbar navbar-expand-lg navbar-light navbar-modern sticky-top">
    <div class="container">
        <!-- Brand/Logo -->
        <a class="navbar-brand fw-bold" href="/">
            <i class="fas fa-coffee" style="margin-right: 8px;"></i>Cafeteria Manager
        </a>
        
        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Navigation Content -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="/"><i class="fas fa-home"></i><span class="d-lg-none ms-2">Home</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="/products"><i class="fas fa-coffee"></i><span class="d-lg-none ms-2">Products</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="/about"><i class="fas fa-info-circle"></i><span class="d-lg-none ms-2">About</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="/contact"><i class="fas fa-envelope"></i><span class="d-lg-none ms-2">Contact</span></a></li>
                    <li class="nav-item">
                        <a class="btn btn-primary-modern" href="/login" style="font-size: 0.9rem; padding: 8px 16px;">
                           <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                    </li>
                <?php elseif (($_SESSION['role'] ?? 'user') === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="/"><i class="fas fa-chart-line"></i><span class="d-lg-none ms-2">Dashboard</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/products"><i class="fas fa-boxes"></i><span class="d-lg-none ms-2">Products</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/orders"><i class="fas fa-clipboard-list"></i><span class="d-lg-none ms-2">Orders</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/messages"><i class="fas fa-inbox"></i><span class="d-lg-none ms-2">Messages</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/users"><i class="fas fa-users"></i><span class="d-lg-none ms-2">Users</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="/logout"><i class="fas fa-sign-out-alt"></i><span class="d-lg-none ms-2">Logout</span></a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/"><i class="fas fa-home"></i><span class="d-lg-none ms-2">Home</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="/orders"><i class="fas fa-receipt"></i><span class="d-lg-none ms-2">My Orders</span></a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="/orders" style="position: relative;">
                            <i class="fas fa-shopping-cart"></i>
                            <span id="cartCount" style="display: none; position: absolute; top: -8px; right: -8px; background: var(--primary-accent); color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; font-weight: bold;">0</span>
                            <span class="d-lg-none ms-2">Cart</span>
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="/logout"><i class="fas fa-sign-out-alt"></i><span class="d-lg-none ms-2">Logout</span></a></li>
                <?php endif; ?>

                <li class="nav-item">
                    <button class="nav-link btn btn-link" id="darkModeToggle"
                            onclick="toggleDarkMode()"
                            title="Toggle dark mode">
                        <i class="fas fa-moon"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    
    .navbar-modern {
        background: var(--bg-white) !important;
        border-bottom: 1px solid #e8e0d5;
        box-shadow: 0 2px 8px rgba(44, 24, 16, 0.08);
        transition: all 0.3s ease;
    }
    
    .navbar-modern .navbar-brand {
        font-size: 1.4rem;
        color: var(--primary-dark) !important;
        font-weight: 700;
        letter-spacing: -0.5px;
    }
    
    
    .navbar-nav .nav-link {
        color: var(--text-dark) !important;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        align-items: center;
        font-size: 1rem;
    }
    
    .navbar-nav .nav-link:hover {
        color: var(--primary-accent) !important;
    }
    
    
    .navbar-nav .btn-primary-modern {
        border-radius: 8px;
        font-weight: 600;
    }
    
    
        color: var(--text-dark) !important;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
        color: var(--primary-accent) !important;
    }
    
    
    @media (max-width: 991px) {
        .navbar-nav {
            margin-top: 1rem;
            border-top: 1px solid #e8e0d5;
            padding-top: 1rem;
        }
        
        .nav-item {
            margin-bottom: 0.5rem;
        }
        
        .navbar-nav .nav-link {
            padding: 0.75rem 0;
        }
        
        .navbar-nav .btn-primary-modern {
            width: 100%;
            margin-top: 0.5rem;
        }
    }
</style>
