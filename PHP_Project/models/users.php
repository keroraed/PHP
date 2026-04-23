<?php 
require_once __DIR__ . '/../config/dp.php';

class User extends Service {
    public function getAllUser() {
        return $this->connection->query("SELECT * FROM users");
    }

    public function getAll() {
        $result = $this->connection->query("SELECT * FROM users");
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function insertUser($fname, $email, $password, $roomNo, $extNo, $picture = null) {
        $stmt = $this->connection->prepare(
            "INSERT INTO users (name, email, password, room_no, ext, profile_picture, role, created_at) 
             VALUES (?,?,?,?,?,?,'user',NOW())"
        );
        return $stmt->execute([$fname, $email, $password, $roomNo, $extNo, $picture]);
    }
    
    public function getUserById($id) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateUser($id, $fname, $email, $password, $roomNo, $extNo, $picture = null) {
        $stmt = $this->connection->prepare(
            "UPDATE users SET name=?, email=?, password=?, room_no=?, ext=?, profile_picture=? WHERE id=?"
        );
        return $stmt->execute([$fname, $email, $password, $roomNo, $extNo, $picture, $id]);
    }

    public function deleteUser($id) {
        $stmt = $this->connection->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function authenticate($email, $password) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    
    public function findOneBy($column, $value) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE {$column} = ? LIMIT 1");
        $stmt->execute([$value]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    
    public function insert($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO users ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(array_values($data));
    }
    
    
    public function update($id, $data) {
        $set = implode(', ', array_map(function($key) {
            return "{$key} = ?";
        }, array_keys($data)));
        
        $sql = "UPDATE users SET {$set} WHERE id = ?";
        $values = array_values($data);
        $values[] = $id;
        
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($values);
    }

    
    public function getUserByEmail($email) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function getAllUsers() {
        $stmt = $this->connection->prepare("SELECT id, name, email, role, profile_picture, room_no, ext, building, created_at FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function findById($id) {
        return $this->getUserById($id);
    }

    
    public function delete($id) {
        return $this->deleteUser($id);
    }
} 
?>