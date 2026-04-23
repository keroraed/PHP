<?php 
require_once __DIR__ . '/../config/dp.php';

class Product extends Service {
    
    public function getAll() {
        $result = $this->getAllProducts();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllProducts() {
        return $this->connection->query("SELECT * FROM products");
    }

    public function getByCategory($categoryId) {
        $stmt = $this->connection->prepare("SELECT * FROM products WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertProduct($name, $price, $category_id, $description = null, $image = null) {
        $stmt = $this->connection->prepare(
            "INSERT INTO products (name, price, category_id, description, image, quantity) VALUES (?,?,?,?,?,?)"
        );
        return $stmt->execute([$name, $price, $category_id, $description, $image, 0]);
    }

    public function getProductById($id) {
        $stmt = $this->connection->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProduct($id, $name, $price, $category_id, $description = null, $image = null) {
        $stmt = $this->connection->prepare(
            "UPDATE products SET name=?, price=?, category_id=?, description=?, image=? WHERE id=?"
        );
        return $stmt->execute([$name, $price, $category_id, $description, $image, $id]);
    }

    public function deleteProduct($id) {
        $stmt = $this->connection->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    
    public function search($keyword) {
        $keyword = '%' . $keyword . '%';
        $stmt = $this->connection->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
        $stmt->execute([$keyword, $keyword]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 
?>