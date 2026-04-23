<?php

abstract class Model {
    
    protected $connection;
    protected $table;

    
    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function findBy($column, $value) {
        $query = "SELECT * FROM " . $this->table . " WHERE " . $column . " = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function findOneBy($column, $value) {
        $query = "SELECT * FROM " . $this->table . " WHERE " . $column . " = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$value]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        return $stmt->execute([$id]);
    }

    
    protected function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($params) ? $stmt : false;
    }

    
    protected function fetch($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    protected function fetchAll($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    
    public function commit() {
        return $this->connection->commit();
    }

    
    public function rollback() {
        return $this->connection->rollBack();
    }
}
?>
