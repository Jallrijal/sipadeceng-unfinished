<?php
class Satker extends BaseModel {
    protected $table = 'satker';

    public function getAllSatker() {
        return $this->db->fetchAll("SELECT * FROM satker ORDER BY nama_satker");
    }

    public function getNamaSatker($id_satker) {
        $result = $this->db->fetch("SELECT nama_satker FROM satker WHERE id_satker = ?", [$id_satker]);
        return $result ? $result['nama_satker'] : $id_satker;
    }

    /**
     * Cari id_satker berdasarkan nama satker (case-insensitive, trim)
     */
    public function getIdByNamaSatker($nama_satker) {
        $result = $this->db->fetch(
            "SELECT id_satker FROM satker WHERE TRIM(LOWER(nama_satker)) = ? LIMIT 1",
            [strtolower(trim($nama_satker))]
        );
        return $result ? $result['id_satker'] : null;
    }
}
