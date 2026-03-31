<?php
class AdminActivity extends BaseModel {
    protected $table = 'admin_activities';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getActivitiesByAdmin($adminId, $month = null, $year = null) {
        $sql = "SELECT aa.*, lr.nomor_surat, u.nama as pegawai_nama, lt.nama_cuti as jenis_cuti
                FROM {$this->table} aa
                LEFT JOIN leave_requests lr ON aa.leave_id = lr.id
                LEFT JOIN users u ON lr.user_id = u.id
                LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id
                WHERE aa.admin_id = ?
                AND aa.created_at >= ? AND aa.created_at < ?";
        
        $startDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
        $endDate = date('Y-m-d', strtotime($startDate . ' +1 month'));
        
        return $this->db->fetchAll($sql, [$adminId, $startDate, $endDate]);
    }
    
    public function getMonthlyReport($adminId, $month, $year) {
        return $this->getActivitiesByAdmin($adminId, $month, $year);
    }
    
    public function logActivity($adminId, $activityType, $leaveId) {
        $sql = "INSERT INTO {$this->table} (admin_id, activity_type, leave_id, created_at) 
                VALUES (?, ?, ?, NOW())";
        return $this->db->execute($sql, [$adminId, $activityType, $leaveId]);
    }
}
?>