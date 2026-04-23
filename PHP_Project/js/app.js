/**
 * Modern Cafeteria - Main JavaScript Module
 * Handles toast notifications, dark mode, and interactive features
 */

class ToastNotification {
    constructor() {
        this.container = this.ensureContainer();
    }

    ensureContainer() {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        return container;
    }

    show(message, type = 'success', title = null, duration = 3000) {
        const toastEl = document.createElement('div');
        toastEl.className = `toast toast-${type}`;
        
        let icon = '';
        switch(type) {
            case 'success':
                icon = '<i class="fas fa-check-circle"></i>';
                break;
            case 'error':
            case 'danger':
                icon = '<i class="fas fa-exclamation-circle"></i>';
                break;
            case 'warning':
                icon = '<i class="fas fa-exclamation-triangle"></i>';
                break;
            case 'info':
                icon = '<i class="fas fa-info-circle"></i>';
                break;
        }

        const titleHtml = title ? `<div class="toast-title">${title}</div>` : '';
        
        toastEl.innerHTML = `
            <div class="toast-icon">${icon}</div>
            <div class="toast-content">
                ${titleHtml}
                <div class="toast-message">${message}</div>
            </div>
            <span class="toast-close">&times;</span>
        `;

        const closeBtn = toastEl.querySelector('.toast-close');
        closeBtn.addEventListener('click', () => toastEl.remove());

        this.container.appendChild(toastEl);

        if (duration > 0) {
            setTimeout(() => {
                if (toastEl.parentNode) {
                    toastEl.remove();
                }
            }, duration);
        }

        return toastEl;
    }

    success(message, title = 'Success') {
        return this.show(message, 'success', title);
    }

    error(message, title = 'Error') {
        return this.show(message, 'error', title);
    }

    warning(message, title = 'Warning') {
        return this.show(message, 'warning', title);
    }

    info(message, title = 'Info') {
        return this.show(message, 'info', title);
    }
}

// Create global toast instance
window.toast = new ToastNotification();

/**
 * Dark Mode Manager
 */
class DarkModeManager {
    constructor() {
        this.storageKey = 'cafeteria-dark-mode';
        this.init();
    }

    init() {
        const isDarkMode = this.getSavedMode();
        if (isDarkMode === null) {
            // Check system preference
            this.setDarkMode(window.matchMedia('(prefers-color-scheme: dark)').matches);
        } else {
            this.setDarkMode(isDarkMode);
        }
    }

    toggle() {
        this.setDarkMode(!this.isDarkMode());
        return this.isDarkMode();
    }

    setDarkMode(enabled) {
        if (enabled) {
            document.body.classList.add('dark-mode');
            localStorage.setItem(this.storageKey, 'true');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem(this.storageKey, 'false');
        }
    }

    isDarkMode() {
        return document.body.classList.contains('dark-mode');
    }

    getSavedMode() {
        const saved = localStorage.getItem(this.storageKey);
        return saved === null ? null : saved === 'true';
    }
}

window.darkMode = new DarkModeManager();

// Dark Mode Toggle Function
function toggleDarkMode() {
    darkMode.toggle();
    const isDark = darkMode.isDarkMode();
    const button = document.getElementById('darkModeToggle');
    if (button) {
        button.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    }
}

/**
 * Loading Spinner
 */
class LoadingSpinner {
    static show(message = 'Loading...') {
        let overlay = document.getElementById('loading-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'loading-overlay';
            overlay.className = 'loading-overlay';
            overlay.innerHTML = `
                <div class="loading-spinner-center">
                    <div class="spinner-border"></div>
                    <div class="loading-text">${message}</div>
                </div>
            `;
            document.body.appendChild(overlay);
        }
        overlay.style.display = 'flex';
    }

    static hide() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }
}

window.LoadingSpinner = LoadingSpinner;

/**
 * Skeleton Loader for Products
 */
class SkeletonLoader {
    static createProductSkeleton() {
        const skeleton = document.createElement('div');
        skeleton.className = 'skeleton skeleton-product';
        skeleton.innerHTML = `
            <div class="skeleton skeleton-text title" style="width: 90%;"></div>
            <div class="skeleton skeleton-text" style="width: 100%;"></div>
            <div class="skeleton skeleton-text small" style="width: 60%;"></div>
        `;
        return skeleton;
    }

    static showProductSkeletons(container, count = 6) {
        container.innerHTML = '';
        for (let i = 0; i < count; i++) {
            container.appendChild(this.createProductSkeleton());
        }
    }

    static clearSkeletons(container) {
        container.innerHTML = '';
    }
}

window.SkeletonLoader = SkeletonLoader;

/**
 * Utility Functions
 */
window.Utils = {
    /**
     * Format currency in EGP
     */
    formatCurrency(amount) {
        return `EGP ${parseFloat(amount).toFixed(2)}`;
    },

    /**
     * Get full image URL for a product image filename.
     * Handles: null/empty → null, absolute paths → as-is, relative → /uploads/products/{file}
     */
    getProductImageUrl(image) {
        if (!image) return null;
        return String(image).startsWith('/') ? image : `/uploads/products/${image}`;
    },

    /**
     * Format date
     */
    formatDate(date) {
        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }).format(new Date(date));
    },

    /**
     * Debounce function
     */
    debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    },

    /**
     * Throttle function
     */
    throttle(func, delay) {
        let lastCall = 0;
        return function(...args) {
            const now = Date.now();
            if (now - lastCall >= delay) {
                lastCall = now;
                func.apply(this, args);
            }
        };
    },

    /**
     * Check if element is in viewport
     */
    isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    },

    /**
     * Scroll to element
     */
    scrollToElement(element, offset = 80) {
        const top = element.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top, behavior: 'smooth' });
    },

    /**
     * Make API request
     */
    async apiRequest(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        };

        const config = { ...defaultOptions, ...options };

        try {
            const response = await fetch(url, config);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            return {
                success: true,
                data: data
            };
        } catch (error) {
            console.error('API Error:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }
};

/**
 * Get current authenticated user (cached)
 */
window.getCurrentUser = async function() {
    if (window.__currentUserCache !== undefined) {
        return window.__currentUserCache;
    }

    const res = await Utils.apiRequest('/api/me');
    if (res.success && res.data && res.data.success) {
        window.__currentUserCache = res.data.data;
        return window.__currentUserCache;
    }

    window.__currentUserCache = null;
    return null;
};

/**
 * Initialize on DOM Ready
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart count
    if (CartManager) {
        CartManager.updateCartCount();
    }
    
    // Initialize any tooltips if Bootstrap is present
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Add smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                Utils.scrollToElement(target);
            }
        });
    });
});

/**
 * Handle form submissions with AJAX
 */
window.submitFormAjax = async function(formElement, options = {}) {
    options = {
        showLoading: true,
        showSuccess: true,
        successMessage: 'Operation completed successfully!',
        errorMessage: 'An error occurred. Please try again.',
        redirect: null,
        ...options
    };

    try {
        if (options.showLoading) {
            LoadingSpinner.show('Submitting...');
        }

        const formData = new FormData(formElement);
        const response = await fetch(formElement.action || window.location.href, {
            method: formElement.method || 'POST',
            body: formData
        });

        const data = await response.json();

        if (options.showLoading) {
            LoadingSpinner.hide();
        }

        if (data.success) {
            if (options.showSuccess) {
                toast.success(options.successMessage);
            }

            if (options.redirect) {
                setTimeout(() => {
                    window.location.href = options.redirect;
                }, 1000);
            }

            if (options.callback) {
                options.callback(data);
            }

            return data;
        } else {
            toast.error(data.message || options.errorMessage);
            return null;
        }
    } catch (error) {
        console.error('Form submission error:', error);
        if (options.showLoading) {
            LoadingSpinner.hide();
        }
        toast.error(options.errorMessage);
        return null;
    }
};

/**
 * Add product to cart (WIP - will be connected to backend)
 */
/**
 * Cart Management System
 */
window.CartManager = {
    STORAGE_KEY: 'cafeteria_cart',
    
    // Get all items in cart
    getCart: function() {
        const cart = localStorage.getItem(this.STORAGE_KEY);
        return cart ? JSON.parse(cart) : {};
    },
    
    // Add item to cart
    addItem: function(productId, product, quantity = 1) {
        const cart = this.getCart();
        
        if (cart[productId]) {
            cart[productId].quantity += quantity;
        } else {
            cart[productId] = {
                id: productId,
                name: product.name,
                price: Number(product.price),
                description: product.description,
                quantity: quantity
            };
        }
        
        localStorage.setItem(this.STORAGE_KEY, JSON.stringify(cart));
        this.updateCartCount();
        return cart[productId];
    },
    
    // Remove item from cart
    removeItem: function(productId) {
        const cart = this.getCart();
        delete cart[productId];
        localStorage.setItem(this.STORAGE_KEY, JSON.stringify(cart));
        this.updateCartCount();
    },
    
    // Update item quantity
    updateQuantity: function(productId, quantity) {
        const cart = this.getCart();
        if (cart[productId]) {
            if (quantity <= 0) {
                this.removeItem(productId);
            } else {
                cart[productId].quantity = quantity;
                localStorage.setItem(this.STORAGE_KEY, JSON.stringify(cart));
                this.updateCartCount();
            }
        }
    },
    
    // Get cart count
    getCartCount: function() {
        const cart = this.getCart();
        return Object.keys(cart).length;
    },
    
    // Get total items (sum of quantities)
    getTotalQuantity: function() {
        const cart = this.getCart();
        return Object.values(cart).reduce((sum, item) => sum + item.quantity, 0);
    },
    
    // Clear entire cart
    clearCart: function() {
        localStorage.removeItem(this.STORAGE_KEY);
        this.updateCartCount();
    },
    
    // Calculate totals
    calculateTotals: function() {
        const cart = this.getCart();
        const subtotal = Object.values(cart).reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const tax = subtotal * 0.14;
        return {
            subtotal: Number(subtotal.toFixed(2)),
            tax: Number(tax.toFixed(2)),
            total: Number((subtotal + tax).toFixed(2))
        };
    },
    
    // Update cart count in navbar
    updateCartCount: function() {
        const count = this.getCartCount();
        const cartIcon = document.getElementById('cartCount');
        if (cartIcon) {
            if (count > 0) {
                cartIcon.textContent = count;
                cartIcon.style.display = 'inline';
            } else {
                cartIcon.style.display = 'none';
            }
        }
    }
};

window.addToCart = async function(productId, quantity = 1) {
    try {
        const me = await window.getCurrentUser();

        // Not logged in
        if (!me) {
            toast.info('Please login first to place an order', 'Authentication');
            setTimeout(() => {
                window.location.href = '/login';
            }, 800);
            return false;
        }

        // Admin: navigate to product management/edit page
        if (me.role === 'admin') {
            toast.info('Admins can edit products from the manage products page', 'Admin Action');
            setTimeout(() => {
                window.location.href = '/admin/products';
            }, 500);
            return true;
        }

        // Load product details
        const productRes = await Utils.apiRequest(`/api/products/${productId}`);
        if (!(productRes.success && productRes.data && productRes.data.success)) {
            toast.error('Failed to load product details');
            return false;
        }

        const product = productRes.data.data;
        
        // Add to local cart
        const addedItem = CartManager.addItem(productId, product, quantity);
        
        // Show success toast
        toast.success(`${product.name} added to cart!`, 'Added');
        
        return true;
    } catch (error) {
        toast.error('An error occurred: ' + error.message);
        return false;
    }
};

/**
 * Toggle wishlist item
 */
window.toggleWishlist = async function(productId, button) {
    try {
        const response = await Utils.apiRequest(`/api/wishlist/${productId}`, {
            method: 'POST'
        });

        if (response.success && button) {
            button.classList.toggle('active');
            const isActive = button.classList.contains('active');
            toast.success(isActive ? 'Added to wishlist' : 'Removed from wishlist');
        }

        return response;
    } catch (error) {
        toast.error('Failed to update wishlist');
        return null;
    }
};

/**
 * Lazy load images
 */
window.lazyLoadImages = function() {
    if ('IntersectionObserver' in window) {
        const images = document.querySelectorAll('img[data-src]');
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for browsers that don't support IntersectionObserver
        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => {
            img.src = img.dataset.src;
        });
    }
};

// Initialize lazy loading when DOM is ready
document.addEventListener('DOMContentLoaded', lazyLoadImages);
