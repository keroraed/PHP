<?php
require_once __DIR__ . '/../config/dp.php';

class ContactMessage extends Service {
    public function ensureTable() {
        $sql = "CREATE TABLE IF NOT EXISTS contact_messages (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50) DEFAULT NULL,
            subject VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_contact_messages_is_read (is_read),
            INDEX idx_contact_messages_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        return $this->connection->exec($sql) !== false;
    }

    public function insert($data) {
        $this->ensureTable();

        $stmt = $this->connection->prepare(
            "INSERT INTO contact_messages (name, email, phone, subject, message)
             VALUES (?, ?, ?, ?, ?)"
        );

        return $stmt->execute([
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['phone'] ?? null,
            $data['subject'] ?? '',
            $data['message'] ?? ''
        ]);
    }

    public function getAll() {
        $this->ensureTable();

        $stmt = $this->connection->prepare(
            "SELECT id, name, email, phone, subject, message, is_read, created_at
             FROM contact_messages
             ORDER BY created_at DESC"
        );
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($id) {
        $this->ensureTable();

        $id = (int)$id;
        if ($id <= 0) {
            return false;
        }

        $stmt = $this->connection->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
        if (!$stmt->execute([$id])) {
            return false;
        }

        return $stmt->rowCount() > 0 || $this->exists($id);
    }

    private function exists($id) {
        $stmt = $this->connection->prepare("SELECT id FROM contact_messages WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);

        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }
}
