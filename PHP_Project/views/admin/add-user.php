<?php
$pageTitle = 'Add User - Admin';
include __DIR__ . '/../layouts/head.php';
?>
    <!-- Navigation -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <h1>👤 Add New User</h1>
            <p>Create a new user account in the system</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card-modern">
                    <div class="card-header-modern">
                        <h5>📋 User Information</h5>
                    </div>
                    <div class="card-body-modern">
                        <form id="addUserForm" enctype="multipart/form-data">
                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label-modern">
                                    <i class="fas fa-user"></i> Full Name
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control-modern" 
                                    id="name" 
                                    name="name" 
                                    placeholder="e.g., John Doe"
                                    required>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label-modern">
                                    <i class="fas fa-envelope"></i> Email Address
                                </label>
                                <input 
                                    type="email" 
                                    class="form-control-modern" 
                                    id="email" 
                                    name="email" 
                                    placeholder="example@cafeteria.com"
                                    required>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label-modern">
                                    <i class="fas fa-lock"></i> Password
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control-modern" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Enter a strong password"
                                    required>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label-modern">
                                    <i class="fas fa-lock"></i> Confirm Password
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control-modern" 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    placeholder="Re-enter password"
                                    required>
                            </div>

                            <!-- Role -->
                            <div class="mb-3">
                                <label for="role" class="form-label-modern">
                                    <i class="fas fa-crown"></i> User Role
                                </label>
                                <select class="form-control-modern" id="role" name="role" required>
                                    <option value="user">User</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>

                            <!-- Room Information -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="room_no" class="form-label-modern">
                                        <i class="fas fa-door-open"></i> Room No.
                                    </label>
                                    <input 
                                        type="text" 
                                        class="form-control-modern" 
                                        id="room_no" 
                                        name="room_no" 
                                        placeholder="e.g., 101">
                                </div>
                                <div class="col-md-6">
                                    <label for="ext" class="form-label-modern">
                                        <i class="fas fa-phone"></i> Extension
                                    </label>
                                    <input 
                                        type="text" 
                                        class="form-control-modern" 
                                        id="ext" 
                                        name="ext" 
                                        placeholder="e.g., 1234">
                                </div>
                            </div>

                            <!-- Building -->
                            <div class="mb-4">
                                <label for="building" class="form-label-modern">
                                    <i class="fas fa-building"></i> Building
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control-modern" 
                                    id="building" 
                                    name="building" 
                                    placeholder="e.g., Main Building">
                            </div>

                            <!-- Profile Picture -->
                            <div class="mb-4">
                                <label for="avatar" class="form-label-modern">
                                    <i class="fas fa-image"></i> Profile Picture
                                </label>
                                <input
                                    type="file"
                                    class="form-control-modern"
                                    id="avatar"
                                    name="avatar"
                                    accept="image/*">
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary-modern">
                                    <i class="fas fa-user-plus"></i> Create User
                                </button>
                                <a href="/admin/users" class="btn btn-secondary-modern">
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
        document.getElementById('addUserForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validate password match
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                window.toast.warning('Passwords do not match!', 'Validation Error');
                return;
            }
            
            const formData = new FormData(this);
            window.LoadingSpinner.show('Creating user...');
            
            try {
                const response = await fetch('/admin/users', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                window.LoadingSpinner.hide();
                
                if (data.success) {
                    window.toast.success('User created successfully!', 'Success');
                    setTimeout(() => {
                        window.location.href = '/admin/users';
                    }, 1500);
                } else {
                    window.toast.error(data.message || 'Failed to create user', 'Error');
                }
            } catch (error) {
                window.LoadingSpinner.hide();
                console.error('Error:', error);
                window.toast.error('Failed to create user: ' + error.message, 'Error');
            }
        });
    </script>
</body>
</html>
