<?php
class LeaveType extends Model {
    protected $table = 'leave_types';
    
    public function getById($id) {
        return $this->find($id);
    }
    
    public function getAllActive() {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} ORDER BY id"
        );
    }
}