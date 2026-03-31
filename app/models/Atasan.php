<?php
class Atasan extends BaseModel {
    protected $table = 'atasan';
    protected $primaryKey = 'id_atasan';
    protected $fillable = ['nama_atasan', 'NIP', 'jabatan', 'role'];
    protected $timestamps = false;
    
    /**
     * Get all atasan ordered by name
     */
    public function getAllAtasan() {
    return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY id_atasan");
    }
    
    /**
     * Get atasan by ID
     */
    public function getAtasanById($id) {
        // Pastikan menggunakan primaryKey yang benar (id_atasan)
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id_atasan = ?", [$id]);
    }
    
    /**
     * Check if NIP already exists
     */
    public function isNipExists($nip, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE NIP = ?";
        $params = [$nip];
        
        if ($excludeId) {
            $sql .= " AND id_atasan != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result && $result['total'] > 0;
    }
    
    /**
     * Get atasan with user count
     */
    public function getAtasanWithUserCount() {
    $sql = "SELECT a.*, COUNT(u.id) as user_count 
        FROM {$this->table} a 
        LEFT JOIN users u ON a.id_atasan = u.atasan AND u.is_deleted = 0 
        GROUP BY a.id_atasan 
        ORDER BY a.id_atasan";
    return $this->db->fetchAll($sql);
    }
    
    /**
     * Get users under specific atasan
     */
    public function getUsersUnderAtasan($atasanId) {
    $sql = "SELECT * FROM users WHERE atasan = ? AND is_deleted = 0 ORDER BY id";
    return $this->db->fetchAll($sql, [$atasanId]);
    }
}
