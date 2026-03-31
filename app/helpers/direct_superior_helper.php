<?php
/**
 * Helper untuk mengotomatisasi penentuan atasan langsung berdasarkan jabatan pegawai
 */

/**
 * Tentukan atasan langsung berdasarkan jabatan pegawai secara otomatis
 * 
 * Aturan atasan langsung:
 * 1. Ketua = NULL
 * 2. Wakil Ketua, Panitera, Sekretaris, Hakim Tinggi, Hakim Yustisial = Ketua
 * 3. Kepala Bagian Perencanaan dan Kepegawaian, Kepala Bagian Umum dan Keuangan = Sekretaris
 * 4. Panitera Muda Hukum, Panitera Muda Banding, Panitera Pengganti = Panitera
 * 5. Kepala Subbagian (sub-bagian tertentu) = Kepala Bagian yang relevan
 * 6. Pegawai non-struktural = ditentukan dari unit kerja yang tertulis di belakang koma
 *
 * @param string $jabatan - Nama jabatan pegawai
 * @return int|null - ID atasan dari tabel atasan, atau null jika tidak ditemukan
 */
function getAutomaticDirectSuperior($jabatan) {
    if (!$jabatan || empty(trim($jabatan))) {
        return null;
    }

    $db = Database::getInstance();
    $jabatan = trim($jabatan);

    // 1. Cek apakah ini jabatan struktural yang punya atasan fixed
    $superiorByPosition = getStructuralSuperior($jabatan, $db);
    if ($superiorByPosition !== null) {
        return $superiorByPosition;
    }

    // 2. Jika bukan struktur tetap, ekstrak unit kerja dari jabatan (setelah koma)
    $unitKerja = extractUnitKerjaFromJabatan($jabatan);
    if ($unitKerja) {
        $superiorByUnit = getSuperiorByUnitKerja($unitKerja, $db);
        if ($superiorByUnit !== null) {
            return $superiorByUnit;
        }
    }

    return null;
}

/**
 * Tentukan atasan untuk jabatan struktural tetap
 */
function getStructuralSuperior($jabatan, $db) {
    $jabatan = trim($jabatan);

    // Ketua tidak punya atasan
    if (strcasecmp($jabatan, 'Ketua') === 0) {
        return null;
    }

    // Wakil Ketua, Panitera, Sekretaris, Hakim Tinggi, Hakim Yustisial, Ketua Pengadilan Agama -> Ketua
    $directToKetua = ['Wakil Ketua', 'Panitera', 'Sekretaris', 'Hakim Tinggi', 'Hakim Yustisial', 'Ketua Pengadilan Agama'];
    foreach ($directToKetua as $position) {
        if (strcasecmp($jabatan, $position) === 0) {
            return getAtasanIdByPosition($db, 'Ketua');
        }
    }

    // Kepala Bagian Perencanaan dan Kepegawaian, Kepala Bagian Umum dan Keuangan -> Sekretaris
    $directToSekretaris = ['Kepala Bagian Perencanaan dan Kepegawaian', 'Kepala Bagian Umum dan Keuangan'];
    foreach ($directToSekretaris as $position) {
        if (strcasecmp($jabatan, $position) === 0) {
            return getAtasanIdByPosition($db, 'Sekretaris');
        }
    }

    // Panitera Muda Hukum, Panitera Muda Banding, Panitera Pengganti -> Panitera
    $directToPanitera = ['Panitera Muda Hukum', 'Panitera Muda Banding', 'Panitera Pengganti'];
    foreach ($directToPanitera as $position) {
        if (strcasecmp($jabatan, $position) === 0) {
            return getAtasanIdByPosition($db, 'Panitera');
        }
    }

    // Kepala Subbagian (multiple) -> Kepala Bagian yang sesuai
    if (stripos($jabatan, 'Kepala Subbagian') !== false) {
        return getKepalaSubbagianSuperior($jabatan, $db);
    }

    return null;
}

/**
 * Tentukan atasan untuk Kepala Subbagian berdasarkan subbagiannya
 */
function getKepalaSubbagianSuperior($jabatan, $db) {
    // Subbagian Rencana Program dan Anggaran -> Kepala Bagian Perencanaan dan Kepegawaian
    if (stripos($jabatan, 'Subbagian Rencana Program dan Anggaran') !== false) {
        return getAtasanIdByPosition($db, 'Kepala Bagian Perencanaan dan Kepegawaian');
    }

    // Subbagian Kepegawaian dan Teknologi Informasi -> Kepala Bagian Perencanaan dan Kepegawaian
    if (stripos($jabatan, 'Subbagian Kepegawaian dan Teknologi Informasi') !== false) {
        return getAtasanIdByPosition($db, 'Kepala Bagian Perencanaan dan Kepegawaian');
    }

    // Subbagian Tata Usaha dan Rumah Tangga -> Kepala Bagian Umum dan Keuangan
    if (stripos($jabatan, 'Subbagian Tata Usaha dan Rumah Tangga') !== false) {
        return getAtasanIdByPosition($db, 'Kepala Bagian Umum dan Keuangan');
    }

    // Subbagian Keuangan dan Pelaporan -> Kepala Bagian Umum dan Keuangan
    if (stripos($jabatan, 'Subbagian Keuangan dan Pelaporan') !== false) {
        return getAtasanIdByPosition($db, 'Kepala Bagian Umum dan Keuangan');
    }

    return null;
}

/**
 * Ekstrak unit kerja dari jabatan (setelah koma)
 * Contoh: "Klerek - Analis Perkara Peradilan, Panitera Muda Hukum" -> "Panitera Muda Hukum"
 */
function extractUnitKerjaFromJabatan($jabatan) {
    if (stripos($jabatan, ',') === false) {
        return null;
    }

    $parts = explode(',', $jabatan);
    if (isset($parts[1])) {
        return trim($parts[1]);
    }

    return null;
}

/**
 * Tentukan atasan berdasarkan unit kerja dari jabatan
 * Unit kerja biasanya adalah posisi struktural yang ada di tabel atasan
 */
function getSuperiorByUnitKerja($unitKerja, $db) {
    if (!$unitKerja || empty(trim($unitKerja))) {
        return null;
    }

    // Mari kita cari unit kerja excat match di tabel atasan
    // Unit kerja harus cocok dengan jabatan atasan
    $unitKerja = trim($unitKerja);

    // Mapping unit kerja ke jabatan atasan
    $mapping = [
        'Sekretaris' => 'Sekretaris',
        'Panitera Muda Hukum' => 'Panitera Muda Hukum',
        'Panitera Muda Banding' => 'Panitera Muda Banding',
        'Kepala Bagian Perencanaan dan Kepegawaian' => 'Kepala Bagian Perencanaan dan Kepegawaian',
        'Bagian Perencanaan dan Kepegawaian' => 'Kepala Bagian Perencanaan dan Kepegawaian',
        'Kepala Bagian Umum dan Keuangan' => 'Kepala Bagian Umum dan Keuangan',
        'Bagian Umum dan Keuangan' => 'Kepala Bagian Umum dan Keuangan',
        'Subbagian Rencana Program dan Anggaran' => 'Kepala Subbagian, Subbagian Rencana Program dan Anggaran',
        'Subbagian Kepegawaian dan Teknologi Informasi' => 'Kepala Subbagian, Subbagian Kepegawaian dan Teknologi Informasi',
        'Subbagian Tata Usaha dan Rumah Tangga' => 'Kepala Subbagian, Subbagian Tata Usaha dan Rumah Tangga',
        'Subbagian Keuangan dan Pelaporan' => 'Kepala Subbagian, Subbagian Keuangan dan Pelaporan',
    ];

    // Exact match dulu
    if (isset($mapping[$unitKerja])) {
        $targetJabatan = $mapping[$unitKerja];
        return getAtasanIdByPosition($db, $targetJabatan);
    }

    // Jika tidak ada exact match, cari yang cocok (partial match dari jabatan)
    // Coba untuk subbagian dengan prefix "Kepala Subbagian"
    if (stripos($unitKerja, 'Subbagian') !== false) {
        // Cari atasan dengan jabatan yg mengandung unit kerja ini
        $sql = "SELECT id_atasan FROM atasan WHERE jabatan LIKE ? LIMIT 1";
        $result = $db->fetch($sql, ['%' . $unitKerja . '%']);
        if ($result) {
            return $result['id_atasan'];
        }
    }

    return null;
}

/**
 * Helper untuk mendapatkan ID atasan berdasarkan nama jabatan
 */
function getAtasanIdByPosition($db, $jabatan) {
    if (!$jabatan || empty(trim($jabatan))) {
        return null;
    }

    // Exact match dulu
    $sql = "SELECT id_atasan FROM atasan WHERE jabatan = ? LIMIT 1";
    $result = $db->fetch($sql, [$jabatan]);
    if ($result) {
        return $result['id_atasan'];
    }

    // Jika tidak ada, coba dengan LIKE untuk matching parsial
    $sql = "SELECT id_atasan FROM atasan WHERE jabatan LIKE ? LIMIT 1";
    $result = $db->fetch($sql, ['%' . trim($jabatan) . '%']);
    if ($result) {
        return $result['id_atasan'];
    }

    return null;
}

/**
 * Helper untuk mendapatkan ID atasan berdasarkan custom criteria
 */
function getAtasanIdByCriteria($db, $criteria, $prefix = "SELECT id_atasan FROM atasan WHERE") {
    $sql = $prefix . " " . $criteria . " LIMIT 1";
    $result = $db->fetch($sql);
    if ($result) {
        return $result['id_atasan'];
    }
    return null;
}

/**
 * Update atasan otomatis untuk seorang pegawai
 * Function ini bisa dipanggil setelah update/create pegawai
 */
function updateAutoDirectSuperior($userId, $jabatan) {
    $db = Database::getInstance();
    $atasanId = getAutomaticDirectSuperior($jabatan);
    
    if ($atasanId !== null) {
        // Update user dengan atasan yang ditentukan otomatis
        $sql = "UPDATE users SET atasan = ? WHERE id = ?";
        return $db->execute($sql, [$atasanId, $userId]);
    } else {
        // Jika tidak ada atasan yang cocok, set ke NULL
        $sql = "UPDATE users SET atasan = NULL WHERE id = ?";
        return $db->execute($sql, [$userId]);
    }
}
