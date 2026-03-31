<?php
abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $timestamps = true;
    
    public function __construct() {
        $db = Database::getInstance();
        $this->db = $db;
    }
    
    public function all($orderBy = null) {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        return $this->db->fetchAll($sql);
    }
    
    public function find($id) {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?", 
            [$id]
        );
    }
    
    public function findBy($field, $value) {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE {$field} = ?", 
            [$value]
        );
    }
    
    public function where($field, $value, $operator = '=') {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE {$field} {$operator} ?", 
            [$value]
        );
    }
    
    public function whereMultiple($conditions) {
        $where = [];
        $values = [];
        
        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $where[] = "{$field} {$value['operator']} ?";
                $values[] = $value['value'];
            } else {
                $where[] = "{$field} = ?";
                $values[] = $value;
            }
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where);
        return $this->db->fetchAll($sql, $values);
    }
    
    public function create($data) {
        // Filter only fillable fields
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        
        // Add timestamps if enabled
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $this->db->query($sql, $values);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        // Filter only fillable fields
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        
        // Update timestamp if enabled
        if ($this->timestamps && !isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE {$this->primaryKey} = ?";
        return $this->db->query($sql, $values);
    }
    
    public function delete($id) {
        return $this->db->query(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?", 
            [$id]
        );
    }
    
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        
        if (!empty($conditions)) {
            $where = [];
            $values = [];
            
            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = ?";
                $values[] = $value;
            }
            
            $sql .= " WHERE " . implode(' AND ', $where);
            $result = $this->db->fetch($sql, $values);
        } else {
            $result = $this->db->fetch($sql);
        }
        
        return $result['total'];
    }
    
    public function exists($field, $value, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$field} = ?";
        $params = [$value];
        
        if ($excludeId) {
            $sql .= " AND {$this->primaryKey} != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['total'] > 0;
    }
    
    public function paginate($page = 1, $perPage = 10, $conditions = []) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table}";
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = ?";
                $params[] = $value;
            }
            $whereClause = " WHERE " . implode(' AND ', $where);
            $sql .= $whereClause;
            $countSql .= $whereClause;
        }
        
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        
        // Get total count
        $totalResult = $this->db->fetch($countSql, $params);
        $total = $totalResult['total'];
        
        // Get data
        $data = $this->db->fetchAll($sql, $params);
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
    
    protected function beginTransaction() {
        $this->db->beginTransaction();
    }
    
    protected function commit() {
        $this->db->commit();
    }
    
    protected function rollback() {
        $this->db->rollback();
    }
}