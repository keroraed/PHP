<?php
$pageTitle = 'Manage Products - Admin';
include __DIR__ . '/../layouts/head.php';
?>
    <!-- Navigation Component -->
    <?php include(__DIR__ . '/../components/navbar.php'); ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <h1><i class="fas fa-boxes me-2"></i>Manage Products</h1>
            <p>Add, edit, and remove products from the cafeteria menu</p>
        </div>
    </div>

    <div class="container-fluid mb-5">
        <div class="row g-4">
            <!-- Add / Edit Product Form -->
            <div class="col-lg-5">
                <div class="card-modern">
                    <div class="card-header-modern">
                        <h5 id="formCardTitle"><i class="fas fa-plus me-1"></i> Add New Product</h5>
                    </div>
                    <div class="card-body-modern">
                        <form action="/admin/products" method="POST" enctype="multipart/form-data" id="productForm">
                            <div class="mb-3">
                                <label for="name" class="form-label-modern">Product Name</label>
                                <input type="text" class="form-control-modern" id="name" name="name"
                                       placeholder="e.g., Espresso" required>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label-modern">Price (EGP)</label>
                                <input type="number" class="form-control-modern" id="price" name="price"
                                       step="0.01" placeholder="50.00" required>
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label-modern">Category</label>
                                <select class="form-select-modern" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <!-- Populated dynamically via loadCategories() -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label-modern">Description</label>
                                <textarea class="form-control-modern" id="description" name="description"
                                          rows="3" placeholder="Enter product description..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label-modern">Product Image</label>
                                <input type="file" class="form-control-modern" id="image" name="image" accept="image/*">
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" id="submitBtn" class="btn btn-primary-modern flex-grow-1">
                                    <i class="fas fa-plus me-1"></i> Add Product
                                </button>
                                <button type="button" id="cancelEditBtn" class="btn btn-secondary-modern d-none" onclick="resetForm()">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="col-lg-7">
                <div class="card-modern">
                    <div class="card-header-modern">
                        <h5><i class="fas fa-list me-1"></i> Products List</h5>
                    </div>
                    <div class="p-0">
                        <div class="table-responsive">
                            <table class="table table-modern mb-0" id="products-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Category</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Products loaded via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-trash text-danger me-2"></i>Delete Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteProductName"></strong>?</p>
                    <p class="text-muted small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-modern" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include(__DIR__ . '/../components/footer.php'); ?>

    <?php include __DIR__ . '/../layouts/scripts.php'; ?>
    <script>
        let categoryMap = {};
        let editingId = null;
        let pendingDeleteId = null;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));

        async function loadCategories() {
            try {
                const response = await Utils.apiRequest('/api/categories');
                if (response.success && response.data && response.data.data) {
                    const categories = response.data.data;
                    const select = document.getElementById('category_id');

                    categories.forEach(cat => {
                        categoryMap[cat.id] = cat.name;

                        if (!select.querySelector(`option[value="${cat.id}"]`)) {
                            const opt = document.createElement('option');
                            opt.value = cat.id;
                            opt.textContent = cat.name;
                            select.appendChild(opt);
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        async function loadProducts() {
            try {
                const response = await fetch('/api/products');
                const data = await response.json();
                if (data.success) displayProducts(data.data);
            } catch (error) {
                console.error('Error loading products:', error);
                window.toast?.error('Failed to load products', 'Error');
            }
        }

        function displayProducts(products) {
            const tbody = document.querySelector('#products-table tbody');
            tbody.innerHTML = '';

            if (!products.length) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No products yet.</td></tr>';
                return;
            }

            products.forEach(product => {
                const categoryName = categoryMap[product.category_id] || `Cat #${product.category_id}`;
                const imgUrl = Utils.getProductImageUrl(product.image);
                const imageHtml = imgUrl
                    ? `<img src="${imgUrl}" alt="${product.name}" class="product-image-thumb">`
                    : `<div class="product-image-placeholder"><i class="fas fa-image"></i></div>`;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><strong>#${product.id}</strong></td>
                    <td class="product-image-cell">${imageHtml}</td>
                    <td>${product.name}</td>
                    <td><strong>EGP ${parseFloat(product.price).toFixed(2)}</strong></td>
                    <td><span class="category-pill">${categoryName}</span></td>
                    <td>
                        <button class="btn btn-icon btn-icon-edit me-1"
                                onclick="editProduct(${product.id}, '${product.name}', ${product.price}, ${product.category_id}, '${(product.description || '').replace(/'/g, "\\'")}')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-icon btn-icon-delete"
                                onclick="confirmDelete(${product.id}, '${product.name}')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function editProduct(id, name, price, categoryId, description) {
            editingId = id;
            document.getElementById('name').value = name;
            document.getElementById('price').value = price;
            document.getElementById('category_id').value = categoryId;
            document.getElementById('description').value = description;
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-1"></i> Update Product';
            document.getElementById('formCardTitle').innerHTML = '<i class="fas fa-pen me-1"></i> Edit Product #' + id;
            document.getElementById('cancelEditBtn').classList.remove('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function resetForm() {
            editingId = null;
            document.getElementById('productForm').reset();
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-plus me-1"></i> Add Product';
            document.getElementById('formCardTitle').innerHTML = '<i class="fas fa-plus me-1"></i> Add New Product';
            document.getElementById('cancelEditBtn').classList.add('d-none');
        }

        function confirmDelete(id, name) {
            pendingDeleteId = id;
            document.getElementById('deleteProductName').textContent = name;
            deleteModal.show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
            if (!pendingDeleteId) return;
            deleteModal.hide();

            try {
                window.LoadingSpinner?.show('Deleting...');
                const response = await fetch(`/admin/products/${pendingDeleteId}`, { method: 'DELETE' });
                const data = await response.json();
                window.LoadingSpinner?.hide();

                if (data.success) {
                    window.toast?.success('Product deleted successfully', 'Deleted');
                    loadProducts();
                } else {
                    window.toast?.error(data.message || 'Failed to delete product', 'Error');
                }
            } catch (error) {
                window.LoadingSpinner?.hide();
                console.error('Error deleting product:', error);
                window.toast?.error('Error: ' + error.message, 'Error');
            }
            pendingDeleteId = null;
        });

        document.addEventListener('DOMContentLoaded', async function() {
            const form = document.getElementById('productForm');

            await loadCategories();
            loadProducts();

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                const url = editingId ? `/admin/products/${editingId}` : '/admin/products';

                try {
                    window.LoadingSpinner?.show(editingId ? 'Updating...' : 'Adding...');
                    const response = await fetch(url, { method: 'POST', body: formData });
                    const data = await response.json();
                    window.LoadingSpinner?.hide();

                    if (data.success) {
                        window.toast?.success(editingId ? 'Product updated!' : 'Product added!', 'Success');
                        resetForm();
                        loadProducts();
                    } else {
                        window.toast?.error(data.message || 'Operation failed', 'Error');
                    }
                } catch (error) {
                    window.LoadingSpinner?.hide();
                    window.toast?.error('Error: ' + error.message, 'Error');
                }
            });
        });
    </script>
</body>
</html>
