<?php
$pageTitle = 'About Us';
include __DIR__ . '/layouts/head.php';
?>
    <!-- Navigation -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <h1>☕ About Premium Cafeteria</h1>
            <p>Discover our story and mission</p>
        </div>
    </div>

    <div class="container py-5">
        <!-- About Section -->
        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="card-modern">
                    <div class="card-body-modern">
                        <h3 class="mb-4" style="color: var(--primary-dark);">
                            <i class="fas fa-info-circle"></i> Who We Are
                        </h3>
                        <p style="color: var(--text-dark); font-size: 1.1rem; line-height: 1.8;">
                            Welcome to Premium Cafeteria, your go-to destination for high-quality beverages and culinary delights. 
                            Founded with a passion for excellence, we have been serving our community with premium coffee, fresh pastries, 
                            and delicious food items for over a decade.
                        </p>
                        <p style="color: var(--text-dark); font-size: 1.1rem; line-height: 1.8;">
                            Our mission is simple: to provide exceptional quality products and outstanding customer service in a 
                            welcoming environment. We believe that every cup of coffee and every meal should be an experience to remember.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats Sidebar -->
            <div class="col-lg-4">
                <div class="card-modern">
                    <div class="card-body-modern">
                        <h5 class="mb-4" style="color: var(--primary-dark); text-align: center;">
                            <i class="fas fa-award"></i> Our Achievements
                        </h5>
                        
                        <div class="mb-4 p-3" style="background: var(--bg-light); border-radius: 8px; border-left: 4px solid var(--primary-color);">
                            <div style="font-size: 2rem; font-weight: bold; color: var(--primary-dark);">
                                10+
                            </div>
                            <div style="color: var(--text-muted);">Years in Business</div>
                        </div>

                        <div class="mb-4 p-3" style="background: var(--bg-light); border-radius: 8px; border-left: 4px solid var(--primary-color);">
                            <div style="font-size: 2rem; font-weight: bold; color: var(--primary-dark);">
                                5000+
                            </div>
                            <div style="color: var(--text-muted);">Happy Customers</div>
                        </div>

                        <div class="mb-4 p-3" style="background: var(--bg-light); border-radius: 8px; border-left: 4px solid var(--primary-color);">
                            <div style="font-size: 2rem; font-weight: bold; color: var(--primary-dark);">
                                50+
                            </div>
                            <div style="color: var(--text-muted);">Menu Items</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Values Section -->
        <div class="row g-4 mb-5">
            <h3 class="mb-4" style="color: var(--primary-dark);">
                <i class="fas fa-heart"></i> Our Core Values
            </h3>

            <div class="col-md-4">
                <div class="card-modern">
                    <div class="card-body-modern text-center">
                        <i class="fas fa-gem" style="font-size: 3rem; color: var(--primary-dark); margin-bottom: 1rem;"></i>
                        <h5 style="color: var(--primary-dark);">Quality First</h5>
                        <p style="color: var(--text-dark);">
                            We use only the finest ingredients and maintain the highest standards in every product we offer.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-modern">
                    <div class="card-body-modern text-center">
                        <i class="fas fa-handshake" style="font-size: 3rem; color: var(--primary-dark); margin-bottom: 1rem;"></i>
                        <h5 style="color: var(--primary-dark);">Customer Service</h5>
                        <p style="color: var(--text-dark);">
                            Your satisfaction is our priority. We're committed to providing exceptional service every time.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-modern">
                    <div class="card-body-modern text-center">
                        <i class="fas fa-leaf" style="font-size: 3rem; color: var(--primary-dark); margin-bottom: 1rem;"></i>
                        <h5 style="color: var(--primary-dark);">Sustainability</h5>
                        <p style="color: var(--text-dark);">
                            We're committed to eco-friendly practices and reducing our environmental impact.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="card-modern mb-5">
            <div class="card-header-modern">
                <h5><i class="fas fa-users"></i> Meet Our Team</h5>
            </div>
            <div class="card-body-modern">
                <p style="color: var(--text-dark); margin-bottom: 2rem;">
                    Our dedicated team of coffee experts, baristas, and kitchen staff work tirelessly to ensure that 
                    every customer receives the best experience possible. With years of combined experience in the food 
                    and beverage industry, we bring passion and expertise to everything we do.
                </p>
                <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                        <div style="text-align: center; padding: 2rem; background: var(--bg-light); border-radius: 8px;">
                            <i class="fas fa-user-circle" style="font-size: 3rem; color: var(--primary-dark); margin-bottom: 1rem;"></i>
                            <h6 style="color: var(--primary-dark);">John Smith</h6>
                            <p style="color: var(--text-muted); margin: 0;">Head Barista</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div style="text-align: center; padding: 2rem; background: var(--bg-light); border-radius: 8px;">
                            <i class="fas fa-user-circle" style="font-size: 3rem; color: var(--primary-dark); margin-bottom: 1rem;"></i>
                            <h6 style="color: var(--primary-dark);">Sarah Johnson</h6>
                            <p style="color: var(--text-muted); margin: 0;">Kitchen Manager</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div style="text-align: center; padding: 2rem; background: var(--bg-light); border-radius: 8px;">
                            <i class="fas fa-user-circle" style="font-size: 3rem; color: var(--primary-dark); margin-bottom: 1rem;"></i>
                            <h6 style="color: var(--primary-dark);">Mike Davis</h6>
                            <p style="color: var(--text-muted); margin: 0;">Pastry Chef</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div style="text-align: center; padding: 2rem; background: var(--bg-light); border-radius: 8px;">
                            <i class="fas fa-user-circle" style="font-size: 3rem; color: var(--primary-dark); margin-bottom: 1rem;"></i>
                            <h6 style="color: var(--primary-dark);">Emma Wilson</h6>
                            <p style="color: var(--text-muted); margin: 0;">Customer Service</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="card-modern" style="background: linear-gradient(135deg, var(--bg-white), var(--bg-light)); border: 2px solid var(--primary-color);">
            <div class="card-body-modern text-center py-5">
                <h4 style="color: var(--primary-dark); margin-bottom: 1.5rem;">
                    <i class="fas fa-coffee"></i> Ready to Experience Excellence?
                </h4>
                <p style="color: var(--text-dark); margin-bottom: 2rem; font-size: 1.1rem;">
                    Visit our cafeteria or order online to enjoy our premium products and exceptional service.
                </p>
                <a href="/products" class="btn btn-primary-modern">
                    <i class="fas fa-shopping-bag"></i> Browse Our Menu
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <?php include __DIR__ . '/layouts/scripts.php'; ?>
</body>
</html>
