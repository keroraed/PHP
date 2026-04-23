# Premium Cafeteria Management System

PHP-based cafeteria app with a custom MVC-style structure, session auth, role-based access, and JSON APIs.

## Tech Stack
- PHP 8+
- MySQL
- Bootstrap 5
- Vanilla JavaScript

## Current Project Structure

```text
PHP_Project/
├── index.php
├── router.php
├── .htrouter.php
├── .htaccess
├── setup_database.sql
├── config/
│   └── dp.php
├── core/
│   ├── Router.php
│   ├── controller.php
│   └── model.php
├── routes/
│   ├── public.php
│   ├── api.php
│   ├── admin.php
│   └── user.php
├── controllers/
│   ├── productController.php
│   ├── orderController.php
│   ├── adminUserController.php
│   ├── userController.php
│   ├── authController.php
│   └── chechController.php
├── models/
│   ├── users.php
│   ├── products.php
│   ├── order.php
│   ├── orderItem.php
│   └── category.php
├── views/
│   ├── layouts/
│   │   ├── head.php
│   │   └── scripts.php
│   ├── components/
│   │   ├── navbar.php
│   │   ├── footer.php
│   │   └── toast.php
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── user/
│   │   ├── home.php
│   │   └── my_order.php
│   ├── admin/
│   │   ├── products.php
│   │   ├── add-product.php
│   │   ├── users.php
│   │   ├── add-user.php
│   │   └── orders.php
│   ├── product/
│   │   └── detail.php
│   ├── order/
│   │   └── create.php
│   ├── products.php
│   ├── orders.php
│   ├── about.php
│   └── contact.php
├── js/
│   └── app.js
├── css/
│   ├── modern.css
│   └── custom.css
├── assets/
│   ├── no-image.svg
│   └── no-profile.svg
└── uploads/
    ├── products/
    └── users/
```

## Application Flow
1. `index.php` boots the app, starts session, loads core files, and imports route modules.
2. `core/Router.php` matches request path/method and executes route callbacks.
3. Route files decide whether to render a view or return JSON.
4. Controllers call models for business logic and database operations.
5. Models use PDO connection from `config/dp.php`.

## Roles
- `user`: browse products, place orders, view own orders.
- `admin`: manage products, users, orders, and contact messages.

## Setup

1. Create DB and seed data:
- Import `setup_database.sql` into MySQL.

2. Configure database credentials in `config/dp.php`:

```php
private $host = "localhost";
private $dbname = "PHP_Project";
private $user = "root";
private $password = "password";
```

3. Run local server from project root:

```bash
php -S 127.0.0.1:8000 router.php
```

4. Open:
- `http://127.0.0.1:8000`

## Seeded Accounts
From `setup_database.sql` comments:
- Admin: `admin@example.com` / `admin123`
- User: `john@example.com` / `user123`

## Main Routes

### Public pages
- `GET /`
- `GET /login`, `POST /login`
- `GET /register` (info page: contact admin for account creation)
- `GET /logout`
- `GET /products`
- `GET /product/{id}`
- `GET /about`
- `GET /contact`, `POST /contact/send`

### User pages
- `GET /order/create`
- `GET /orders`
- `GET /my-orders`
- `POST /orders/store`

### Admin pages
- `GET /admin/products`
- `GET /admin/products/add`
- `GET /admin/users`
- `GET /admin/users/add`
- `GET /admin/orders`
- `GET /admin/messages`

### API
- Products: `/api/products`, `/api/products/{id}`
- Categories: `/api/categories`
- Users: `/api/users`, `/api/users/{id}`, `/api/me`, `/api/users/search/{q}`
- Orders: `/api/orders`, `/api/orders/{id}`, `/api/orders/create`, `/api/orders/all`, `/api/orders/{id}/status`, `/api/orders/{id}/cancel`
- Contact messages: `/api/admin/messages`, `/api/admin/messages/{id}/read`
- Cart/Wishlist placeholders: `/api/cart`, `/api/cart/add`, `/api/wishlist`, `/api/wishlist/{id}`

## Notes
- Frontend shared logic is in `js/app.js` (toast, dark mode, loader, cart, API helpers).
- Main styling is `css/modern.css`; `css/custom.css` is legacy.
- `controllers/chechController.php` filename contains a typo but is kept as-is for compatibility.
