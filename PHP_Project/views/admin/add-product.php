<?php
$pageTitle = 'Add Product - Admin';
include __DIR__ . '/../layouts/head.php';
?>
    <!-- Navigation -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <h1>➕ Add New Product</h1>
            <p>Create a new menu item for the cafeteria</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card-modern">
                    <div class="card-header-modern">
                        <h5>📝 Product Information</h5>
                    </div>
                    <div class="card-body-modern">
                        <form id="addProductForm" enctype="multipart/form-data">
                            <!-- Product Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label-modern">
                                    <i class="fas fa-utensils"></i> Product Name
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control-modern" 
                                    id="name" 
                                    name="name" 
                                    placeholder="e.g., Espresso Coffee"
                                    required>
                            </div>

                            <!-- Category -->
                            <div class="mb-3">
                                <label for="category_id" class="form-label-modern">
                                    <i class="fas fa-tag"></i> Category
                                </label>
                                <select class="form-control-modern" id="category_id" name="category_id" required>
                                    <option value="">Select a category</option>
                                    <?php
                                    if (isset($_SESSION['user_id'])) {
                                        require_once 'models/category.php';
                                        $category = new Category();
                                        $categories = $category->getAll();
                                        foreach ($categories as $cat):
                                    ?>
                                        <option value="<?= htmlspecialchars($cat['id']) ?>">
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php
                                        endforeach;
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Price -->
                            <div class="mb-3">
                                <label for="price" class="form-label-modern">
                                    <i class="fas fa-dollar-sign"></i> Price
                                </label>
                                <input 
                                    type="number" 
                                    class="form-control-modern" 
                                    id="price" 
                                    name="price" 
                                    placeholder="0.00"
                                    step="0.01"
                                    min="0"
                                    required>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label-modern">
                                    <i class="fas fa-align-left"></i> Description
                                </label>
                                <textarea 
                                    class="form-control-modern" 
                                    id="description" 
                                    name="description" 
                                    rows="4"
                                    placeholder="Describe your product..."
                                    required></textarea>
                            </div>

                            <!-- Image Upload -->
                            <div class="mb-4">
                                <label for="image" class="form-label-modern">
                                    <i class="fas fa-image"></i> Product Image
                                </label>
                                <input 
                                    type="file" 
                                    class="form-control-modern" 
                                    id="image" 
                                    name="image"
                                    accept="image/*"
                                    required>
                                <small class="text-muted">Supported: JPG, PNG, GIF (Max 2MB)</small>
                            </div>

                            <!-- Status -->
                            <div class="mb-4">
                                <label for="status" class="form-label-modern">
                                    <i class="fas fa-toggle-on"></i> Status
                                </label>
                                <select class="form-control-modern" id="status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary-modern">
                                    <i class="fas fa-save"></i> Save Product
                                </button>
                                <a href="/admin/products" class="btn btn-secondary-modern">
                                    <i class="fas fa-arrow-left"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <?php include __DIR__ . '/../layouts/scripts.php'; ?>
    <script>
        document.getElementById('addProductForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            window.LoadingSpinner.show('Creating product...');
            
            try {
                const response = await fetch('/admin/products/store', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                window.LoadingSpinner.hide();
                
                if (data.success) {
                    window.toast.success('Product created successfully!', 'Success');
                    setTimeout(() => {
                        window.location.href = '/admin/products';
                    }, 1500);
                } else {
                    window.toast.error(data.message || 'Failed to create product', 'Error');
                }
            } catch (error) {
                window.LoadingSpinner.hide();
                console.error('Error:', error);
                window.toast.error('Failed to create product: ' + error.message, 'Error');
            }
        });
    </script>
</body>
</html>
