<?php
class DocumentModel extends Model {
    protected $table = 'leave_documents';
    
    public function createDocument($data) {
        return $this->create($data);
    }
    
    public function getByLeaveId($leaveId) {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE leave_request_id = ? ORDER BY created_at DESC",
            [$leaveId]
        );
    }
    
    public function getLatestByLeaveId($leaveId, $type) {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} 
             WHERE leave_request_id = ? AND document_type = ? 
             ORDER BY created_at DESC LIMIT 1",
            [$leaveId, $type]
        );
    }
    
    public function updateStatus($id, $status) {
        return $this->update($id, ['status' => $status]);
    }
    
    public function deleteByLeaveId($leaveId) {
        return $this->db->query(
            "DELETE FROM {$this->table} WHERE leave_request_id = ?",
            [$leaveId]
        );
    }
}