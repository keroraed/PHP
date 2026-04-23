<?php
$pageTitle = 'Menu';
include __DIR__ . '/layouts/head.php';
?>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1><i class="fas fa-utensils me-2"></i>Our Menu</h1>
            <p>Browse, filter, and discover your next favorite item</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5 mb-5">
        <div class="row">
            <!-- Sidebar - Filters -->
            <div class="col-lg-3 mb-4">
                <div class="card-modern filters-sticky">
                    <div class="card-header-modern">
                        <h5><i class="fas fa-filter me-2"></i>Filters</h5>
                    </div>
                    <div class="card-body-modern">
                        <!-- Search -->
                        <div class="mb-4">
                            <label class="form-label-modern">Search</label>
                            <div class="search-bar-modern">
                                <input type="text" id="searchInput" class="search-input-modern" 
                                       placeholder="Search products...">
                                <i class="fas fa-search search-icon-modern"></i>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-4">
                            <label class="form-label-modern">Category</label>
                            <select id="categoryFilter" class="form-select-modern">
                                <option value="">All Categories</option>
                            </select>
                        </div>

                        <!-- Price Filter -->
                        <div class="mb-4">
                            <label class="form-label-modern">Price Range</label>
                            <div class="input-group mb-2">
                                <input type="number" id="priceMin" class="form-control-modern" 
                                       placeholder="Min" min="0">
                            </div>
                            <div class="input-group">
                                <input type="number" id="priceMax" class="form-control-modern" 
                                       placeholder="Max" min="0">
                            </div>
                        </div>

                        <!-- Apply Filters Button -->
                        <button class="btn btn-primary-modern w-100" onclick="applyFilters()">
                            <i class="fas fa-check me-2"></i>Apply Filters
                        </button>

                        <button class="btn btn-secondary-modern w-100 mt-2" onclick="resetFilters()">
                            <i class="fas fa-rotate-left me-2"></i>Clear All
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Products Area -->
            <div class="col-lg-9">
                <div class="results-toolbar mb-3">
                    <div id="resultsMeta" class="results-meta">Loading menu items...</div>
                    <div class="view-toggle-group" aria-label="View mode toggle">
                        <button id="gridViewBtn" class="view-toggle-btn active" type="button" onclick="setViewMode('grid')" title="Grid view">
                            <i class="fas fa-grip"></i>
                        </button>
                        <button id="listViewBtn" class="view-toggle-btn" type="button" onclick="setViewMode('list')" title="List view">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>

                <!-- Sort & View Controls -->
                <div class="controls-bar mb-4">
                    <div class="control-item">
                        <label class="form-label-modern">Sort By</label>
                        <select id="sortBy" class="form-select-modern" onchange="applyFilters()">
                            <option value="newest">Newest</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="name">Name: A to Z</option>
                        </select>
                    </div>
                    <div class="control-item">
                        <label class="form-label-modern">Items per Page</label>
                        <select id="perPage" class="form-select-modern" onchange="applyFilters()">
                            <option value="6">6 Items</option>
                            <option value="12">12 Items</option>
                            <option value="24">24 Items</option>
                        </select>
                    </div>
                </div>

                <div id="quickCategoryChips" class="quick-category-chips mb-4">
                    <!-- Category chips are rendered dynamically -->
                </div>

                <!-- Products Grid -->
                <div id="productsGrid" class="row mb-5 products-grid-enhanced">
                    <!-- Products will be loaded here -->
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-3">Loading products...</p>
                    </div>
                </div>

                <!-- Pagination -->
                <nav id="paginationContainer" style="display: none;">
                    <ul id="pagination" class="pagination-modern"></ul>
                </nav>

                <!-- Empty State -->
                <div id="emptyState" style="display: none;">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4>No Products Found</h4>
                        <p>Try adjusting your filters or search terms</p>
                        <button class="btn btn-primary-modern mt-3" onclick="resetFilters()">
                            <i class="fas fa-redo me-2"></i>Reset Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <!-- Scripts -->
    <?php include __DIR__ . '/layouts/scripts.php'; ?>

    <script>
        const isAdmin = <?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'true' : 'false'; ?>;
        let allProducts = [];
        let filteredProducts = [];
        let currentPage = 1;
        let currentView = 'grid';
        const itemsPerPageOptions = [6, 12, 24];

        document.addEventListener('DOMContentLoaded', async function() {
            await loadCategories();
            hydrateFiltersFromUrl();
            await loadAllProducts();
            renderQuickCategoryChips();
            applyFilters(false);
            initializeViewMode();

            ['categoryFilter', 'priceMin', 'priceMax', 'sortBy', 'perPage'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', () => applyFilters());
                }
            });
        });

        function hydrateFiltersFromUrl() {
            const params = new URLSearchParams(window.location.search);

            const category = params.get('category') || '';
            const search = params.get('search') || '';
            const min = params.get('min') || '';
            const max = params.get('max') || '';
            const sort = params.get('sort') || 'newest';
            const perPage = params.get('perPage') || '6';
            const view = params.get('view') || localStorage.getItem('products-view-mode') || 'grid';

            document.getElementById('categoryFilter').value = category;
            document.getElementById('searchInput').value = search;
            document.getElementById('priceMin').value = min;
            document.getElementById('priceMax').value = max;
            document.getElementById('sortBy').value = sort;

            if (itemsPerPageOptions.includes(parseInt(perPage))) {
                document.getElementById('perPage').value = perPage;
            }

            currentView = view === 'list' ? 'list' : 'grid';
        }

        function syncUrlState() {
            const params = new URLSearchParams();

            const search = document.getElementById('searchInput').value.trim();
            const category = document.getElementById('categoryFilter').value;
            const min = document.getElementById('priceMin').value;
            const max = document.getElementById('priceMax').value;
            const sort = document.getElementById('sortBy').value;
            const perPage = document.getElementById('perPage').value;

            if (search) params.set('search', search);
            if (category) params.set('category', category);
            if (min) params.set('min', min);
            if (max) params.set('max', max);
            if (sort && sort !== 'newest') params.set('sort', sort);
            if (perPage && perPage !== '6') params.set('perPage', perPage);
            if (currentView === 'list') params.set('view', 'list');

            const query = params.toString();
            const nextUrl = query ? `${window.location.pathname}?${query}` : window.location.pathname;
            history.replaceState(null, '', nextUrl);
        }

        function renderQuickCategoryChips() {
            const container = document.getElementById('quickCategoryChips');
            const categorySelect = document.getElementById('categoryFilter');
            if (!container || !categorySelect) return;

            let html = '<button class="chip-btn active" data-category="">All</button>';
            Array.from(categorySelect.options)
                .filter(opt => opt.value)
                .forEach(opt => {
                    html += `<button class="chip-btn" data-category="${opt.value}">${opt.textContent}</button>`;
                });

            container.innerHTML = html;

            container.querySelectorAll('.chip-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    categorySelect.value = this.dataset.category || '';
                    applyFilters();
                });
            });
        }

        function refreshActiveCategoryChip() {
            const activeCategory = document.getElementById('categoryFilter').value;
            document.querySelectorAll('#quickCategoryChips .chip-btn').forEach(btn => {
                btn.classList.toggle('active', (btn.dataset.category || '') === activeCategory);
            });
        }

        function initializeViewMode() {
            setViewMode(currentView, false);
        }

        function setViewMode(mode, updateUrl = true) {
            currentView = mode === 'list' ? 'list' : 'grid';
            localStorage.setItem('products-view-mode', currentView);

            const grid = document.getElementById('productsGrid');
            const gridBtn = document.getElementById('gridViewBtn');
            const listBtn = document.getElementById('listViewBtn');

            if (grid) {
                grid.classList.toggle('list-view', currentView === 'list');
            }
            if (gridBtn && listBtn) {
                gridBtn.classList.toggle('active', currentView === 'grid');
                listBtn.classList.toggle('active', currentView === 'list');
            }

            if (updateUrl) syncUrlState();
        }

        async function loadAllProducts() {
            try {
                const response = await Utils.apiRequest('/api/products');
                if (response.success) {
                    allProducts = response.data.data || [];
                    return true;
                }
            } catch (error) {
                console.error('Error loading products:', error);
            }
            return false;
        }

        async function loadCategories() {
            try {
                const response = await Utils.apiRequest('/api/categories');
                if (response.success) {
                    const categorySelect = document.getElementById('categoryFilter');
                    const categories = response.data.data || [];
                    
                    categories.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.id;
                        option.textContent = cat.name;
                        categorySelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        function applyFilters(updateUrl = true) {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const categoryId = document.getElementById('categoryFilter').value;
            const priceMin = parseFloat(document.getElementById('priceMin').value) || 0;
            const priceMax = parseFloat(document.getElementById('priceMax').value) || Infinity;
            const sortBy = document.getElementById('sortBy').value;

            filteredProducts = allProducts.filter(product => {
                const matchesSearch = !searchTerm || 
                    product.name.toLowerCase().includes(searchTerm) ||
                    (product.description && product.description.toLowerCase().includes(searchTerm));
                
                const matchesCategory = !categoryId || product.category_id == categoryId;
                const matchesPrice = product.price >= priceMin && product.price <= priceMax;

                return matchesSearch && matchesCategory && matchesPrice;
            });

            switch(sortBy) {
                case 'price-low':
                    filteredProducts.sort((a, b) => a.price - b.price);
                    break;
                case 'price-high':
                    filteredProducts.sort((a, b) => b.price - a.price);
                    break;
                case 'name':
                    filteredProducts.sort((a, b) => a.name.localeCompare(b.name));
                    break;
                default: // newest
                    filteredProducts.sort((a, b) => b.id - a.id);
            }

            currentPage = 1;
            refreshActiveCategoryChip();
            displayProducts();

            if (updateUrl) {
                syncUrlState();
            }
        }

        function displayProducts() {
            const container = document.getElementById('productsGrid');
            const emptyState = document.getElementById('emptyState');
            const itemsPerPage = parseInt(document.getElementById('perPage').value) || 6;

            if (filteredProducts.length === 0) {
                container.style.display = 'none';
                emptyState.style.display = 'block';
                document.getElementById('paginationContainer').style.display = 'none';
                document.getElementById('resultsMeta').textContent = 'No menu items found.';
                return;
            }

            container.style.display = 'flex';
            emptyState.style.display = 'none';

            const startIdx = (currentPage - 1) * itemsPerPage;
            const endIdx = startIdx + itemsPerPage;
            const pageProducts = filteredProducts.slice(startIdx, endIdx);

            updateResultsMeta(startIdx, endIdx, filteredProducts.length, allProducts.length);

            let html = '';
            pageProducts.forEach(product => {
                const imageUrl = Utils.getProductImageUrl(product.image);
                const actionBtn = isAdmin
                    ? `<a href="/admin/products" class="btn btn-primary-modern btn-sm"><i class="fas fa-pen me-1"></i> Edit</a>`
                    : `<button class="btn btn-primary-modern btn-sm" onclick="addToCart(${product.id})"><i class="fas fa-plus me-1"></i> Add Order</button>`;

                html += `
                    <div class="col-md-6 col-lg-4 mb-4 product-item-col">
                        <div class="card-modern product-card-featured hover-lift h-100 d-flex flex-column">
                            <div class="card-body-modern d-flex flex-column">
                                <div class="text-center mb-3" style="font-size: 4rem; min-height: 120px; display: flex; align-items: center; justify-content: center; flex-grow: 1;">
                                    ${imageUrl
                                        ? `<img src="${imageUrl}" alt="${product.name}" style="max-height:120px;max-width:100%;object-fit:contain;border-radius:10px;">`
                                        : `<i class="fas fa-coffee" style="color: var(--primary-dark);"></i>`}
                                </div>
                                <h5 style="color: var(--text-dark); margin-bottom: 0.5rem; text-align: center;">${product.name}</h5>
                                <p class="text-muted text-truncate-2" style="font-size: 0.85rem; margin-bottom: 1rem; text-align: center;">
                                    ${product.description || 'Premium product'}
                                </p>
                                <div class="d-flex justify-content-between align-items-center mb-3 mt-auto">
                                    <span style="color: var(--primary-dark); font-weight: 700; font-size: 1.3rem;">
                                        EGP ${parseFloat(product.price).toFixed(2)}
                                    </span>
                                    <button class="wishlist-btn" onclick="toggleWishlist(${product.id}, this)" title="Add to wishlist">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>
                                <div class="d-grid gap-2">
                                    ${actionBtn}
                                    <a href="/product/${product.id}" class="btn btn-secondary-modern btn-sm">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;

            displayPagination(itemsPerPage);
        }

        function updateResultsMeta(startIdx, endIdx, filteredCount, totalCount) {
            const meta = document.getElementById('resultsMeta');
            if (!meta) return;

            const shownFrom = filteredCount === 0 ? 0 : startIdx + 1;
            const shownTo = Math.min(endIdx, filteredCount);

            meta.textContent = `Showing ${shownFrom}-${shownTo} of ${filteredCount} items${filteredCount !== totalCount ? ` (from ${totalCount})` : ''}`;
        }

        function displayPagination(itemsPerPage) {
            const paginationContainer = document.getElementById('paginationContainer');
            const pagination = document.getElementById('pagination');
            const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);

            if (totalPages <= 1) {
                paginationContainer.style.display = 'none';
                return;
            }

            paginationContainer.style.display = 'block';
            pagination.innerHTML = '';

            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${currentPage - 1}); return false;"><i class="fas fa-chevron-left"></i></a>`;
            pagination.appendChild(prevLi);

            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${i}); return false;">${i}</a>`;
                pagination.appendChild(li);
            }

            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${currentPage + 1}); return false;"><i class="fas fa-chevron-right"></i></a>`;
            pagination.appendChild(nextLi);
        }

        function goToPage(page) {
            const itemsPerPage = parseInt(document.getElementById('perPage').value) || 6;
            const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
            
            if (page >= 1 && page <= totalPages) {
                currentPage = page;
                displayProducts();
                Utils.scrollToElement(document.getElementById('productsGrid'), 150);
            }
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('categoryFilter').value = '';
            document.getElementById('priceMin').value = '';
            document.getElementById('priceMax').value = '';
            document.getElementById('sortBy').value = 'newest';
            document.getElementById('perPage').value = '6';
            currentView = 'grid';
            setViewMode('grid', false);
            applyFilters();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', Utils.debounce(() => {
                    applyFilters();
                }, 300));
            }
        });

    </script>
</body>
</html>
