<?php
/**
 * Helper untuk mengelola kuota cuti otomatis saat pergantian tahun
 * Logika khusus untuk kuota cuti tahunan (leave_type_id = 1)
 */

require_once dirname(__DIR__) . '/../config/database.php';
require_once dirname(__DIR__) . '/core/Database.php';

/**
 * Menjalankan proses otomatis pengelolaan kuota cuti tahunan
 * Dipanggil setiap 1 Januari
 */
function runAnnualQuotaManagement() {
    $db = Database::getInstance();
    $db->beginTransaction();
    
    try {
        $currentYear = date('Y');
        $previousYear = $currentYear - 1;
        $twoYearsAgo = $currentYear - 2;
        $threeYearsAgo = $currentYear - 3;
        
        // 1. Hapus kuota 3 tahun lalu dan ke atasnya
        deleteOldQuotas($threeYearsAgo);
        
        // 2. Simpan kuota 2 tahun lalu dengan 0 hari
        saveQuotaTwoYearsAgo($twoYearsAgo);
        
        // 3. Simpan kuota 1 tahun lalu sesuai sisa (maksimal 6 hari)
        saveQuotaPreviousYear($previousYear);
        
        // 4. Buat kuota tahun baru dengan 12 hari
        createNewYearQuota($currentYear);
        
        $db->commit();
        return [
            'success' => true,
            'message' => 'Pengelolaan kuota cuti tahunan berhasil dilakukan',
            'year' => $currentYear
        ];
        
    } catch (Exception $e) {
        $db->rollback();
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

/**
 * Hapus kuota cuti tahunan yang sudah 3 tahun lalu dan ke atasnya
 */
function deleteOldQuotas($cutoffYear) {
    $db = Database::getInstance();
    
    $sql = "DELETE FROM leave_balances WHERE tahun <= ? AND leave_type_id = 1";
    $db->execute($sql, [$cutoffYear]);
    
    return true;
}

/**
 * Simpan kuota 2 tahun lalu dengan 0 hari
 */
function saveQuotaTwoYearsAgo($year) {
    $db = Database::getInstance();
    
    // Ambil semua user yang aktif
        $users = $db->fetchAll("SELECT id FROM users WHERE user_type IN ('pegawai', 'atasan') AND is_deleted = 0");
    
    foreach ($users as $user) {
        // Cek apakah sudah ada kuota untuk tahun tersebut
        $existing = $db->fetch(
            "SELECT id FROM leave_balances WHERE user_id = ? AND tahun = ? AND leave_type_id = 1",
            [$user['id'], $year]
        );
        
        if (!$existing) {
            // Buat kuota baru dengan 0 hari
            $db->execute(
                "INSERT INTO leave_balances (user_id, leave_type_id, tahun, kuota_tahunan, sisa_kuota) VALUES (?, 1, ?, 0, 0)",
                [$user['id'], $year]
            );
        } else {
            // Update kuota yang ada menjadi 0
            $db->execute(
                "UPDATE leave_balances SET kuota_tahunan = 0, sisa_kuota = 0 WHERE user_id = ? AND tahun = ? AND leave_type_id = 1",
                [$user['id'], $year]
            );
        }
    }
    
    return true;
}

/**
 * Simpan kuota 1 tahun lalu sesuai sisa (maksimal 6 hari)
 */
function saveQuotaPreviousYear($year) {
    $db = Database::getInstance();
    
    // Ambil semua user yang aktif
        $users = $db->fetchAll("SELECT id FROM users WHERE user_type IN ('pegawai', 'atasan') AND is_deleted = 0");
    
    foreach ($users as $user) {
        // Ambil sisa kuota tahun sebelumnya
        $currentBalance = $db->fetch(
            "SELECT sisa_kuota FROM leave_balances WHERE user_id = ? AND tahun = ? AND leave_type_id = 1",
            [$user['id'], $year]
        );
        
        $sisaKuota = $currentBalance ? $currentBalance['sisa_kuota'] : 0;
        
        // Tentukan kuota yang dapat dibawa ke tahun berikutnya (maksimal 6 hari)
        $kuotaYangDibawa = min($sisaKuota, 6);
        
        // Cek apakah sudah ada kuota untuk tahun tersebut
        $existing = $db->fetch(
            "SELECT id FROM leave_balances WHERE user_id = ? AND tahun = ? AND leave_type_id = 1",
            [$user['id'], $year]
        );
        
        if (!$existing) {
            // Buat kuota baru
            $db->execute(
                "INSERT INTO leave_balances (user_id, leave_type_id, tahun, kuota_tahunan, sisa_kuota) VALUES (?, 1, ?, ?, ?)",
                [$user['id'], $year, $kuotaYangDibawa, $kuotaYangDibawa]
            );
        } else {
            // Update kuota yang ada
            $db->execute(
                "UPDATE leave_balances SET kuota_tahunan = ?, sisa_kuota = ? WHERE user_id = ? AND tahun = ? AND leave_type_id = 1",
                [$kuotaYangDibawa, $kuotaYangDibawa, $user['id'], $year]
            );
        }
    }
    
    return true;
}

/**
 * Buat kuota tahun baru dengan 12 hari
 */
function createNewYearQuota($year) {
    $db = Database::getInstance();
    
    // Ambil semua user yang aktif
        $users = $db->fetchAll("SELECT id FROM users WHERE user_type IN ('pegawai', 'atasan') AND is_deleted = 0");
    
    foreach ($users as $user) {
        // Cek apakah sudah ada kuota untuk tahun tersebut
        $existing = $db->fetch(
            "SELECT id FROM leave_balances WHERE user_id = ? AND tahun = ? AND leave_type_id = 1",
            [$user['id'], $year]
        );
        
        if (!$existing) {
            // Buat kuota baru dengan 12 hari
            $db->execute(
                "INSERT INTO leave_balances (user_id, leave_type_id, tahun, kuota_tahunan, sisa_kuota) VALUES (?, 1, ?, 12, 12)",
                [$user['id'], $year]
            );
        } else {
            // Update kuota yang ada menjadi 12 hari
            $db->execute(
                "UPDATE leave_balances SET kuota_tahunan = 12, sisa_kuota = 12 WHERE user_id = ? AND tahun = ? AND leave_type_id = 1",
                [$user['id'], $year]
            );
        }
    }
    
    return true;
}

/**
 * Mendapatkan total kuota cuti tahunan untuk user dalam 3 tahun terakhir
 */
function getTotalQuotaLastThreeYears($userId) {
    $db = Database::getInstance();
    $currentYear = date('Y');
    
    $sql = "SELECT tahun, kuota_tahunan, sisa_kuota 
            FROM leave_balances 
            WHERE user_id = ? AND leave_type_id = 1 
            AND tahun >= ? - 2 AND tahun <= ?
            ORDER BY tahun ASC";
    
    $balances = $db->fetchAll($sql, [$userId, $currentYear, $currentYear]);
    
    $totalKuota = 0;
    $totalSisa = 0;
    $yearlyData = [];
    
    foreach ($balances as $balance) {
        $totalKuota += $balance['kuota_tahunan'];
        $totalSisa += $balance['sisa_kuota'];
        $yearlyData[$balance['tahun']] = [
            'kuota_tahunan' => $balance['kuota_tahunan'],
            'sisa_kuota' => $balance['sisa_kuota'],
            'terpakai' => $balance['kuota_tahunan'] - $balance['sisa_kuota']
        ];
    }
    
    return [
        'total_kuota' => $totalKuota,
        'total_sisa' => $totalSisa,
        'total_terpakai' => $totalKuota - $totalSisa,
        'yearly_data' => $yearlyData,
        'max_quota' => 18 // Maksimal 18 hari dalam 3 tahun
    ];
}

/**
 * Mendapatkan daftar user dengan total kuota 3 tahun terakhir
 */
function getUsersWithThreeYearQuota() {
    $db = Database::getInstance();
    $currentYear = date('Y');
    
    $sql = "SELECT u.id, u.nama, u.nip, u.unit_kerja,
                   COALESCE(SUM(lb.kuota_tahunan), 0) as total_kuota,
                   COALESCE(SUM(lb.sisa_kuota), 0) as total_sisa,
                   COALESCE(SUM(lb.kuota_tahunan - lb.sisa_kuota), 0) as total_terpakai
            FROM users u
            LEFT JOIN leave_balances lb ON u.id = lb.user_id 
                AND lb.leave_type_id = 1 
                AND lb.tahun >= ? - 2 
                AND lb.tahun <= ?
            WHERE u.user_type IN ('pegawai', 'atasan') AND u.is_deleted = 0
            GROUP BY u.id, u.nama, u.nip, u.unit_kerja
            ORDER BY u.nama";
    
    $users = $db->fetchAll($sql, [$currentYear, $currentYear]);
    
    foreach ($users as &$user) {
        $user['max_quota'] = 18;
        $user['persentase_terpakai'] = $user['total_kuota'] > 0 ? 
            round(($user['total_terpakai'] / $user['total_kuota']) * 100) : 0;
    }
    
    return $users;
}

/**
 * Log aktivitas pengelolaan kuota otomatis
 */
function logQuotaManagement($action, $details = []) {
    // Sementara disable logging untuk testing
    return true;
}

/**
 * Cek apakah proses otomatis sudah dijalankan hari ini
 */
function isQuotaManagementAlreadyRun() {
    // Sementara disable check untuk testing
    return false;
} 