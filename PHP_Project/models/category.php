<?php
require_once __DIR__ . '/../config/dp.php';
class Category extends Service {

    
    public function getAll() {
        $result = $this->getAllCategories();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllCategories() {
        return $this->connection->query("SELECT * FROM categories");
    }

    public function insertCategory($name) {
        $stmt = $this->connection->prepare(
            "INSERT INTO categories (name) VALUES (?)"
        );
        return $stmt->execute([$name]);
    }

    public function getCategoryById($id) {
        $stmt = $this->connection->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateCategory($id, $name) {
        $stmt = $this->connection->prepare(
            "UPDATE categories SET name=? WHERE id=?"
        );
        return $stmt->execute([$name, $id]);
    }

    public function deleteCategory($id) {
        $stmt = $this->connection->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>