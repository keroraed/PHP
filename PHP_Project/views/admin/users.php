<?php
$pageTitle = 'Manage Users - Admin';
include __DIR__ . '/../layouts/head.php';
?>
    <!-- Navigation -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <h1>👥 Manage Users</h1>
            <p>View and manage all registered users</p>
        </div>
    </div>

    <div class="container-fluid mb-5">
        <div class="row g-4">
            <!-- Add User Form -->
            <div class="col-lg-5">
                <div class="card-modern">
                    <div class="card-header-modern">
                        <h5>➕ Add New User</h5>
                    </div>
                    <div class="card-body-modern">
                        <form id="addUserForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label-modern">👤 Full Name</label>
                                <input type="text" class="form-control-modern" id="name" name="name" placeholder="e.g., John Doe" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label-modern">📧 Email Address</label>
                                <input type="email" class="form-control-modern" id="email" name="email" placeholder="user@example.com" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label-modern">🔒 Password</label>
                                <input type="password" class="form-control-modern" id="password" name="password" placeholder="••••••••" required>
                            </div>

                            <div class="mb-3">
                                <label for="room_no" class="form-label-modern">🚪 Room Number</label>
                                <input type="text" class="form-control-modern" id="room_no" name="room_no" placeholder="e.g., 101">
                            </div>

                            <div class="mb-3">
                                <label for="ext" class="form-label-modern">☎️ Extension</label>
                                <input type="text" class="form-control-modern" id="ext" name="ext" placeholder="e.g., 5001">
                            </div>

                            <div class="mb-3">
                                <label for="building" class="form-label-modern">🏢 Building</label>
                                <input type="text" class="form-control-modern" id="building" name="building" placeholder="e.g., Block A">
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label-modern">👔 Role</label>
                                <select class="form-select-modern" id="role" name="role" required>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="avatar" class="form-label-modern">🖼️ Profile Photo</label>
                                <input type="file" class="form-control-modern" id="avatar" name="avatar" accept="image/*">
                            </div>

                            <button type="submit" class="btn-primary-modern w-100">
                                <i class="fas fa-plus"></i> Add User
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="col-lg-7">
                <div class="card-modern">
                    <div class="card-header-modern">
                        <h5>👥 Users List</h5>
                    </div>
                    <div class="card-body-modern p-0">
                        <div class="table-responsive">
                            <table class="table-modern" id="users-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Room</th>
                                        <th>Ext</th>
                                        <th>Building</th>
                                        <th>Role</th>
                                        <th>Created</th>
                                        <th>Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Users will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <?php include __DIR__ . '/../layouts/scripts.php'; ?>
    <script>
        let editingId = null;
        let usersCache = [];

        async function loadUsers() {
            try {
                const response = await fetch('/api/users');
                const data = await response.json();

                if (data.success) {
                    displayUsers(data.data || []);
                } else {
                    window.toast.error(data.message || 'Failed to load users', 'Error');
                }
            } catch (error) {
                window.toast.error('Failed to load users: ' + error.message, 'Error');
            }
        }

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
        }

        function getUserPhotoUrl(user) {
            const file = user.profile_picture || user.photo || '';
            if (!file) return null;
            if (String(file).startsWith('/')) return file;
            return `/uploads/users/${file}`;
        }

        function formatDate(value) {
            if (!value) return 'N/A';
            const d = new Date(value.replace(' ', 'T'));
            if (Number.isNaN(d.getTime())) return escapeHtml(value);
            return d.toLocaleString();
        }

        function displayUsers(users) {
            usersCache = users;
            const tbody = document.querySelector('#users-table tbody');
            tbody.innerHTML = '';

            users.forEach(user => {
                const photoUrl = getUserPhotoUrl(user);
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><strong>#${user.id}</strong></td>
                    <td>
                        ${photoUrl
                            ? `<img src="${photoUrl}" alt="${escapeHtml(user.name)}" style="width:40px;height:40px;object-fit:cover;border-radius:50%;border:1px solid var(--border-color);">`
                            : `<span class="text-muted">N/A</span>`}
                    </td>
                    <td>${escapeHtml(user.name)}</td>
                    <td>${escapeHtml(user.email)}</td>
                    <td>${escapeHtml(user.room_no || 'N/A')}</td>
                    <td>${escapeHtml(user.ext || 'N/A')}</td>
                    <td>${escapeHtml(user.building || 'N/A')}</td>
                    <td><span style="background: rgba(212, 165, 116, 0.2); padding: 4px 12px; border-radius: 20px; font-size: 0.85rem;">${escapeHtml(user.role || 'user')}</span></td>
                    <td>${formatDate(user.created_at)}</td>
                    <td>${formatDate(user.updated_at)}</td>
                    <td>
                        <button class="btn-icon btn-icon-edit" onclick="editUserById(${user.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-icon btn-icon-delete" onclick="deleteUser(${user.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function editUserById(id) {
            const user = usersCache.find(u => Number(u.id) === Number(id));
            if (!user) {
                window.toast.error('User not found', 'Error');
                return;
            }
            editUser(user);
        }

        function editUser(user) {
            editingId = user.id;
            document.getElementById('name').value = user.name || '';
            document.getElementById('email').value = user.email || '';
            document.getElementById('room_no').value = user.room_no || '';
            document.getElementById('ext').value = user.ext || '';
            document.getElementById('building').value = user.building || '';
            document.getElementById('role').value = user.role || 'user';
            document.getElementById('password').value = '';
            document.querySelector('button[type="submit"]').innerHTML = '<i class="fas fa-save"></i> Update User';
            document.querySelector('.card-header-modern h5').textContent = '✏️ Edit User #' + user.id;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function resetForm() {
            editingId = null;
            document.getElementById('addUserForm').reset();
            document.querySelector('button[type="submit"]').innerHTML = '<i class="fas fa-user-plus"></i> Add User';
            document.querySelector('.card-header-modern h5').textContent = '➕ Add New User';
        }

        async function deleteUser(id) {
            if (!confirm('Are you sure you want to delete this user?')) return;

            try {
                const response = await fetch(`/api/users/${id}`, { method: 'DELETE' });
                const data = await response.json();

                if (data.success) {
                    window.toast.success('User deleted successfully', 'Success');
                    loadUsers();
                } else {
                    window.toast.error(data.message || 'Failed to delete user', 'Error');
                }
            } catch (error) {
                window.toast.error('Failed to delete user: ' + error.message, 'Error');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('addUserForm');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const payload = {
                    name: document.getElementById('name').value,
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value,
                    room_no: document.getElementById('room_no').value,
                    ext: document.getElementById('ext').value,
                    building: document.getElementById('building').value,
                    role: document.getElementById('role').value
                };

                try {
                    window.LoadingSpinner.show(editingId ? 'Updating user...' : 'Creating user...');

                    const response = editingId
                        ? await fetch(`/api/users/${editingId}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(payload)
                        })
                        : await fetch('/admin/users', {
                            method: 'POST',
                            body: new FormData(form)
                        });

                    const data = await response.json();
                    window.LoadingSpinner.hide();

                    if (data.success) {
                        window.toast.success(editingId ? 'User updated successfully' : 'User created successfully', 'Success');
                        resetForm();
                        loadUsers();
                    } else {
                        window.toast.error(data.message || 'Request failed', 'Error');
                    }
                } catch (error) {
                    window.LoadingSpinner.hide();
                    window.toast.error('Request failed: ' + error.message, 'Error');
                }
            });

            loadUsers();
        });
    </script>
</body>
</html>
