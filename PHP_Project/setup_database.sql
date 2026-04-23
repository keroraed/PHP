-- Create database if not exists
CREATE DATABASE IF NOT EXISTS PHP_Project;
USE PHP_Project;

-- Drop tables in correct order (reverse of creation)
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;


-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    room_no VARCHAR(50),
    ext VARCHAR(50),
    building VARCHAR(50),
    profile_picture VARCHAR(255),
    role VARCHAR(50) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT,
    image VARCHAR(255),
    quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Orders table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    subtotal DECIMAL(10, 2) DEFAULT 0,
    tax DECIMAL(10, 2) DEFAULT 0,
    total DECIMAL(10, 2) DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Coffee', 'Various coffee beverages'),
('Tea', 'Tea and herbal drinks'),
('Snacks', 'Pastries and light snacks');

-- Insert sample products
INSERT INTO products (name, description, price, category_id, quantity) VALUES
('Espresso', 'Strong Italian espresso', 2.50, 1, 100),
('Cappuccino', 'Creamy cappuccino with foam', 3.50, 1, 100),
('Latte', 'Smooth coffee with steamed milk', 3.75, 1, 100),
('Green Tea', 'Fresh green tea', 2.00, 2, 100),
('Black Tea', 'Premium black tea', 2.25, 2, 100),
('Croissant', 'Butter croissant', 2.75, 3, 50),
('Muffin', 'Chocolate muffin', 2.50, 3, 50),
('Sandwich', 'Chicken sandwich', 5.00, 3, 40);

-- Insert sample admin user (password: admin123)
INSERT INTO users (name, email, password, room_no, ext, building, role) VALUES
('Admin User', 'admin@example.com', '$2y$10$slYQmyNdGzin7olVN3YO2OPST9/PgBkqquzi.Ss7KIUgO2t0jKMm2', '101', '5000', 'A', 'admin');

-- Insert sample regular user (password: user123)
INSERT INTO users (name, email, password, room_no, ext, building, role) VALUES
('John Doe', 'john@example.com', '$2y$10$slYQmyNdGzin7olVN3YO2OPST9/PgBkqquzi.Ss7KIUgO2t0jKMm2', '201', '5001', 'B', 'user');
