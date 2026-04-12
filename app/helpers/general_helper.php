<?php
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function baseUrl($url = '') {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $base = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
    return rtrim($base, '\\/') . '/' . ltrim($url, '/');
}

function generateNomorSurat($unitKerja) {
    $db = Database::getInstance();
    $tahun = date('Y');
    $bulan = getRomawi(date('n'));
    
    // Ambil kode unit
    $kodeUnit = getKodeUnit($unitKerja);
    
    // Hitung nomor urut
    $sql = "SELECT COUNT(*) as total FROM leave_requests 
            WHERE YEAR(created_at) = ? AND 
            user_id IN (SELECT id FROM users WHERE unit_kerja = ?)";
    
    $result = $db->fetch($sql, [$tahun, $unitKerja]);
    $nomorUrut = str_pad($result['total'] + 1, 3, '0', STR_PAD_LEFT);
    
    return $nomorUrut . '/' . $kodeUnit . '/PC/' . $bulan . '/' . $tahun;
}

function getKodeUnit($unitKerja) {
    $mapping = [
        'Makassar' => 'PA.Mks',
        'Gowa' => 'PA.Gw',
        'Takalar' => 'PA.Tkl',
        'Maros' => 'PA.Mrs',
        'Jeneponto' => 'PA.Jpt',
        'Bantaeng' => 'PA.Btg',
        'Bulukumba' => 'PA.Blk',
        'Sinjai' => 'PA.Snj',
        'Selayar' => 'PA.Sly',
        'Bone' => 'PA.Bn',
        'Soppeng' => 'PA.Spp',
        'Wajo' => 'PA.Wj',
        'Sidrap' => 'PA.Sdr',
        'Pinrang' => 'PA.Prg',
        'Enrekang' => 'PA.Enr',
        'Luwu Utara' => 'PA.Lut',
        'Luwu Timur' => 'PA.Ltm',
        'Luwu' => 'PA.Lw',
        'Toraja Utara' => 'PA.TrU',
        'Tana Toraja' => 'PA.TTr',
        'Pare' => 'PA.Pr',
        'Barru' => 'PA.Br',
        'Pangkep' => 'PA.Pkp',
        'Palopo' => 'PA.Plp'
    ];
    
    foreach ($mapping as $key => $code) {
        if (strpos($unitKerja, $key) !== false) {
            return $code;
        }
    }
    
    return 'PA';
}

function getRomawi($bulan) {
    $romawi = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ];
    return $romawi[$bulan];
}

/**
 * Determine whether the current logged-in user is allowed to download
 * a generated blanko for the given leave row.
 *
 * This mirrors the access control logic in LeaveController::downloadGeneratedDoc
 * and also knows about the status restrictions for various user types.
 *
 * @param array $leaveRow  Row returned by LeaveModel::getHistory / getDetail
 * @return bool
 */
function canDownloadGeneratedDocRow($leaveRow) {
    // must have a generated document available
    if (empty($leaveRow['has_generated_doc'])) {
        return false;
    }

    // admins (pimpinan) may download once the request has reached their
    // workflow stage; typical statuses are approved/rejected/changed/postponed
    if (function_exists('isAdmin') && isAdmin()) {
        return in_array($leaveRow['status'], ['awaiting_pimpinan', 'approved', 'rejected', 'changed', 'postponed']);
    }

    // atasan roles
    if (function_exists('isAtasan') && isAtasan()) {
        // ketua can be the direct atasan (level 1, status=pending) OR the final approver
        // (awaiting_pimpinan) OR can view completed requests – allow all relevant statuses.
        if (function_exists('isKetua') && isKetua()) {
            return in_array($leaveRow['status'], ['pending', 'pending_kasubbag', 'pending_kabag', 'pending_sekretaris', 'awaiting_pimpinan', 'approved', 'rejected', 'changed', 'postponed']);
        }

        // other atasan (including kasubbag/kabag/sekretaris) – only when the
        // leave is still pending or awaiting recommendation
        return in_array($leaveRow['status'], ['pending', 'pending_kasubbag', 'pending_kabag', 'pending_sekretaris', 'awaiting_pimpinan']);
    }

    // owner of the leave can always download their own generated blanko
    if (isset($_SESSION['user_id']) && isset($leaveRow['user_id']) && $_SESSION['user_id'] == $leaveRow['user_id']) {
        return true;
    }

    return false;
}

function getStatusBadge($status) {
    switch ($status) {
        case 'draft':
            return '<span class="badge bg-secondary">Draft</span>';
        case 'pending':
            return '<span class="badge bg-warning text-dark">Menunggu Atasan</span>';
        case 'pending_kasubbag':
            return '<span class="badge bg-info text-dark">Menunggu Kasubbag</span>';
        case 'awaiting_pimpinan':
            return '<span class="badge bg-warning text-dark">Menunggu Pimpinan</span>';
        case 'pending_admin_upload':
            return '<span class="badge bg-secondary">Menunggu Dokumen</span>';
        case 'approved':
            return '<span class="badge bg-success">Disetujui</span>';
        case 'rejected':
            return '<span class="badge bg-danger">Ditolak</span>';
        case 'changed':
            return '<span class="badge bg-warning text-dark">Perlu Diubah</span>';
        case 'postponed':
            return '<span class="badge bg-secondary">Ditangguhkan</span>';
        case 'pending_kabag':
            return '<span class="badge bg-info text-dark">Menunggu Kabag</span>';
        case 'pending_sekretaris':
            return '<span class="badge bg-info text-dark">Menunggu Sekretaris</span>';
        case 'completed':
            return '<span class="badge bg-primary">Selesai</span>';
        default:
            return '<span class="badge bg-secondary">' . ucfirst($status) . '</span>';
    }
}

function uploadDokumen($file) {
    $targetDir = "public/uploads/documents/";
    
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $newFilename = uniqid() . '_' . date('YmdHis') . '.' . $fileExtension;
    $targetFile = $targetDir . $newFilename;
    
    $allowedExtensions = ["pdf", "jpg", "jpeg", "png", "doc", "docx"];
    if (!in_array($fileExtension, $allowedExtensions)) {
        return ['success' => false, 'message' => 'Format file tidak diizinkan.'];
    }
    
    if ($file["size"] > 5000000) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (max 5MB).'];
    }
    
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return ['success' => true, 'filename' => $newFilename];
    } else {
        return ['success' => false, 'message' => 'Gagal mengupload file.'];
    }
}

// Fungsi untuk mendapatkan kuota berdasarkan leave_type_id
function getKuotaFromLeaveType($leaveTypeId) {
    $db = Database::getInstance();
    $sql = "SELECT max_days FROM leave_types WHERE id = ?";
    $result = $db->fetch($sql, [$leaveTypeId]);
    return $result ? $result['max_days'] : 0;
}

// Fungsi untuk membuat kuota awal berdasarkan jenis cuti
function createInitialKuotaByType($userId, $leaveTypeId) {
    $db = Database::getInstance();
    $kuota = getKuotaFromLeaveType($leaveTypeId);
    if ($kuota <= 0) return false;
    $tahun = date('Y');
    switch ($leaveTypeId) {
        case 2: // Cuti Besar
            // Cek exist
            $cek = $db->fetch("SELECT id FROM kuota_cuti_besar WHERE user_id = ?", [$userId]);
            if (!$cek) {
                $sql = "INSERT INTO kuota_cuti_besar (user_id, leave_type_id, kuota_total, sisa_kuota) VALUES (?, ?, ?, ?)";
                return $db->execute($sql, [$userId, $leaveTypeId, $kuota, $kuota]);
            }
            return true;
        case 3: // Cuti Sakit
            $cek = $db->fetch("SELECT id FROM kuota_cuti_sakit WHERE user_id = ? AND tahun = ?", [$userId, $tahun]);
            if (!$cek) {
                $sql = "INSERT INTO kuota_cuti_sakit (user_id, leave_type_id, tahun, kuota_tahunan, sisa_kuota) VALUES (?, ?, ?, ?, ?)";
                return $db->execute($sql, [$userId, $leaveTypeId, $tahun, $kuota, $kuota]);
            }
            return true;
        case 4: // Cuti Melahirkan
            $cek = $db->fetch("SELECT id FROM kuota_cuti_melahirkan WHERE user_id = ?", [$userId]);
            if (!$cek) {
                $sql = "INSERT INTO kuota_cuti_melahirkan (user_id, leave_type_id, kuota_total, jumlah_pengambilan, sisa_pengambilan, status) VALUES (?, ?, ?, 0, 3, 'tersedia')";
                return $db->execute($sql, [$userId, $leaveTypeId, $kuota]);
            }
            return true;
        case 5: // Cuti Alasan Penting
            $cek = $db->fetch("SELECT id FROM kuota_cuti_alasan_penting WHERE user_id = ? AND tahun = ?", [$userId, $tahun]);
            if (!$cek) {
                $sql = "INSERT INTO kuota_cuti_alasan_penting (user_id, leave_type_id, tahun, kuota_tahunan) VALUES (?, ?, ?, ?)";
                return $db->execute($sql, [$userId, $leaveTypeId, $tahun, $kuota]);
            }
            return true;
        case 6: // Cuti Luar Tanggungan
            $cek = $db->fetch("SELECT id FROM kuota_cuti_luar_tanggungan WHERE user_id = ? AND tahun = ?", [$userId, $tahun]);
            if (!$cek) {
                $sql = "INSERT INTO kuota_cuti_luar_tanggungan (user_id, leave_type_id, tahun, kuota_tahunan, sisa_kuota) VALUES (?, ?, ?, ?, ?)";
                return $db->execute($sql, [$userId, $leaveTypeId, $tahun, $kuota, $kuota]);
            }
            return true;
        default:
            return false;
    }
}

// Fungsi untuk mendapatkan sisa kuota berdasarkan jenis cuti
function getSisaKuotaByType($userId, $leaveTypeId, $tahun = null) {
    $db = Database::getInstance();
    
    switch ($leaveTypeId) {
        case 1: // Cuti Tahunan (menggunakan tabel leave_balances yang sudah ada)
            if (!$tahun) $tahun = date('Y');
            $sql = "SELECT sisa_kuota FROM leave_balances WHERE user_id = ? AND tahun = ?";
            $result = $db->fetch($sql, [$userId, $tahun]);
            return $result ? $result['sisa_kuota'] : 0;
            
        case 2: // Cuti Besar
            $sql = "SELECT sisa_kuota FROM kuota_cuti_besar WHERE user_id = ?";
            $result = $db->fetch($sql, [$userId]);
            return $result ? $result['sisa_kuota'] : 0;
            
        case 3: // Cuti Sakit
            if (!$tahun) $tahun = date('Y');
            $sql = "SELECT sisa_kuota FROM kuota_cuti_sakit WHERE user_id = ? AND tahun = ?";
            $result = $db->fetch($sql, [$userId, $tahun]);
            return $result ? $result['sisa_kuota'] : 0;
            
        case 4: // Cuti Melahirkan - cek sisa pengambilan
            $sql = "SELECT sisa_pengambilan FROM kuota_cuti_melahirkan WHERE user_id = ?";
            $result = $db->fetch($sql, [$userId]);
            return $result ? (int)$result['sisa_pengambilan'] : 0;
            
        case 5: // Cuti Alasan Penting - tidak berpatokan pada kuota DB
            // Cuti alasan penting tidak menggunakan kuota akumulatif.
            // Batas adalah per-pengajuan: maks 10 hari (pegawai biasa) atau 30 hari (hakim tinggi).
            // Kembalikan nilai besar agar validasi kuota tidak memblokir (validasi sesungguhnya di validateKuotaCuti).
            return 99999;
            
        case 6: // Cuti Luar Tanggungan
            if (!$tahun) $tahun = date('Y');
            $sql = "SELECT sisa_kuota FROM kuota_cuti_luar_tanggungan WHERE user_id = ? AND tahun = ?";
            $result = $db->fetch($sql, [$userId, $tahun]);
            return $result ? $result['sisa_kuota'] : 0;
            
        default:
            return 0;
    }
}

// Fungsi untuk mengurangi kuota berdasarkan jenis cuti
function kurangiKuotaByType($userId, $leaveTypeId, $jumlahHari, $tahun = null) {
    $db = Database::getInstance();
    switch ($leaveTypeId) {
        case 1: // Cuti Tahunan
            if (!$tahun) $tahun = date('Y');
            $tahunInt = (int)$tahun;
            $years = [$tahunInt - 2, $tahunInt - 1, $tahunInt];
            $remaining = $jumlahHari;
            foreach ($years as $y) {
                $sql = "SELECT sisa_kuota FROM leave_balances WHERE user_id = ? AND tahun = ?";
                $result = $db->fetch($sql, [$userId, $y]);
                $sisa = $result ? (int)$result['sisa_kuota'] : 0;
                if ($remaining > 0 && $sisa > 0) {
                    $potong = min($remaining, $sisa);
                    $db->execute("UPDATE leave_balances SET sisa_kuota = sisa_kuota - ? WHERE user_id = ? AND tahun = ?", [$potong, $userId, $y]);
                    $remaining -= $potong;
                }
            }
            return true;
        case 2: // Cuti Besar
            $sql = "UPDATE kuota_cuti_besar SET sisa_kuota = sisa_kuota - ? WHERE user_id = ?";
            return $db->execute($sql, [$jumlahHari, $userId]);
            
        case 3: // Cuti Sakit
            if (!$tahun) $tahun = date('Y');
            $sql = "UPDATE kuota_cuti_sakit SET sisa_kuota = sisa_kuota - ? WHERE user_id = ? AND tahun = ?";
            return $db->execute($sql, [$jumlahHari, $userId, $tahun]);
            
        case 4: // Cuti Melahirkan - tidak mengurangi kuota karena bukan akumulatif
            // Cuti melahirkan tidak mengurangi kuota, hanya mengubah status
            return true;
            
        case 5: // Cuti Alasan Penting - tidak mengurangi kuota karena bukan akumulatif
            // Cuti alasan penting tidak mengurangi kuota, hanya validasi maksimal hari
            return true;
            
        case 6: // Cuti Luar Tanggungan
            if (!$tahun) $tahun = date('Y');
            $sql = "UPDATE kuota_cuti_luar_tanggungan SET sisa_kuota = sisa_kuota - ? WHERE user_id = ? AND tahun = ?";
            return $db->execute($sql, [$jumlahHari, $userId, $tahun]);
            
        default:
            return false;
    }
}

// Fungsi untuk membuat semua kuota awal untuk user baru
function createAllInitialQuota($userId) {
    // Cuti tahunan (sudah ada di UserController)
    // createInitialQuota($userId);
    
    // Cuti lainnya
    createInitialKuotaByType($userId, 2); // Cuti Besar
    createInitialKuotaByType($userId, 3); // Cuti Sakit
    createInitialKuotaByType($userId, 4); // Cuti Melahirkan
    createInitialKuotaByType($userId, 5); // Cuti Alasan Penting
    createInitialKuotaByType($userId, 6); // Cuti Luar Tanggungan
}

// Fungsi untuk validasi kuota cuti
function validateKuotaCuti($userId, $leaveTypeId, $jumlahHari, $tahun = null) {
    $sisaKuota = getSisaKuotaByType($userId, $leaveTypeId, $tahun);
    
    // Untuk cuti sakit (id=3), selalu valid walau sisa kuota kurang
    if ($leaveTypeId == 3) {
        return [
            'valid' => true,
            'message' => ($sisaKuota < $jumlahHari) ? "Kuota cuti sakit tidak mencukupi. Sisa: {$sisaKuota} hari, Dibutuhkan: {$jumlahHari} hari. Pengajuan tetap diproses dan sisa kuota akan minus." : 'Kuota cuti sakit mencukupi'
        ];
    }
    
    // Validasi maksimal hari untuk Cuti Melahirkan (id=4) dan Cuti Alasan Penting (id=5)
    if ($leaveTypeId == 4) { // Cuti Melahirkan
        $maxDays = getKuotaFromLeaveType(4); // 90 hari
        // CEK JUMLAH PENGAMBILAN
        $db = Database::getInstance();
        $cek = $db->fetch("SELECT jumlah_pengambilan FROM kuota_cuti_melahirkan WHERE user_id = ?", [$userId]);
        $jumlahPengambilan = $cek ? (int)$cek['jumlah_pengambilan'] : 0;
        if ($jumlahPengambilan >= 3) {
            return [
                'valid' => false,
                'message' => "Cuti Melahirkan hanya dapat diambil maksimal 3 kali selama masa kerja. Anda sudah menggunakan seluruh hak tersebut."
            ];
        }
        if ($jumlahHari > $maxDays) {
            return [
                'valid' => false,
                'message' => "Cuti Melahirkan maksimal {$maxDays} hari per sekali mengajukan. Jumlah hari yang diajukan: {$jumlahHari} hari"
            ];
        }
        // Cuti melahirkan bersifat non-akumulatif: tidak dibandingkan dengan "sisa hari".
        // Jika lolos cek jumlah pengambilan dan batas maksimal hari, anggap valid dan hentikan validasi lanjutan.
        return ['valid' => true, 'message' => 'Kuota cuti melahirkan valid'];
    }
    
    if ($leaveTypeId == 5) { // Cuti Alasan Penting
        // Tidak berpatokan pada kuota DB. Validasi hanya batas hari per pengajuan.
        // Hakim Tinggi: maks 30 hari. Pegawai biasa: maks 10 hari.
        $jabatan = isset($_SESSION['jabatan']) ? strtolower(trim($_SESSION['jabatan'])) : '';
        $isHakimTinggi = (strpos($jabatan, 'hakim tinggi') !== false);
        $maxDays = $isHakimTinggi ? 30 : 10;
        if ($jumlahHari > $maxDays) {
            $tipe = $isHakimTinggi ? 'Hakim Tinggi' : 'Pegawai';
            return [
                'valid' => false,
                'message' => "Cuti Alasan Penting untuk {$tipe} maksimal {$maxDays} hari per sekali mengajukan. Jumlah hari yang diajukan: {$jumlahHari} hari"
            ];
        }
        return ['valid' => true, 'message' => 'Cuti Alasan Penting valid'];
    }
    
    // Untuk cuti lainnya, validasi sisa kuota
    if ($sisaKuota < $jumlahHari) {
        return [
            'valid' => false, 
            'message' => "Kuota cuti tidak mencukupi. Sisa: {$sisaKuota} hari, Dibutuhkan: {$jumlahHari} hari"
        ];
    }
    
    return ['valid' => true, 'message' => 'Kuota cuti mencukupi'];
}

/**
 * Generate catatan cuti otomatis berdasarkan satker, jenis cuti, dan tanggal
 * 
 * @param int $satker_id ID satuan kerja
 * @param int $leave_type_id ID jenis cuti
 * @param string $tanggal Tanggal target (format: Y-m-d), default: hari ini
 * @return string Catatan cuti yang sudah diformat
 */
function generateCatatanCuti($satker_id, $leave_type_id = 1, $tanggal = null) {
    try {
        // Default tanggal ke hari ini
        if ($tanggal === null) {
            $tanggal = date('Y-m-d');
        }
        
        $db = Database::getInstance();
        
        // Query untuk mendapatkan nama satker
        $sqlSatker = "SELECT nama_satker FROM satker WHERE id_satker = ?";
        $stmtSatker = $db->query($sqlSatker, [$satker_id]);
        $resultSatker = $stmtSatker->get_result()->fetch_assoc();
        $nama_satker = $resultSatker ? $resultSatker['nama_satker'] : 'Satuan Kerja';
        
        // Query untuk mendapatkan nama jenis cuti
        $sqlLeaveType = "SELECT nama_cuti FROM leave_types WHERE id = ?";
        $stmtLeaveType = $db->query($sqlLeaveType, [$leave_type_id]);
        $resultLeaveType = $stmtLeaveType->get_result()->fetch_assoc();
        $nama_cuti = $resultLeaveType ? $resultLeaveType['nama_cuti'] : 'Cuti';
        
        // Query untuk mendapatkan jumlah pegawai (termasuk atasan) yang sedang
        // menjalankan cuti dengan status 'approved' pada tanggal tersebut.
        // Status 'approved' menandakan cuti telah disetujui dan sedang berjalan.
        $sqlJumlahCuti = "
            SELECT COUNT(DISTINCT lr.user_id) AS jumlah_cuti
            FROM leave_requests lr
            INNER JOIN users u ON lr.user_id = u.id
            WHERE u.unit_kerja = ?
              AND lr.leave_type_id = ?
              AND lr.status = 'approved'
              AND ? BETWEEN lr.tanggal_mulai AND lr.tanggal_selesai
              AND u.user_type IN ('pegawai', 'atasan')
              AND u.is_deleted = 0
        ";
        $stmtJumlahCuti = $db->query($sqlJumlahCuti, [$satker_id, $leave_type_id, $tanggal]);
        $resultJumlahCuti = $stmtJumlahCuti->get_result()->fetch_assoc();
        $jumlah_cuti = $resultJumlahCuti ? (int)$resultJumlahCuti['jumlah_cuti'] : 0;
        
        // Query untuk mendapatkan total pegawai di satker.
        // Mencakup user_type 'pegawai' DAN 'atasan', karena atasan juga merupakan
        // pegawai yang dapat mengajukan cuti di satker tersebut.
        $sqlTotalPegawai = "
            SELECT COUNT(*) AS total_pegawai
            FROM users
            WHERE unit_kerja = ?
              AND user_type IN ('pegawai', 'atasan')
              AND is_deleted = 0
        ";
        $stmtTotalPegawai = $db->query($sqlTotalPegawai, [$satker_id]);
        $resultTotalPegawai = $stmtTotalPegawai->get_result()->fetch_assoc();
        $total_pegawai = $resultTotalPegawai ? (int)$resultTotalPegawai['total_pegawai'] : 0;
        
        // Hitung persentase
        $persentase = 0;
        if ($total_pegawai > 0) {
            $persentase = round(($jumlah_cuti / $total_pegawai) * 100, 1);
        }
        
        // Konversi persentase ke format Indonesia (gunakan koma sebagai separator desimal)
        $persentase_formatted = number_format($persentase, 1, ',', '.');
        
        // Format catatan:
        // %d pertama  = jumlah pegawai/atasan yang cutinya 'approved' pada tanggal dipilih
        // %d terakhir = total pegawai + atasan di satker (karena atasan dapat mengajukan cuti)
        $catatan = sprintf(
            "Pada %s Terdapat %d Orang yang Menjalankan %s, Dengan Persentase %s %% Dari Jumlah Pegawai %s Yaitu Sebanyak %d Orang Pegawai",
            $nama_satker,
            $jumlah_cuti,
            $nama_cuti,
            $persentase_formatted,
            $nama_satker,
            $total_pegawai
        );
        
        return $catatan;
        
    } catch (Exception $e) {
        // Jika terjadi error, return catatan kosong atau default
        return '';
    }
}

/**
 * Hitung persentase pegawai yang sedang menjalankan cuti tahunan (approved)
 * pada tanggal dan satker tertentu.
 *
 * @param int|string $satker_id  ID satuan kerja (unit_kerja dari session)
 * @param string     $tanggal    Tanggal yang dicek (format: Y-m-d)
 * @return array ['persentase' => float, 'jumlah_cuti' => int, 'total_pegawai' => int]
 */
function getCutiTahunanPercentage($satker_id, $tanggal) {
    try {
        $db = Database::getInstance();

        // Jumlah pegawai yang sedang menjalankan cuti tahunan (leave_type_id=1, status='approved')
        $sqlCuti = "
            SELECT COUNT(DISTINCT lr.user_id) AS jumlah_cuti
            FROM leave_requests lr
            INNER JOIN users u ON lr.user_id = u.id
            WHERE u.unit_kerja = ?
              AND lr.leave_type_id = 1
              AND lr.status = 'approved'
              AND ? BETWEEN lr.tanggal_mulai AND lr.tanggal_selesai
              AND u.user_type IN ('pegawai', 'atasan')
              AND u.is_deleted = 0
        ";
        $resCuti = $db->query($sqlCuti, [$satker_id, $tanggal])->get_result()->fetch_assoc();
        $jumlah_cuti = $resCuti ? (int)$resCuti['jumlah_cuti'] : 0;

        // Total pegawai aktif di satker
        $sqlTotal = "
            SELECT COUNT(*) AS total_pegawai
            FROM users
            WHERE unit_kerja = ?
              AND user_type IN ('pegawai', 'atasan')
              AND is_deleted = 0
        ";
        $resTotal = $db->query($sqlTotal, [$satker_id])->get_result()->fetch_assoc();
        $total_pegawai = $resTotal ? (int)$resTotal['total_pegawai'] : 0;

        $persentase = 0;
        if ($total_pegawai > 0) {
            $persentase = round(($jumlah_cuti / $total_pegawai) * 100, 2);
        }

        return [
            'persentase'    => $persentase,
            'jumlah_cuti'   => $jumlah_cuti,
            'total_pegawai' => $total_pegawai,
        ];
    } catch (Exception $e) {
        return [
            'persentase'    => 0,
            'jumlah_cuti'   => 0,
            'total_pegawai' => 0,
        ];
    }
}