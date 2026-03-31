<?php
// Helper untuk mengambil nama satker dan nama kota dari id_satker
require_once dirname(__DIR__) . '/models/Satker.php';

function getNamaSatkerById($id_satker) {
    $satkerModel = new Satker();
    return $satkerModel->getNamaSatker($id_satker);
}

function getKotaFromNamaSatker($nama_satker) {
    // Ambil kata terakhir dari nama satker yang biasanya adalah nama kota
    // Contoh: "Pengadilan Agama Makassar" => "Makassar"
    //         "Pengadilan Tinggi Agama Makassar" => "Makassar"
    //         "Pengadilan Agama Maros" => "Maros"
    if (preg_match('/([A-Za-z\s]+) ([A-Za-z]+)$/u', $nama_satker, $matches)) {
        return trim($matches[2]);
    }
    return $nama_satker;
}
