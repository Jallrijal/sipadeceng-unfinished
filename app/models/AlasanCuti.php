<?php
class AlasanCuti extends Model {
    protected $table = 'alasan_cuti';

    public function getByLeaveType($leaveTypeId) {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE leave_type_id = ? AND is_active = 1 ORDER BY alasan",
            [$leaveTypeId]
        );
    }

    public function getAllActive() {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY leave_type_id, alasan"
        );
    }

    public function getById($id) {
        return $this->find($id);
    }
} 