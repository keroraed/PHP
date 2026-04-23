<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
$pageTitle = 'My Orders';
include __DIR__ . '/layouts/head.php';
?>
    <!-- Navigation Component -->
    <?php include(__DIR__ . '/components/navbar.php'); ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <h1><i class="fas fa-receipt"></i> My Orders</h1>
            <p class="text-muted">View and track your orders</p>
        </div>
    </div>

    <div class="container-fluid py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Pending Cart Section -->
                <div id="pendingCartSection" style="display: none; margin-bottom: 2rem;">
                    <div class="card-modern" style="border-color: var(--primary-accent); background: #fafaf9;">
                        <div class="card-header-modern" style="background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-accent) 100%); color: white;">
                            <h5 style="color: white; margin: 0;">
                                <i class="fas fa-shopping-cart"></i> Your Shopping Cart
                            </h5>
                        </div>
                        <div class="card-body-modern">
                            <div id="pendingCartItems"></div>
                            
                            <div class="cart-totals-summary">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <strong id="cartSubtotal">EGP 0</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax (14%):</span>
                                    <strong id="cartTax">EGP 0</strong>
                                </div>
                                <hr class="my-2 border-0" style="height:1px;background:#e8e0d5;">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold fs-6">Total:</span>
                                    <strong class="fs-5" style="color: var(--primary-accent);" id="cartTotal">EGP 0</strong>
                                </div>
                            </div>
                            
            <div style="display: flex; gap: 1rem; margin-top: 15px; flex-wrap: wrap;">
                                <button onclick="placePendingOrder()" class="btn btn-primary-modern flex-grow-1" style="min-width: 150px;">
                                    <i class="fas fa-check me-2"></i> Place Order
                                </button>
                                <button onclick="clearPendingCart()" class="btn btn-secondary-modern flex-grow-1" style="min-width: 150px;">
                                    <i class="fas fa-trash me-2"></i> Clear Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="margin-bottom: 2rem;">
                    <a href="/products" class="btn btn-primary-modern">
                        <i class="fas fa-shopping-bag"></i> Continue Shopping
                    </a>
                </div>

                <!-- Orders List -->
                <div id="ordersContainer">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Loading your orders...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <?php include __DIR__ . '/layouts/scripts.php'; ?>
    <script>
        // Display pending cart
        function displayPendingCart() {
            if (typeof CartManager === 'undefined') {
                console.error('CartManager not loaded');
                return;
            }

            const cart = CartManager.getCart();
            const section = document.getElementById('pendingCartSection');
            
            if (Object.keys(cart).length === 0) {
                section.style.display = 'none';
                return;
            }

            section.style.display = 'block';
            const itemsContainer = document.getElementById('pendingCartItems');
            
            let html = '<ul class="item-list mb-0">';
            Object.values(cart).forEach(item => {
                const subtotal = item.quantity * item.price;
                html += `
                    <li>
                        <div class="flex-grow-1">
                            <div class="item-name">${item.name}</div>
                            <small class="item-details">${item.quantity} × EGP ${item.price.toFixed(2)}</small>
                        </div>
                        <div class="me-3">
                            <strong class="item-price">EGP ${subtotal.toFixed(2)}</strong>
                        </div>
                        <button onclick="removeFromCart(${item.id})" class="btn-remove-cart-item">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </li>
                `;
            });
            html += '</ul>';
            itemsContainer.innerHTML = html;

            // Update totals
            const totals = CartManager.calculateTotals();
            document.getElementById('cartSubtotal').textContent = 'EGP ' + totals.subtotal.toFixed(2);
            document.getElementById('cartTax').textContent = 'EGP ' + totals.tax.toFixed(2);
            document.getElementById('cartTotal').textContent = 'EGP ' + totals.total.toFixed(2);
        }

        function removeFromCart(productId) {
            CartManager.removeItem(productId);
            displayPendingCart();
            window.toast.success('Item removed from cart', 'Removed');
        }

        function clearPendingCart() {
            if (confirm('Are you sure you want to clear your entire cart?')) {
                CartManager.clearCart();
                displayPendingCart();
                window.toast.success('Cart cleared', 'Cleared');
            }
        }

        async function placePendingOrder() {
            if (typeof CartManager === 'undefined') {
                window.toast.error('Cart manager not available');
                return;
            }

            const cart = CartManager.getCart();
            if (Object.keys(cart).length === 0) {
                window.toast.warning('Your cart is empty', 'Empty Cart');
                return;
            }

            try {
                window.LoadingSpinner.show('Placing order...');
                
                const totals = CartManager.calculateTotals();
                const items = Object.values(cart).map(item => ({
                    id: item.id,
                    quantity: item.quantity,
                    price: item.price
                }));

                const response = await fetch('/api/orders/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        userId: null, // Current user will be set by the API
                        notes: 'Order placed from shopping cart',
                        subtotal: totals.subtotal,
                        tax: totals.tax,
                        total: totals.total,
                        items: items
                    })
                });

                const result = await response.json();
                window.LoadingSpinner.hide();

                if (result.success) {
                    window.toast.success('Order placed successfully!', 'Success');
                    CartManager.clearCart();
                    displayPendingCart();
                    loadOrders();
                } else {
                    window.toast.error(result.message || 'Failed to place order', 'Error');
                }
            } catch (error) {
                window.LoadingSpinner.hide();
                console.error('Error:', error);
                window.toast.error('Error placing order: ' + error.message, 'Error');
            }
        }

        async function loadOrders() {
            try {
                const response = await fetch('/api/orders');
                const data = await response.json();
                
                if (data.success && data.data) {
                    displayOrders(data.data);
                } else {
                    showEmptyState('No orders found');
                }
            } catch (error) {
                console.error('Error loading orders:', error);
                showEmptyState('Error loading orders');
            }
        }

        function displayOrders(orders) {
            if (orders.length === 0) {
                showEmptyState('You haven\'t placed any orders yet');
                return;
            }

            const container = document.getElementById('ordersContainer');
            container.innerHTML = '';

            orders.forEach(order => {
                const orderDate = new Date(order.created_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const items = order.items || [];
                const totalValue = parseFloat(order.total ?? order.total_price ?? 0);
                const canCancel = ['pending', 'processing', 'out-for-delivery'].includes(String(order.status || '').toLowerCase());
                const itemsHtml = items.map(item => `
                    <li>
                        <div>
                            <div class="item-name">${item.name}</div>
                            <div class="item-details">Qty: ${item.quantity}</div>
                        </div>
                        <div class="item-price">EGP ${(item.quantity * item.price).toFixed(2)}</div>
                    </li>
                `).join('');

                const orderElement = document.createElement('div');
                orderElement.className = 'order-card';
                orderElement.setAttribute('data-order-id', order.id);
                orderElement.innerHTML = `
                    <div class="order-header">
                        <div class="order-id">Order #${order.id}</div>
                        <span class="order-status status-${order.status}">
                            <i class="fas fa-circle"></i> ${order.status.charAt(0).toUpperCase() + order.status.slice(1).replace('-', ' ')}
                        </span>
                    </div>

                    <div class="order-details">
                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-calendar"></i> Date</div>
                            <div class="detail-value">${orderDate}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-box"></i> Items</div>
                            <div class="detail-value">${items.length}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-dollar-sign"></i> Subtotal</div>
                            <div class="detail-value">EGP ${parseFloat(order.subtotal || 0).toFixed(2)}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-receipt"></i> Total</div>
                            <div class="detail-value">EGP ${totalValue.toFixed(2)}</div>
                        </div>
                    </div>

                    <div class="order-items">
                        <strong class="d-block mb-2 text-dark">
                            <i class="fas fa-list"></i> Order Items
                        </strong>
                        <ul class="item-list">
                            ${itemsHtml}
                        </ul>
                    </div>
                    
                    <div class="order-actions">
                        ${canCancel ? `<button class="btn-cancel-order" onclick="cancelOrder(${order.id})"><i class="fas fa-times me-1"></i>Cancel Order</button>` : ''}
                    </div>
                `;
                container.appendChild(orderElement);
            });
        }
        
        async function cancelOrder(orderId) {
            // Show custom confirmation dialog
            const orderCard = document.querySelector(`[data-order-id="${orderId}"]`);
            if (!orderCard) return;
            
            // Create confirmation modal
            const confirmModal = document.createElement('div');
            confirmModal.className = 'modal fade';
            confirmModal.id = `confirmCancelModal-${orderId}`;
            confirmModal.tabIndex = '-1';
            confirmModal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border border-light">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title">
                                <i class="fas fa-trash-alt text-danger"></i> Cancel Order
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to cancel this order? This action cannot be undone.</p>
                            <p class="text-muted small mt-3">Order #${orderId} will be permanently removed from your orders list.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary-modern" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i> Keep Order
                            </button>
                            <button type="button" class="btn btn-danger" onclick="confirmCancelOrder(${orderId})">
                                <i class="fas fa-trash-alt me-2"></i> Yes, Cancel Order
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(confirmModal);
            const modal = new bootstrap.Modal(confirmModal);
            modal.show();
            
            // Clean up modal after it's hidden
            confirmModal.addEventListener('hidden.bs.modal', function() {
                confirmModal.remove();
            });
        }

        async function confirmCancelOrder(orderId) {
            try {
                // Show loading state on the order card
                const orderCard = document.querySelector(`[data-order-id="${orderId}"]`);
                if (orderCard) {
                    const originalContent = orderCard.innerHTML;
                    orderCard.style.opacity = '0.6';
                    orderCard.style.pointerEvents = 'none';
                }

                const response = await fetch(`/api/orders/${orderId}`, {
                    method: 'DELETE'
                });
                const result = await response.json();
                
                // Close the confirmation modal
                const confirmModal = document.querySelector(`#confirmCancelModal-${orderId}`);
                if (confirmModal) {
                    const modal = bootstrap.Modal.getInstance(confirmModal);
                    if (modal) modal.hide();
                }
                
                if (result.success) {
                    // Animate removal
                    if (orderCard) {
                        orderCard.style.transition = 'all 0.3s ease';
                        orderCard.style.opacity = '0';
                        orderCard.style.transform = 'translateX(100%)';
                        setTimeout(() => {
                            orderCard.remove();
                            // Check if there are any orders left
                            const container = document.getElementById('ordersContainer');
                            if (container && container.children.length === 0) {
                                showEmptyState('You haven\'t placed any orders yet');
                            }
                        }, 300);
                    }
                    
                    window.toast?.success('Order cancelled and removed successfully', 'Cancelled');
                } else {
                    // Restore state if error
                    if (orderCard) {
                        orderCard.style.opacity = '1';
                        orderCard.style.pointerEvents = 'auto';
                    }
                    window.toast?.error(result.message || 'Failed to cancel order', 'Error');
                }
            } catch (error) {
                // Restore state if error
                const orderCard = document.querySelector(`[data-order-id="${orderId}"]`);
                if (orderCard) {
                    orderCard.style.opacity = '1';
                    orderCard.style.pointerEvents = 'auto';
                }
                window.toast?.error('Failed to cancel order: ' + error.message, 'Error');
            }
        }

        function showEmptyState(message) {
            const container = document.getElementById('ordersContainer');
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>${message}</p>
                    <a href="/order/create" class="btn btn-primary-modern">
                        <i class="fas fa-plus"></i> Create Your First Order
                    </a>
                </div>
            `;
        }

        // Load orders on page load
        document.addEventListener('DOMContentLoaded', function() {
            displayPendingCart();
            loadOrders();
        });
    </script>
</body>
</html>
