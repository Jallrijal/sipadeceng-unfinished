<?php
// Helper untuk mengambil nama satker dari id
function get_nama_satker($id_satker) {
    if (!$id_satker) return '-';
    require_once __DIR__ . '/../models/Satker.php';
    $satkerModel = new Satker();
    $nama = $satkerModel->getNamaSatker($id_satker);
    if ($nama == $id_satker) {
        return '-';
    }
    return $nama;
}
