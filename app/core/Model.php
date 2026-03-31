<?php
class Model {
    protected $db;
    protected $table;
    
    public function __construct() {
        // Ensure Database class is loaded
        if (!class_exists('Database')) {
            require_once 'app/core/Database.php';
        }
        $this->db = Database::getInstance();
    }
    
    // Public methods untuk database operations
    public function getDb() {
        return $this->db;
    }
    
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    public function commit() {
        return $this->db->commit();
    }
    
    public function rollback() {
        return $this->db->rollback();
    }
    
    public function query($sql, $params = []) {
        return $this->db->query($sql, $params);
    }
    
    public function fetch($sql, $params = []) {
        return $this->db->fetch($sql, $params);
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->db->fetchAll($sql, $params);
    }
    
    public function all() {
        return $this->db->fetchAll("SELECT * FROM {$this->table}");
    }
    
    public function find($id) {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }
    
    public function create($data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $this->db->query($sql, $values);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        return $this->db->query($sql, $values);
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM {$this->table} WHERE id = ?", [$id]);
    }
    
    public function where($field, $value) {
        return $this->db->fetchAll("SELECT * FROM {$this->table} WHERE $field = ?", [$value]);
    }
}