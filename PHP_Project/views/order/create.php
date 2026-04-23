<?php
$pageTitle = 'Create Order';
$bodyClass = 'order-create-page';
include __DIR__ . '/../layouts/head.php';
?>
    <!-- Navigation -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <h1>📦 Create New Order</h1>
            <p>Select products and create your order</p>
        </div>
    </div>

    <div class="container-fluid mb-5">
        <div class="row g-4">
            <!-- Order Form -->
            <div class="col-lg-5">
                <div class="card-modern">
                    <div class="card-header-modern">
                        <h5>✏️ Order Details</h5>
                    </div>
                    <div class="card-body-modern">
                        <form id="orderForm">
                            <!-- Selected Items Summary -->
                            <div class="mb-4">
                                <label class="form-label-modern">📋 Selected Items</label>
                                <div id="selectedItems" style="max-height: 200px; overflow-y: auto; border: 1px solid rgba(255,255,255,0.1); padding: 12px; border-radius: 8px; background: rgba(0,0,0,0.2);">
                                    <p class="text-muted text-center mb-0">No items selected yet</p>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="mb-3">
                                <label for="notes" class="form-label-modern">📝 Special Notes</label>
                                <textarea class="form-control-modern" id="notes" name="notes" rows="3" placeholder="e.g., Extra sugar, No ice..."></textarea>
                            </div>

                            <!-- Room Selection (if user is admin) -->
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <div class="mb-3">
                                <label for="userId" class="form-label-modern">👤 Deliver To</label>
                                <select class="form-select-modern" id="userId" name="userId" required>
                                    <option value="">Select User</option>
                                </select>
                            </div>
                            <?php endif; ?>

                            <!-- Total Section -->
                            <div style="background: linear-gradient(135deg, rgba(212,165,116,0.1) 0%, rgba(139,111,71,0.1) 100%); border: 1px solid var(--primary-color); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                                <div class="mb-2 d-flex justify-content-between">
                                    <span class="text-muted">Subtotal:</span>
                                    <span class="fw-bold" id="subtotalAmount">EGP 0</span>
                                </div>
                                <div class="mb-3 d-flex justify-content-between">
                                    <span class="text-muted">Tax (14%):</span>
                                    <span class="fw-bold" id="taxAmount">EGP 0</span>
                                </div>
                                <hr style="border-color: rgba(255,255,255,0.1); margin: 10px 0;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-5 fw-bold">Total Amount:</span>
                                    <span class="fs-4 fw-bold" style="color: var(--primary-color);" id="totalAmount">EGP 0</span>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn-primary-modern">
                                    <i class="fas fa-check-circle"></i> Confirm Order
                                </button>
                                <a href="/orders" class="btn-secondary-modern">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Products Selection -->
            <div class="col-lg-7">
                <div class="card-modern">
                    <div class="card-header-modern">
                        <h5>🍽️ Available Products</h5>
                    </div>
                    <div class="card-body-modern">
                        <input type="text" id="searchProducts" class="form-control-modern mb-4" placeholder="🔍 Search products...">
                        <div id="productsGrid" class="row g-3">
                            <!-- Products will load here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmAddModal" tabindex="-1" aria-labelledby="confirmAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 1px solid #e8e0d5;">
                <div class="modal-header" style="background: #f9f6f1; border-bottom: 1px solid #e8e0d5;">
                    <h5 class="modal-title" id="confirmAddModalLabel">
                        <i class="fas fa-question-circle" style="color: var(--primary-accent);"></i> Confirm Item Addition
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Do you want to add <strong id="confirmItemName"></strong> to your order?</p>
                    <div style="background: #f9f6f1; padding: 15px; border-radius: 8px; margin-top: 15px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Item Price:</span>
                            <strong>EGP <span id="confirmItemPrice"></span></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Quantity:</span>
                            <strong><span id="confirmItemQty"></span>x</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e8e0d5;">
                    <button type="button" class="btn btn-secondary-modern" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary-modern" onclick="confirmAddItem()">
                        <i class="fas fa-check me-2"></i> Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <?php include __DIR__ . '/../layouts/scripts.php'; ?>
    <script>
        let products = [];
        let selectedItems = {};
        const TAX_RATE = 0.14; // 14% tax

        async function loadProducts() {
            try {
                const response = await fetch('/api/products');
                const data = await response.json();
                
                if (data.success) {
                    products = data.data;
                    displayProducts(products);
                }
            } catch (error) {
                console.error('Error loading products:', error);
            }
        }

        async function loadUsers() {
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            try {
                const response = await fetch('/api/users');
                const data = await response.json();
                
                if (data.success) {
                    const userSelect = document.getElementById('userId');
                    const currentUser = <?php echo $_SESSION['user_id'] ?? 'null'; ?>;
                    
                    data.data.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = `${user.name} (Room ${user.room_no})`;
                        userSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading users:', error);
            }
            <?php endif; ?>
        }

        // Pending item for confirmation
        let pendingItem = null;

        function displayProducts(productsToShow) {
            const grid = document.getElementById('productsGrid');
            grid.innerHTML = '';

            if (productsToShow.length === 0) {
                grid.innerHTML = '<div class="col-12"><p class="text-muted text-center">No products found</p></div>';
                return;
            }

            productsToShow.forEach(product => {
                const card = document.createElement('div');
                card.className = 'col-md-6';
                card.innerHTML = `
                    <div class="product-card">
                        <div class="product-card-header">
                            <h6>${product.name}</h6>
                            <span class="product-price">EGP ${parseFloat(product.price).toFixed(2)}</span>
                        </div>
                        <p class="product-description">${product.description || 'Premium quality item'}</p>
                        <div class="product-controls">
                            <button class="btn-qty-control" onclick="decreaseQuantity(${product.id})">−</button>
                            <input type="number" class="qty-input" value="0" id="qty-${product.id}" min="0" onchange="updateItem(${product.id}, ${parseFloat(product.price)})">
                            <button class="btn-qty-control" onclick="increaseQuantity(${product.id})">+</button>
                        </div>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        function increaseQuantity(productId) {
            const input = document.getElementById(`qty-${productId}`);
            const product = products.find(p => p.id === productId);
            const newQuantity = (parseInt(input.value) || 0) + 1;
            
            // Store pending item and show confirmation modal
            pendingItem = {
                productId: productId,
                newQuantity: newQuantity,
                price: parseFloat(product.price),
                name: product.name,
                inputElement: input
            };
            
            // Update modal content
            document.getElementById('confirmItemName').textContent = product.name;
            document.getElementById('confirmItemPrice').textContent = parseFloat(product.price).toFixed(2);
            document.getElementById('confirmItemQty').textContent = newQuantity;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('confirmAddModal'));
            modal.show();
        }

        function decreaseQuantity(productId) {
            const input = document.getElementById(`qty-${productId}`);
            const product = products.find(p => p.id === productId);
            const current = parseInt(input.value) || 0;
            if (current > 0) {
                input.value = current - 1;
                if (input.value === '0') {
                    // Show confirmation for removal
                    pendingItem = {
                        productId: productId,
                        newQuantity: 0,
                        price: parseFloat(product.price),
                        name: product.name,
                        inputElement: input,
                        isRemoval: true
                    };
                    
                    // Update modal for removal confirmation
                    document.getElementById('confirmItemName').textContent = product.name;
                    document.getElementById('confirmItemPrice').textContent = parseFloat(product.price).toFixed(2);
                    document.getElementById('confirmItemQty').textContent = '0';
                    
                    // Change modal title for removal
                    const modalTitle = document.getElementById('confirmAddModalLabel');
                    modalTitle.innerHTML = '<i class="fas fa-trash-alt" style="color: #dc3545;"></i> Confirm Item Removal';
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('confirmAddModal'));
                    modal.show();
                } else {
                    updateItem(productId, parseFloat(product.price));
                }
            }
        }

        function confirmAddItem() {
            if (pendingItem) {
                const itemName = pendingItem.name;
                const isRemoval = pendingItem.isRemoval || false;
                
                if (isRemoval) {
                    // For removal, set quantity to 0
                    delete selectedItems[pendingItem.productId];
                } else {
                    // For add, update the input and item
                    const input = document.getElementById(`qty-${pendingItem.productId}`);
                    if (input) {
                        input.value = pendingItem.newQuantity;
                    }
                    updateItem(pendingItem.productId, pendingItem.price);
                }
                
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmAddModal'));
                modal.hide();
                
                // Reset modal title and button for next use
                const modalTitle = document.getElementById('confirmAddModalLabel');
                modalTitle.innerHTML = '<i class="fas fa-question-circle" style="color: var(--primary-accent);"></i> Confirm Item Addition';
                
                const confirmBtn = document.querySelector('#confirmAddModal .modal-footer .btn-primary-modern');
                confirmBtn.innerHTML = '<i class="fas fa-check me-2"></i> Confirm';
                confirmBtn.style.background = '';
                
                // Update selected items display
                updateSelectedItems();
                
                // Show appropriate toast
                if (isRemoval) {
                    window.toast.success(`${itemName} removed from order!`, 'Removed');
                } else {
                    window.toast.success(`${itemName} added to order!`, 'Added');
                }
                
                // Reset pending item
                pendingItem = null;
            }
        }

        function removeItemFromCart(productId) {
            const product = products.find(p => p.id === productId);
            const item = selectedItems[productId];
            
            if (!item) return;
            
            // Show confirmation for removal
            pendingItem = {
                productId: productId,
                newQuantity: 0,
                price: item.price,
                name: item.name,
                isRemoval: true
            };
            
            // Update modal for removal confirmation
            document.getElementById('confirmItemName').textContent = item.name;
            document.getElementById('confirmItemPrice').textContent = item.price.toFixed(2);
            document.getElementById('confirmItemQty').textContent = '0';
            
            // Change modal title for removal
            const modalTitle = document.getElementById('confirmAddModalLabel');
            modalTitle.innerHTML = '<i class="fas fa-trash-alt" style="color: #dc3545;"></i> Confirm Item Removal';
            
            // Change button text and color for removal
            const confirmBtn = document.querySelector('#confirmAddModal .modal-footer .btn-primary-modern');
            confirmBtn.innerHTML = '<i class="fas fa-trash-alt me-2"></i> Remove';
            confirmBtn.style.background = '#dc3545';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('confirmAddModal'));
            modal.show();
        }

        function updateItem(productId, price) {
            const quantity = parseInt(document.getElementById(`qty-${productId}`).value) || 0;
            
            if (quantity > 0) {
                selectedItems[productId] = {
                    id: productId,
                    quantity: quantity,
                    price: price,
                    name: products.find(p => p.id === productId).name
                };
            } else {
                delete selectedItems[productId];
            }
            
            updateSelectedItems();
        }

        function updateSelectedItems() {
            const container = document.getElementById('selectedItems');
            const items = Object.values(selectedItems);

            if (items.length === 0) {
                container.innerHTML = '<p class="text-muted text-center mb-0">No items selected yet</p>';
                updateTotal();
                return;
            }

            let html = '<ul class="list-unstyled mb-0">';
            items.forEach(item => {
                const subtotal = item.quantity * item.price;
                html += `
                    <li class="order-item-summary" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <div style="flex: 1;">
                            <strong>${item.name}</strong><br>
                            <small class="text-muted">${item.quantity} × EGP ${item.price.toFixed(2)}</small>
                        </div>
                        <span class="fw-bold" style="color: var(--primary-color); margin-right: 10px;">EGP ${subtotal.toFixed(2)}</span>
                        <button onclick="removeItemFromCart(${item.id})" class="btn btn-sm" style="background: rgba(220,53,69,0.2); color: #dc3545; border: none; padding: 5px 10px; border-radius: 6px; cursor: pointer;">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </li>
                `;
            });
            html += '</ul>';
            container.innerHTML = html;
            updateTotal();
        }

        function updateTotal() {
            let subtotal = 0;
            Object.values(selectedItems).forEach(item => {
                subtotal += item.quantity * item.price;
            });

            const tax = subtotal * TAX_RATE;
            const total = subtotal + tax;

            document.getElementById('subtotalAmount').textContent = `EGP ${subtotal.toFixed(2)}`;
            document.getElementById('taxAmount').textContent = `EGP ${tax.toFixed(2)}`;
            document.getElementById('totalAmount').textContent = `EGP ${total.toFixed(2)}`;
        }

        document.getElementById('orderForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const items = Object.values(selectedItems);
            if (items.length === 0) {
                window.toast.warning('Please select at least one product', 'Validation');
                return;
            }

            const subtotal = items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
            const total = subtotal + (subtotal * TAX_RATE);

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            const selectedUserId = document.getElementById('userId')?.value;
            if (!selectedUserId) {
                window.toast.warning('Please select a user to assign this order', 'Validation');
                return;
            }
            <?php endif; ?>

            const payload = {
                userId: <?php echo isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? '(document.getElementById("userId")?.value || null)' : ($_SESSION['user_id'] ?? 'null'); ?>,
                notes: document.getElementById('notes')?.value || '',
                subtotal: Number(subtotal.toFixed(2)),
                tax: Number((subtotal * TAX_RATE).toFixed(2)),
                total: Number(total.toFixed(2)),
                items: items.map(item => ({
                    id: item.id,
                    quantity: item.quantity,
                    price: Number(item.price)
                }))
            };

            window.LoadingSpinner.show('Creating order...');

            try {
                const response = await fetch('/api/orders/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();
                window.LoadingSpinner.hide();

                if (result.success) {
                    window.toast.success('Order created successfully!', 'Success');
                    setTimeout(() => {
                        window.location.href = '/orders';
                    }, 1500);
                } else {
                    window.toast.error(result.message || 'Failed to create order', 'Error');
                }
            } catch (error) {
                window.LoadingSpinner.hide();
                console.error('Error:', error);
                window.toast.error('Failed to create order: ' + error.message, 'Error');
            }
        });

        // Search functionality
        document.getElementById('searchProducts').addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            const filtered = products.filter(p => 
                p.name.toLowerCase().includes(query) || 
                (p.description && p.description.toLowerCase().includes(query))
            );
            displayProducts(filtered);
        });

        // Load data on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadProducts();
            loadUsers();
        });
    </script>
</body>
</html>
