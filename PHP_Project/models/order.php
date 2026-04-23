<?php 
require_once __DIR__ . '/../config/dp.php';

class Order extends Service {
    
    public function getAllOrders() {
        return $this->connection->query("SELECT * FROM orders ORDER BY created_at DESC");
    }

    
    public function getAll() {
        $stmt = $this->connection->prepare(
            "SELECT o.*, u.name AS user_name, u.email AS user_email
             FROM orders o
             LEFT JOIN users u ON o.user_id = u.id
             ORDER BY o.created_at DESC"
        );
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as &$order) {
            $order['items'] = $this->getOrderItems($order['id']);
        }

        return $orders;
    }

    public function getOrdersByUser($userId) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC"
        );
        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as &$order) {
            $order['items'] = $this->getOrderItems($order['id']);
        }

        return $orders;
    }

    public function getOrderById($id) {
        $stmt = $this->connection->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createOrder($userId, $subtotal = 0, $tax = 0, $total = 0, $notes = '') {
        $stmt = $this->connection->prepare(
            "INSERT INTO orders (user_id, subtotal, tax, total, notes, status, created_at) 
             VALUES (?, ?, ?, ?, ?, 'pending', NOW())"
        );
        return $stmt->execute([$userId, $subtotal, $tax, $total, $notes]);
    }

    public function updateOrder($id, $data) {
        $allowed = ['user_id', 'status', 'subtotal', 'tax', 'total', 'notes'];
        $columns = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $columns[] = "{$key} = ?";
                $values[] = $value;
            }
        }
        
        if (empty($columns)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE orders SET " . implode(', ', $columns) . ", updated_at = NOW() WHERE id = ?";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($values);
    }

    
    public function update($id, $data) {
        return $this->updateOrder($id, $data);
    }

    public function deleteOrder($id) {
        $stmt = $this->connection->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->execute([$id]);
        
        $stmt = $this->connection->prepare("DELETE FROM orders WHERE id = ?");
        return $stmt->execute([$id]);
    }

    
    public function delete($id) {
        return $this->deleteOrder($id);
    }

    
    public function updateStatus($id, $status) {
        if (!$status) {
            return false;
        }

        $stmt = $this->connection->prepare(
            "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?"
        );
        return $stmt->execute([$status, $id]);
    }

    
    public function addItem($orderId, $productId, $quantity, $price) {
        $stmt = $this->connection->prepare(
            "INSERT INTO order_items (order_id, product_id, quantity, price, created_at) 
             VALUES (?, ?, ?, ?, NOW())"
        );
        return $stmt->execute([$orderId, $productId, $quantity, $price]);
    }

    
    public function getOrderItems($orderId) {
        $stmt = $this->connection->prepare(
            "SELECT oi.*, p.name, p.description FROM order_items oi 
             JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ?"
        );
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function removeItem($itemId) {
        $stmt = $this->connection->prepare("DELETE FROM order_items WHERE id = ?");
        return $stmt->execute([$itemId]);
    }
}
?>