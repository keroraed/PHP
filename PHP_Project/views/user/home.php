<?php
$pageTitle = 'Your Perfect Coffee Moment';
include __DIR__ . '/../layouts/head.php';
?>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section hero-enhanced">
        <div class="hero-background">
            <div class="hero-bg-animated"></div>
            <div class="hero-overlay"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title fade-in-up">Your Perfect Coffee Moment</h1>
                <p class="hero-subtitle fade-in-up" style="animation-delay: 0.2s;">Discover premium beverages and freshly prepared snacks</p>
                <div class="hero-cta-buttons fade-in-up" style="animation-delay: 0.4s;">
                    <a href="/products" class="btn btn-primary-modern btn-lg">
                        <i class="fas fa-coffee me-2"></i>Browse Products
                    </a>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="/register" class="btn btn-light-accent btn-lg">
                        <i class="fas fa-user-shield me-2"></i>Contact Admin
                    </a>
                    <?php else: ?>
                    <a href="/orders" class="btn btn-light-accent btn-lg">
                        <i class="fas fa-receipt me-2"></i>My Orders
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="container py-5 stats-section">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="stat-item stat-item-animated">
                    <div class="stat-icon-circle">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="stat-number counter" data-target="500">0</span>
                    <span class="stat-label">Happy Customers</span>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="stat-item stat-item-animated">
                    <div class="stat-icon-circle">
                        <i class="fas fa-box"></i>
                    </div>
                    <span class="stat-number counter" data-target="50">0</span>
                    <span class="stat-label">Premium Products</span>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="stat-item stat-item-animated">
                    <div class="stat-icon-circle">
                        <i class="fas fa-clock"></i>
                    </div>
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Available Service</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="featured-section featured-enhanced">
        <div class="featured-bg"></div>
        <div class="container">
            <h2 class="section-title fade-in">Featured Products</h2>
            <p class="text-center text-muted mb-5 fade-in" style="animation-delay: 0.2s;">Hand-picked selection of our finest items</p>
            
            <div id="featuredProductsContainer" class="row">
                <!-- Products will be loaded here via JavaScript -->
                <div class="col-md-4 mb-4">
                    <div class="skeleton skeleton-product"></div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="skeleton skeleton-product"></div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="skeleton skeleton-product"></div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="skeleton skeleton-product"></div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="skeleton skeleton-product"></div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="skeleton skeleton-product"></div>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="/products" class="btn btn-primary-modern btn-lg">
                    <i class="fas fa-arrow-right me-2"></i>View All Products
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section features-enhanced py-5">
        <div class="features-bg"></div>
        <div class="container">
            <div class="row features-row">
                <div class="col-md-4 mb-4">
                    <div class="card-modern hover-lift feature-card">
                        <div class="feature-icon-bg"></div>
                        <div class="card-body-modern text-center">
                            <div class="feature-icon-wrapper">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                            <h5 class="fw-bold">Fast Delivery</h5>
                            <p class="text-muted">Quick and reliable order fulfillment</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card-modern hover-lift feature-card">
                        <div class="feature-icon-bg"></div>
                        <div class="card-body-modern text-center">
                            <div class="feature-icon-wrapper">
                                <i class="fas fa-leaf"></i>
                            </div>
                            <h5 class="fw-bold">Fresh Quality</h5>
                            <p class="text-muted">Only the freshest ingredients used</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card-modern hover-lift feature-card">
                        <div class="feature-icon-bg"></div>
                        <div class="card-body-modern text-center">
                            <div class="feature-icon-wrapper">
                                <i class="fas fa-headset"></i>
                            </div>
                            <h5 class="fw-bold">24/7 Support</h5>
                            <p class="text-muted">Always here to help you out</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search & Browse Section -->
    <section class="py-5 category-section">
        <div class="container">
            <h2 class="mb-4 section-title fade-in">Or Explore by Category</h2>
            <div id="categorySection" class="row category-row">
                <!-- Categories will be loaded via JavaScript -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Scripts -->
    <?php include __DIR__ . '/../layouts/scripts.php'; ?>
    
    <script>
        function animateCounters() {
            const counters = document.querySelectorAll('.counter');
            counters.forEach(counter => {
                const target = parseInt(counter.dataset.target);
                let current = 0;
                const increment = target / 40;
                
                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        counter.textContent = Math.floor(current);
                        setTimeout(updateCounter, 50);
                    } else {
                        counter.textContent = target + '+';
                    }
                };
                
                const observer = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting && !counter.dataset.animated) {
                        counter.dataset.animated = 'true';
                        updateCounter();
                    }
                });
                observer.observe(counter);
            });
        }

        window.addEventListener('scroll', () => {
            const hero = document.querySelector('.hero-enhanced');
            if (hero) {
                const scrolled = window.pageYOffset;
                const bg = hero.querySelector('.hero-bg-animated');
                if (bg) bg.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            animateCounters();
            
            setTimeout(() => {
                const categoryItems = document.querySelectorAll('.category-row > div, .category-item-animated');
                categoryItems.forEach((item, index) => {
                    if (item.style.animation === '') {
                        item.style.animation = `fadeInUp 0.6s ease-out ${index * 0.1}s both`;
                    }
                });
            }, 500);
        });

        const isAdmin = <?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'true' : 'false'; ?>;

        document.addEventListener('DOMContentLoaded', async function() {
            await loadFeaturedProducts();
            await loadCategories();
        });

        async function loadFeaturedProducts() {
            const container = document.getElementById('featuredProductsContainer');
            
            try {
                const response = await Utils.apiRequest('/api/products');
                
                if (response.success) {
                    const products = response.data.data.slice(0, 6);
                    
                    if (products.length === 0) {
                        container.innerHTML = '<div class="col-12"><p class="text-center text-muted py-5">No products available yet.</p></div>';
                        return;
                    }

                    let html = '';
                    products.forEach(product => {
                        const imageUrl = Utils.getProductImageUrl(product.image);
                        const actionBtn = isAdmin
                            ? `<a href="/admin/products" class="btn btn-primary-modern btn-sm"><i class="fas fa-pen"></i></a>`
                            : `<button class="btn btn-primary-modern btn-sm" onclick="addToCart(${product.id})"><i class="fas fa-plus"></i></button>`;
                        const categoryBadge = product.category_id ? `<span class="badge badge-info me-2">Category ${product.category_id}</span>` : '';
                        
                        html += `
                            <div class="col-md-4 col-sm-6 mb-4">
                                <div class="card-modern product-card-featured hover-lift">
                                    <div class="card-body-modern">
                                        <div class="text-center mb-3 product-img-container">
                                            ${imageUrl
                                                ? `<img src="${imageUrl}" alt="${product.name}" class="product-img-thumb">`
                                                : `<i class="fas fa-coffee product-img-placeholder-icon"></i>`}
                                        </div>
                                        <h5 class="product-card-title">${product.name}</h5>
                                        <p class="text-muted text-truncate-2 product-card-desc">
                                            ${product.description || 'Premium product from our collection'}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="product-card-price">EGP ${parseFloat(product.price).toFixed(2)}</span>
                                            ${actionBtn}
                                        </div>
                                        <div class="mt-3">
                                            <a href="/product/${product.id}" class="btn btn-secondary-modern btn-sm w-100">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div class="col-12"><p class="text-center text-danger py-5">Failed to load products.</p></div>';
                }
            } catch (error) {
                console.error('Error loading products:', error);
                container.innerHTML = '<div class="col-12"><p class="text-center text-danger py-5">Error loading products.</p></div>';
            }
        }

        async function loadCategories() {
            const container = document.getElementById('categorySection');
            
            container.innerHTML = '<div class="col-12 text-center py-4"><div class="spinner-border text-primary spinner-sm"></div></div>';
            
            try {
                const response = await Utils.apiRequest('/api/categories');
                
                if (response.success && response.data && response.data.data) {
                    const categories = response.data.data;
                    
                    if (categories.length === 0) {
                        container.innerHTML = '<div class="col-12"><p class="text-center text-muted py-5">No categories available. Check back soon!</p></div>';
                        return;
                    }

                    let html = '';
                    categories.forEach((category, index) => {
                        const icons = ['fa-coffee', 'fa-leaf', 'fa-cookie', 'fa-cake-candles'];
                        const icon = icons[index % icons.length];
                        
                        html += `
                            <div class="col-md-3 col-sm-6 mb-4 category-item-animated">
                                <a href="/products?category=${category.id}" class="text-decoration-none">
                                    <div class="card-modern hover-lift category-card">
                                        <div class="mb-3 category-card-icon">
                                            <i class="fas ${icon} fa-3x"></i>
                                        </div>
                                        <h6>${category.name}</h6>
                                        <p class="text-muted small mb-0">${category.description || 'Browse items'}</p>
                                    </div>
                                </a>
                            </div>
                        `;
                    });

                    container.innerHTML = html;
                    
                    const items = container.querySelectorAll('.category-item-animated');
                    items.forEach((item, idx) => {
                        item.style.opacity = '0';
                        item.style.animation = `fadeInUp 0.6s ease-out ${idx * 0.1}s forwards`;
                    });
                } else {
                    console.warn('Invalid response format:', response);
                    container.innerHTML = '<div class="col-12"><p class="text-center text-muted py-5">Unable to load categories. Please try again later.</p></div>';
                }
            } catch (error) {
                console.error('Error loading categories:', error);
                container.innerHTML = '<div class="col-12"><p class="text-center text-danger py-5"><i class="fas fa-exclamation-circle me-2"></i>Unable to load categories. Please refresh the page.</p></div>';
            }
        }
    </script>
</body>
</html>
