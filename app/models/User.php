<?php
class User extends BaseModel {
    protected $table = 'users';
    protected $fillable = ['username', 'password', 'nama', 'nip', 'email', 'jabatan', 'golongan', 'tanggal_masuk', 'unit_kerja', 'atasan', 'user_type', 'is_deleted', 'deleted_at', 'is_modified', 'last_modified_at', 'failed_login_attempts', 'lock_until'];
    
    public function findByUsername($username) {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE username = ?", [$username]);
    }
    
    public function getAdmins() {
        return $this->where('user_type', 'admin');
    }
    
    public function getUsers() {
        return $this->db->fetchAll("SELECT * FROM {$this->table} WHERE user_type IN ('pegawai', 'atasan') AND is_deleted = 0");
    }

    // Mengecek apakah username sudah ada di database
    public function isUsernameExists($username) {
        $result = $this->db->fetch("SELECT COUNT(*) as total FROM {$this->table} WHERE username = ?", [$username]);
        return $result && $result['total'] > 0;
    }

    // Mengecek apakah NIP sudah ada di database
    public function isNipExists($nip) {
        $result = $this->db->fetch("SELECT COUNT(*) as total FROM {$this->table} WHERE nip = ?", [$nip]);
        return $result && $result['total'] > 0;
    }
    
    /**
     * Get user with atasan information
     */
    public function getUserWithAtasan($id) {
        $sql = "SELECT u.*, a.nama_atasan, a.NIP as nip_atasan 
                FROM {$this->table} u 
                LEFT JOIN atasan a ON u.atasan = a.id_atasan 
                WHERE u.id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Get all users with atasan information
     */
    public function getAllUsersWithAtasan() {
    $sql = "SELECT u.*, a.nama_atasan, a.NIP as nip_atasan, s.nama_satker 
        FROM {$this->table} u 
        LEFT JOIN atasan a ON u.atasan = a.id_atasan 
        LEFT JOIN satker s ON u.unit_kerja = s.id_satker 
        WHERE u.is_deleted = 0 
        ORDER BY u.id";
    return $this->db->fetchAll($sql);
    }
    
    /**
     * Get active users with atasan information
     */
    public function getActiveUsersWithAtasan() {
    $sql = "SELECT u.*, a.nama_atasan, a.NIP as nip_atasan 
        FROM {$this->table} u 
        LEFT JOIN atasan a ON u.atasan = a.id_atasan 
        WHERE u.is_deleted = 0 
        ORDER BY u.id";
    return $this->db->fetchAll($sql);
    }
    
    /**
     * Get atasan list for dropdown
     */
    public function getAtasanList() {
    $sql = "SELECT id_atasan, nama_atasan, NIP FROM atasan ORDER BY id_atasan";
    return $this->db->fetchAll($sql);
    }
    
    /**
     * Get users by atasan ID
     */
    public function getUsersByAtasan($atasanId) {
    $sql = "SELECT * FROM {$this->table} WHERE atasan = ? AND is_deleted = 0 ORDER BY id";
    return $this->db->fetchAll($sql, [$atasanId]);
    }
    
    /**
     * Soft delete user
     */
    public function softDelete($id) {
        $data = [
            'is_deleted' => 1,
            'deleted_at' => date('Y-m-d H:i:s')
        ];
        return $this->update($id, $data);
    }
    
    /**
     * Restore soft deleted user
     */
    public function restore($id) {
        $data = [
            'is_deleted' => 0,
            'deleted_at' => null
        ];
        return $this->update($id, $data);
    }
    
    /**
     * Get active users only
     */
    public function getActiveUsers() {
    return $this->db->fetchAll("SELECT * FROM {$this->table} WHERE is_deleted = 0 ORDER BY id");
    }
    
    /**
     * Get deleted users
     */
    public function getDeletedUsers() {
        return $this->db->fetchAll("SELECT * FROM {$this->table} WHERE is_deleted = 1 ORDER BY deleted_at DESC");
    }
    
    /**
     * Get modified users
     */
    public function getModifiedUsers() {
        return $this->db->fetchAll("SELECT * FROM {$this->table} WHERE is_modified = 1 ORDER BY last_modified_at DESC");
    }
    
    /**
     * Override update method to track changes
     */
    public function update($id, $data) {
        // Get current user data
        $currentUser = $this->find($id);
        if (!$currentUser) {
            return false;
        }
        
        // Check if important fields are being changed
        $importantFields = ['nama', 'nip', 'jabatan', 'unit_kerja', 'golongan', 'atasan'];
        $hasImportantChanges = false;
        
        foreach ($importantFields as $field) {
            if (isset($data[$field]) && $data[$field] !== $currentUser[$field]) {
                $hasImportantChanges = true;
                break;
            }
        }
        
        // If important changes detected, create snapshot before update
        if ($hasImportantChanges) {
            $userSnapshotModel = new UserSnapshot();
            $userSnapshotModel->createSnapshot($currentUser, 'modified', 'User data modified');
        }
        
        return parent::update($id, $data);
    }
    
    /**
     * Override delete method to perform permanent delete
     */
    public function delete($id) {
        // Get current user data
        $currentUser = $this->find($id);
        if (!$currentUser) {
            return false;
        }
        
        // Create snapshot before permanent delete for history preservation
        $userSnapshotModel = new UserSnapshot();
        $userSnapshotModel->createSnapshot($currentUser, 'deleted', 'User account permanently deleted');
        
        // Perform permanent delete
        return parent::delete($id);
    }
}