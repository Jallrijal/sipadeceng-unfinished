<?php
class UserSnapshot extends BaseModel {
    protected $table = 'user_snapshots';
    protected $timestamps = false; // Disable timestamps karena tabel tidak punya created_at/updated_at
    protected $fillable = [
        'original_user_id', 'username', 'nama', 'nip', 'jabatan', 
        'golongan', 'tanggal_masuk', 'unit_kerja', 'atasan', 'user_type', 
        'snapshot_type', 'snapshot_date', 'reason'
    ];
    
    /**
     * Membuat snapshot dari data user
     */
    public function createSnapshot($userData, $snapshotType = 'modified', $reason = null) {
        $data = [
            'original_user_id' => $userData['id'],
            'username' => $userData['username'],
            'nama' => $userData['nama'],
            'nip' => $userData['nip'],
            'jabatan' => $userData['jabatan'],
            'golongan' => $userData['golongan'],
            'tanggal_masuk' => $userData['tanggal_masuk'],
            'unit_kerja' => $userData['unit_kerja'],
            'atasan' => $userData['atasan'] ?? null,
            'user_type' => $userData['user_type'],
            'snapshot_type' => $snapshotType,
            'snapshot_date' => date('Y-m-d H:i:s'),
            'reason' => $reason
        ];
        
        return $this->create($data);
    }
    
    /**
     * Mendapatkan snapshot berdasarkan original_user_id
     */
    public function getByOriginalUserId($originalUserId) {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE original_user_id = ? ORDER BY snapshot_date DESC LIMIT 1",
            [$originalUserId]
        );
    }
    
    /**
     * Mendapatkan semua snapshot untuk user tertentu
     */
    public function getAllByOriginalUserId($originalUserId) {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE original_user_id = ? ORDER BY snapshot_date DESC",
            [$originalUserId]
        );
    }
    
    /**
     * Mendapatkan snapshot berdasarkan tipe
     */
    public function getByType($type) {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE snapshot_type = ? ORDER BY snapshot_date DESC",
            [$type]
        );
    }
    
    /**
     * Mendapatkan data user untuk leave request (baik dari users atau user_snapshots)
     */
    public function getUserDataForLeave($userId, $userSnapshotId = null) {
        if ($userSnapshotId) {
            // Jika ada snapshot, gunakan data snapshot
            return $this->db->fetch(
                "SELECT id, username, nama, nip, jabatan, golongan, tanggal_masuk, unit_kerja, user_type, 'snapshot' as data_source
                 FROM {$this->table} WHERE id = ?",
                [$userSnapshotId]
            );
        } else {
            // Jika tidak ada snapshot, gunakan data user aktif
            return $this->db->fetch(
                "SELECT id, username, nama, nip, jabatan, golongan, tanggal_masuk, unit_kerja, user_type, 'active' as data_source
                 FROM users WHERE id = ?",
                [$userId]
            );
        }
    }
    
    /**
     * Mendapatkan statistik snapshot
     */
    public function getSnapshotStats() {
        $sql = "SELECT 
                    snapshot_type,
                    COUNT(*) as total,
                    DATE(snapshot_date) as snapshot_date
                FROM {$this->table} 
                GROUP BY snapshot_type, DATE(snapshot_date)
                ORDER BY snapshot_date DESC";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Mencari snapshot berdasarkan nama atau NIP
     */
    public function searchSnapshots($keyword) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE nama LIKE ? OR nip LIKE ? OR username LIKE ?
                ORDER BY snapshot_date DESC";
        
        $searchTerm = "%{$keyword}%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }
    
    /**
     * Mendapatkan semua snapshot dengan filter
     */
    public function getAll($filters = []) {
        $sql = "SELECT * FROM {$this->table}";
        $conditions = [];
        $params = [];
        
        if (isset($filters['type'])) {
            $conditions[] = "snapshot_type = ?";
            $params[] = $filters['type'];
        }
        
        if (isset($filters['search'])) {
            $conditions[] = "(nama LIKE ? OR nip LIKE ? OR username LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY snapshot_date DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Mendapatkan snapshot berdasarkan ID
     */
    public function findById($id) {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }
} 