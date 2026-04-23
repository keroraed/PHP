<?php
$pageTitle = 'Contact Us';
include __DIR__ . '/layouts/head.php';
?>
    <!-- Navigation -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <h1>📞 Contact Us</h1>
            <p>We'd love to hear from you. Get in touch with us today!</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-4">
            <!-- Contact Information -->
            <div class="col-lg-4">
                <!-- Phone -->
                <div class="card-modern mb-4">
                    <div class="card-body-modern text-center">
                        <i class="fas fa-phone" style="font-size: 3rem; color: var(--primary-dark); margin-bottom: 1rem; display: block;"></i>
                        <h5 style="color: var(--primary-dark); margin-bottom: 0.5rem;">Phone</h5>
                        <p style="color: var(--text-dark); margin: 0;">
                            <a href="tel:+1234567890" style="color: var(--primary-dark); text-decoration: none;">
                                +1 (234) 567-890
                            </a>
                        </p>
                        <small style="color: var(--text-muted);">Mon - Fri: 8:00 AM - 5:00 PM</small>
                    </div>
                </div>

                <!-- Email -->
                <div class="card-modern mb-4">
                    <div class="card-body-modern text-center">
                        <i class="fas fa-envelope" style="font-size: 3rem; color: var(--primary-dark); margin-bottom: 1rem; display: block;"></i>
                        <h5 style="color: var(--primary-dark); margin-bottom: 0.5rem;">Email</h5>
                        <p style="color: var(--text-dark); margin: 0;">
                            <a href="mailto:info@cafeteria.com" style="color: var(--primary-dark); text-decoration: none;">
                                info@cafeteria.com
                            </a>
                        </p>
                        <small style="color: var(--text-muted);">We'll reply within 24 hours</small>
                    </div>
                </div>

                <!-- Location -->
                <div class="card-modern">
                    <div class="card-body-modern text-center">
                        <i class="fas fa-map-marker-alt" style="font-size: 3rem; color: var(--primary-dark); margin-bottom: 1rem; display: block;"></i>
                        <h5 style="color: var(--primary-dark); margin-bottom: 0.5rem;">Location</h5>
                        <p style="color: var(--text-dark); margin: 0;">
                            123 Coffee Street<br>
                            Brew City, BC 12345<br>
                            United States
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="card-modern">
                    <div class="card-header-modern">
                        <h5><i class="fas fa-envelope-open"></i> Send us a Message</h5>
                    </div>
                    <div class="card-body-modern">
                        <form action="/contact/send" method="POST">
                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label-modern">
                                    <i class="fas fa-user"></i> Full Name
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control-modern" 
                                    id="name" 
                                    name="name" 
                                    placeholder="Your name"
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
                                    placeholder="your@email.com"
                                    required>
                            </div>

                            <!-- Phone -->
                            <div class="mb-3">
                                <label for="phone" class="form-label-modern">
                                    <i class="fas fa-phone"></i> Phone Number
                                </label>
                                <input 
                                    type="tel" 
                                    class="form-control-modern" 
                                    id="phone" 
                                    name="phone" 
                                    placeholder="(123) 456-7890">
                            </div>

                            <!-- Subject -->
                            <div class="mb-3">
                                <label for="subject" class="form-label-modern">
                                    <i class="fas fa-lightbulb"></i> Subject
                                </label>
                                <select class="form-control-modern" id="subject" name="subject" required>
                                    <option value="">Select a subject</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="orders">Order Question</option>
                                    <option value="feedback">Feedback</option>
                                    <option value="partnership">Partnership</option>
                                    <option value="complaint">Complaint</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <!-- Message -->
                            <div class="mb-4">
                                <label for="message" class="form-label-modern">
                                    <i class="fas fa-comment-dots"></i> Message
                                </label>
                                <textarea 
                                    class="form-control-modern" 
                                    id="message" 
                                    name="message" 
                                    rows="6"
                                    placeholder="Please tell us what's on your mind..."
                                    required></textarea>
                            </div>

                            <!-- Subscribe Checkbox -->
                            <div class="mb-4 form-check">
                                <input 
                                    type="checkbox" 
                                    class="form-check-input" 
                                    id="subscribe" 
                                    name="subscribe" 
                                    style="border-color: var(--primary-color); width: 1.25em; height: 1.25em;">
                                <label class="form-check-label" for="subscribe" style="color: var(--text-dark); margin-left: 0.5rem;">
                                    Subscribe to our newsletter for updates and special offers
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary-modern">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                        </form>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="card-modern mt-4">
                    <div class="card-header-modern">
                        <h5><i class="fas fa-question-circle"></i> Frequently Asked Questions</h5>
                    </div>
                    <div class="card-body-modern">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item" style="border-color: var(--primary-color);">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" style="color: var(--primary-dark);">
                                        What are your business hours?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body" style="background: var(--bg-light); color: var(--text-dark);">
                                        We're open Monday to Friday from 8:00 AM to 5:00 PM, and Saturday from 10:00 AM to 3:00 PM.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item" style="border-color: var(--primary-color);">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" style="color: var(--primary-dark);">
                                        Do you offer delivery?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body" style="background: var(--bg-light); color: var(--text-dark);">
                                        Yes! We offer delivery for orders within our service area. Orders are typically delivered within 30 minutes.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item" style="border-color: var(--primary-color);">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" style="color: var(--primary-dark);">
                                        Can I modify my order after placing it?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body" style="background: var(--bg-light); color: var(--text-dark);">
                                        If you contact us within 5 minutes of placing your order, we can make changes. After that, orders cannot be modified.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <?php include __DIR__ . '/layouts/scripts.php'; ?>
</body>
</html>
