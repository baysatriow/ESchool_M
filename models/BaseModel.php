<?php

abstract class BaseModel {
    protected $conn;
    protected $table_name;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $query = "INSERT INTO " . $this->table_name . " ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->conn->prepare($query);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        return $stmt->execute();
    }
    
    public function read($conditions = [], $limit = null, $offset = null) {
        $query = "SELECT * FROM " . $this->table_name;
        
        if (!empty($conditions)) {
            $where_clauses = [];
            foreach ($conditions as $key => $value) {
                $where_clauses[] = "{$key} = :{$key}";
            }
            $query .= " WHERE " . implode(' AND ', $where_clauses);
        }
        
        if ($limit) {
            $query .= " LIMIT {$limit}";
            if ($offset) {
                $query .= " OFFSET {$offset}";
            }
        }
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function update($id, $data) {
        $set_clauses = [];
        foreach ($data as $key => $value) {
            $set_clauses[] = "{$key} = :{$key}";
        }
        
        $query = "UPDATE " . $this->table_name . " SET " . implode(', ', $set_clauses) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindValue(':id', $id);
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        
        return $stmt->execute();
    }
    
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findAll($orderBy = 'id', $orderDirection = 'ASC') {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY {$orderBy} {$orderDirection}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAll() {
        return $this->findAll();
    }
    
    public function count($conditions = []) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        
        if (!empty($conditions)) {
            $where_clauses = [];
            foreach ($conditions as $key => $value) {
                $where_clauses[] = "{$key} = :{$key}";
            }
            $query .= " WHERE " . implode(' AND ', $where_clauses);
        }
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'];
    }
    
    protected function createAndGetId($data) {
        $this->create($data);
        return $this->conn->lastInsertId();
    }
}
