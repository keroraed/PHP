<?php
$pageTitle = 'Product Details';
include __DIR__ . '/../layouts/head.php';
?>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <!-- Product Detail Container -->
    <div class="container mt-5 mb-5">
        <div id="productDetail" class="row">
            <div class="col-12 text-center py-5">
                <div class="spinner-border" style="color: var(--primary-dark);"></div>
                <p class="mt-3">Loading product details...</p>
            </div>
        </div>
    </div>

    <!-- Related Products Section -->
    <section class="py-5" style="background: var(--bg-light);">
        <div class="container">
            <h2 class="section-title mb-4">Related Products</h2>
            <div id="relatedProducts" class="row">
                <!-- Loaded via JS -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <?php include __DIR__ . '/../layouts/scripts.php'; ?>
    <script>
        const productId = window.location.pathname.split('/').pop();

        document.addEventListener('DOMContentLoaded', async function() {
            await loadProductDetails();
        });

        async function loadProductDetails() {
            const container = document.getElementById('productDetail');
            try {
                const response = await Utils.apiRequest(`/api/products/${productId}`);
                if (response.success) {
                    const product = response.data.data;
                    displayProductDetails(product);
                    await loadRelatedProducts(product.category_id);
                } else {
                    container.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>Product not found
                            </div>
                            <a href="/products" class="btn btn-primary-modern">Back to Products</a>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading product:', error);
                container.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>Error loading product details
                        </div>
                        <a href="/products" class="btn btn-primary-modern">Back to Products</a>
                    </div>
                `;
            }
        }


        function displayProductDetails(product) {
            const container = document.getElementById('productDetail');
            const imageUrl = Utils.getProductImageUrl(product.image);

            container.innerHTML = `
                <div class="col-md-6 mb-4">
                    <div class="card-modern detail-card-padding text-center">
                        <div class="product-img-container mb-3" style="min-height:280px;">
                            ${imageUrl
                                ? `<img src="${imageUrl}" alt="${product.name}" class="product-img-detail">`
                                : `<i class="fas fa-coffee product-img-placeholder-icon" style="font-size:8rem;"></i>`}
                        </div>
                        <p class="text-muted small">Product Image</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card-modern detail-card-padding">
                        <nav class="mb-3">
                            <a href="/products" class="text-decoration-none text-muted">
                                <i class="fas fa-arrow-left me-1"></i> Back to Products
                            </a>
                        </nav>

                        <h1 class="fw-bold mb-3">${product.name}</h1>

                        <div class="stars mb-3">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            <span class="text-muted small ms-2">(125 reviews)</span>
                        </div>

                        <div class="mb-4">
                            <span class="product-detail-price">
                                EGP ${parseFloat(product.price).toFixed(2)}
                            </span>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold mb-2">Description</h5>
                            <p class="text-muted">
                                ${product.description || 'This is a premium product from our carefully curated collection.'}
                            </p>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold mb-2">Details</h5>
                            <ul class="list-unstyled mb-0">
                                <li class="py-2 border-bottom text-muted">
                                    <i class="fas fa-tag me-2" style="color: var(--primary-dark);"></i>
                                    <strong>Category:</strong> Category #${product.category_id}
                                </li>
                                <li class="py-2 text-muted">
                                    <i class="fas fa-check me-2" style="color: var(--primary-dark);"></i>
                                    <strong>In Stock:</strong> Yes
                                </li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <label class="form-label-modern">Quantity</label>
                            <div class="qty-selector">
                                <button type="button" class="qty-btn" onclick="decreaseQty()">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="quantity" class="qty-input" value="1" min="1" max="100">
                                <button type="button" class="qty-btn" onclick="increaseQty()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary-modern btn-lg" onclick="addProductToCart(${product.id})">
                                <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                            </button>
                            <button class="btn btn-secondary-modern btn-lg" id="wishlistBtn"
                                    onclick="toggleProductWishlist(${product.id})">
                                <i class="far fa-heart me-2"></i> Add to Wishlist
                            </button>
                        </div>

                        <div class="shipping-info-box mt-4">
                            <p class="mb-2">
                                <i class="fas fa-shipping-fast me-2" style="color: var(--primary-dark);"></i>
                                <strong>Free Shipping</strong> on orders over EGP 500
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-undo me-2" style="color: var(--primary-dark);"></i>
                                <strong>30-day Returns</strong> if not satisfied
                            </p>
                        </div>
                    </div>
                </div>
            `;
        }

        async function loadRelatedProducts(categoryId) {
            const container = document.getElementById('relatedProducts');
            try {
                const response = await Utils.apiRequest('/api/products');
                if (!response.success) return;

                const related = (response.data.data || [])
                    .filter(p => p.category_id == categoryId && p.id != productId)
                    .slice(0, 4);

                if (!related.length) {
                    container.innerHTML = '<div class="col-12 text-center text-muted py-5">No related products found</div>';
                    return;
                }

                container.innerHTML = related.map(product => {
                    const imgUrl = Utils.getProductImageUrl(product.image);
                    return `
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card-modern product-card-featured hover-lift h-100 d-flex flex-column">
                                <div class="card-body-modern d-flex flex-column">
                                    <div class="product-img-container text-center mb-3">
                                        ${imgUrl
                                            ? `<img src="${imgUrl}" alt="${product.name}" class="product-img-thumb">`
                                            : `<i class="fas fa-coffee product-img-placeholder-icon fa-3x"></i>`}
                                    </div>
                                    <h6 class="product-card-title">${product.name}</h6>
                                    <p class="product-card-price mb-3">
                                        EGP ${parseFloat(product.price).toFixed(2)}
                                    </p>
                                    <div class="d-grid gap-2 mt-auto">
                                        <button class="btn btn-primary-modern btn-sm"
                                                onclick="addProductToCart(${product.id})">
                                            <i class="fas fa-plus me-1"></i> Add
                                        </button>
                                        <a href="/product/${product.id}" class="btn btn-secondary-modern btn-sm">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            } catch (error) {
                console.error('Error loading related products:', error);
            }
        }

        function increaseQty() {
            const input = document.getElementById('quantity');
            input.value = parseInt(input.value) + 1;
        }

        function decreaseQty() {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
        }

        async function addProductToCart(id) {
            const quantity = parseInt(document.getElementById('quantity')?.value) || 1;
            await addToCart(id, quantity);
        }

        function toggleProductWishlist(id) {
            const btn = document.getElementById('wishlistBtn');
            btn.classList.toggle('active');
            const isActive = btn.classList.contains('active');
            btn.innerHTML = isActive
                ? '<i class="fas fa-heart me-2"></i> Remove from Wishlist'
                : '<i class="far fa-heart me-2"></i> Add to Wishlist';
            window.toast?.success(isActive ? 'Added to wishlist' : 'Removed from wishlist');
        }
    </script>
</body>
</html>
