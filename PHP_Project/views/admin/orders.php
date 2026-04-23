<?php
$pageTitle = 'Manage Orders - Admin';
include __DIR__ . '/../layouts/head.php';
?>
    <!-- Navigation Component -->
    <?php include(__DIR__ . '/../components/navbar.php'); ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1>📦 Manage Orders</h1>
            <p class="text-muted">Track and manage all customer orders</p>
        </div>
    </div>

    <div class="container-fluid py-5">
        <div class="mb-3">
            <a href="/order/create" class="btn btn-primary-modern">
                <i class="fas fa-plus me-2"></i>Create Order (Assign to User)
            </a>
        </div>

        <!-- Orders Summary -->
        <div id="summarySection" style="display: none;">
            <div class="orders-card mb-4" style="border-left: 4px solid var(--primary-accent);">
                <h5 class="fw-bold mb-4">
                    <i class="fas fa-chart-line"></i> Orders Summary
                </h5>
                <div class="row g-4">
                    <div class="col-md-4 summary-item text-center">
                        <div class="text-muted small mb-2">
                            <i class="fas fa-check-circle"></i> Total Done Orders
                        </div>
                        <div id="doneCount" class="fs-2 fw-bold" style="color: var(--success-color);">0</div>
                    </div>
                    <div class="col-md-4 summary-item text-center">
                        <div class="text-muted small mb-2">
                            <i class="fas fa-dollar-sign"></i> Total Revenue
                        </div>
                        <div id="doneTotal" class="fs-2 fw-bold" style="color: var(--primary-accent);">EGP 0.00</div>
                    </div>
                    <div class="col-md-4 summary-item text-center">
                        <div class="text-muted small mb-2">
                            <i class="fas fa-boxes"></i> Total Items (Done)
                        </div>
                        <div id="doneItems" class="fs-2 fw-bold" style="color: var(--info-color);">0</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-section">
            <h5 style="color: var(--text-dark); margin-bottom: 1.5rem;">
                <i class="fas fa-filter"></i> Filter Orders
            </h5>
            <div class="filter-row">
                <div>
                    <label class="filter-label">Date From</label>
                    <input type="date" id="dateFrom" class="form-control filter-input" onchange="filterOrders()">
                </div>
                <div>
                    <label class="filter-label">Date To</label>
                    <input type="date" id="dateTo" class="form-control filter-input" onchange="filterOrders()">
                </div>
                <div>
                    <label class="filter-label">Customer</label>
                    <select id="userFilter" class="form-control filter-input" onchange="filterOrders()">
                        <option value="">All Customers</option>
                    </select>
                </div>
                <div>
                    <label class="filter-label">Status</label>
                    <select id="statusFilter" class="form-control filter-input" onchange="filterOrders()">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="out-for-delivery">Out for Delivery</option>
                        <option value="done">Done</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="filter-row">
                <div>
                    <button class="btn btn-secondary" onclick="resetFilters()">
                        <i class="fas fa-sync-alt"></i> Reset Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="orders-card">
            <h5 class="fw-bold mb-4">
                <i class="fas fa-list"></i> Orders
            </h5>
            <div id="ordersContainer">
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>Loading orders...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Change Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="statusOrderId">
                    <div class="mb-3">
                        <label class="form-label form-label-modern">New Status</label>
                        <select id="newStatus" class="filter-input">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="out-for-delivery">Out for Delivery</option>
                            <option value="done">Done</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary-modern" onclick="updateOrderStatus()">
                        Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include(__DIR__ . '/../components/footer.php'); ?>

    <?php include __DIR__ . '/../layouts/scripts.php'; ?>
    <script>
        let allOrders = [];
        let userMap = {};
        let pendingAdminCancelOrderId = null;
        const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));

        async function loadOrders() {
            try {
                const response = await fetch('/api/orders/all');
                const data = await response.json();
                
                if (data.success) {
                    allOrders = data.data;
                    displayOrders(allOrders);
                }
            } catch (error) {
                console.error('Error loading orders:', error);
                document.getElementById('ordersContainer').innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Error loading orders</p>
                    </div>
                `;
            }
        }

        async function loadUsers() {
            try {
                const response = await fetch('/api/users');
                const data = await response.json();
                
                if (data.success) {
                    const userSelect = document.getElementById('userFilter');
                    data.data.forEach(user => {
                        userMap[user.id] = user.name;
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = user.name;
                        userSelect.appendChild(option);
                    });
                }
                return true;
            } catch (error) {
                console.error('Error loading users:', error);
                return false;
            }
        }

        function displayOrders(orders) {
            const container = document.getElementById('ordersContainer');
            
            if (orders.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No orders found</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = '';

            orders.forEach(order => {
                const orderDate = new Date(order.created_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const itemCount = order.items ? order.items.length : 0;
                const items = order.items || [];
                const orderTotal = parseFloat(order.total ?? order.total_price ?? 0);
                const itemsHtml = items.map(item => 
                    `<li><span>${item.name} <span class="item-quantity">× ${item.quantity}</span></span> <span class="item-price">EGP ${(item.quantity * item.price).toFixed(2)}</span></li>`
                ).join('');

                const orderElement = document.createElement('div');
                orderElement.className = 'order-row';
                orderElement.setAttribute('data-order-id', order.id);
                orderElement.innerHTML = `
                    <div class="order-header">
                        <div class="order-id">#${order.id}</div>
                        <span class="order-status status-${order.status}">
                            <i class="fas fa-circle"></i> ${order.status.charAt(0).toUpperCase() + order.status.slice(1).replace('-', ' ')}
                        </span>
                    </div>

                    <div class="order-details">
                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-user"></i> Customer</div>
                            <div class="detail-value">${userMap[order.user_id] || 'Unknown'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-calendar"></i> Date</div>
                            <div class="detail-value">${orderDate}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-box"></i> Items</div>
                            <div class="detail-value">${itemCount}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-dollar-sign"></i> Total</div>
                            <div class="detail-value">EGP ${orderTotal.toFixed(2)}</div>
                        </div>
                    </div>

                    <div class="order-items">
                        <strong class="d-block mb-2">
                            <i class="fas fa-shopping-bag"></i> Order Items
                        </strong>
                        <ul class="item-list">
                            ${itemsHtml}
                        </ul>
                    </div>

                    <div class="action-buttons">
                        <button class="btn-small btn-status" onclick="openStatusModal(${order.id}, '${order.status}')">
                            <i class="fas fa-edit"></i> Change Status
                        </button>
                        <button class="btn-small btn-cancel" onclick="cancelAdminOrder(${order.id})">
                            <i class="fas fa-times"></i> Cancel Order
                        </button>
                    </div>
                `;
                container.appendChild(orderElement);
            });

            updateSummary();
        }

        function openStatusModal(orderId, currentStatus) {
            document.getElementById('statusOrderId').value = orderId;
            document.getElementById('newStatus').value = currentStatus;
            statusModal.show();
        }

        async function updateOrderStatus() {
            const orderId = document.getElementById('statusOrderId').value;
            const newStatus = document.getElementById('newStatus').value;

            try {
                const response = await fetch(`/api/orders/${orderId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                const result = await response.json();

                if (result.success) {
                    window.toast?.success('Order status updated successfully', 'Success');
                    statusModal.hide();
                    loadOrders();
                } else {
                    window.toast?.error(result.message || 'Error updating status', 'Error');
                }
            } catch (error) {
                console.error('Error:', error);
                window.toast?.error('Failed to update status', 'Error');
            }
        }

        async function cancelAdminOrder(orderId) {
            // Show confirmation modal instead of basic confirm
            pendingAdminCancelOrderId = orderId;
            
            // Remove any existing modal
            const oldModal = document.getElementById('adminCancelConfirmModal');
            if (oldModal) {
                oldModal.remove();
            }
            
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.id = 'adminCancelConfirmModal';
            modal.tabindex = '-1';
            modal.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle text-warning"></i> Confirm Cancellation
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to cancel order <strong>#${orderId}</strong>?</p>
                            <p class="text-muted small mb-0">
                                This action will remove the order from the list.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It</button>
                            <button type="button" class="btn btn-danger" id="adminConfirmCancelBtn">
                                <i class="fas fa-times"></i> Yes, Cancel Order
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            
            // Show modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            
            // Wait a moment for modal to be rendered, then attach event listener
            setTimeout(() => {
                const confirmBtn = document.getElementById('adminConfirmCancelBtn');
                if (confirmBtn) {
                    confirmBtn.onclick = confirmAdminCancelOrder;
                }
            }, 100);
        }

        async function confirmAdminCancelOrder() {
            if (!pendingAdminCancelOrderId) {
                console.error('No order ID set');
                return;
            }
            
            const orderId = pendingAdminCancelOrderId;
            const orderElement = document.querySelector(`[data-order-id="${orderId}"]`);
            
            try {
                const response = await fetch(`/api/orders/${orderId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                const result = await response.json();

                if (result.success) {
                    // Remove from allOrders array
                    allOrders = allOrders.filter(order => order.id != orderId);
                    
                    // Animate removal
                    if (orderElement) {
                        orderElement.style.animation = 'fadeOutSlide 0.5s ease-out forwards';
                        setTimeout(() => {
                            orderElement.remove();
                            updateSummary();
                        }, 500);
                    }
                    
                    window.toast?.success('Order cancelled and removed', 'Success');
                    
                    // Close the modal
                    const modalElement = document.getElementById('adminCancelConfirmModal');
                    if (modalElement) {
                        const bsModal = bootstrap.Modal.getInstance(modalElement);
                        if (bsModal) {
                            bsModal.hide();
                        }
                        // Remove modal from DOM
                        setTimeout(() => {
                            const modal = document.getElementById('adminCancelConfirmModal');
                            if (modal && modal.parentNode) {
                                modal.remove();
                            }
                            pendingAdminCancelOrderId = null;
                        }, 300);
                    }
                } else {
                    window.toast?.error(result.message || 'Failed to cancel order', 'Error');
                }
            } catch (error) {
                console.error('Error:', error);
                window.toast?.error('Failed to cancel order', 'Error');
            }
        }

        function filterOrders() {
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            const userId = document.getElementById('userFilter').value;
            const status = document.getElementById('statusFilter').value;

            let filtered = allOrders;

            if (dateFrom) {
                filtered = filtered.filter(order => {
                    const orderDate = new Date(order.created_at).toLocaleDateString('en-CA');
                    return orderDate >= dateFrom;
                });
            }

            if (dateTo) {
                filtered = filtered.filter(order => {
                    const orderDate = new Date(order.created_at).toLocaleDateString('en-CA');
                    return orderDate <= dateTo;
                });
            }

            if (userId) {
                filtered = filtered.filter(order => order.user_id == userId);
            }

            if (status) {
                filtered = filtered.filter(order => order.status === status);
            }

            displayOrders(filtered);
        }

        function resetFilters() {
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            document.getElementById('userFilter').value = '';
            document.getElementById('statusFilter').value = '';
            displayOrders(allOrders);
        }

        function updateSummary() {
            // Calculate summary for done orders
            const doneOrders = allOrders.filter(order => order.status === 'done');
            
            if (doneOrders.length === 0) {
                document.getElementById('summarySection').style.display = 'none';
                return;
            }

            document.getElementById('summarySection').style.display = 'block';

            let totalRevenue = 0;
            let totalItems = 0;

            doneOrders.forEach(order => {
                totalRevenue += parseFloat(order.total ?? order.total_price ?? 0);
                totalItems += (order.items?.length ?? 0);
            });

            document.getElementById('doneCount').textContent = doneOrders.length;
            document.getElementById('doneTotal').textContent = `EGP ${totalRevenue.toFixed(2)}`;
            document.getElementById('doneItems').textContent = totalItems;
        }

        // Load data on page load
        document.addEventListener('DOMContentLoaded', async () => {
            await loadUsers();
            loadOrders();
        });
    </script>
</body>
</html>
