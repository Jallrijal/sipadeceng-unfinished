<?php
class LeaveBalance extends Model {
    protected $table = 'leave_balances';
    protected $fillable = ['user_id', 'tahun', 'kuota_tahunan', 'sisa_kuota'];
    
    public function getBalance($userId, $tahun) {
        $balance = $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE user_id = ? AND tahun = ?",
            [$userId, $tahun]
        );
        
        if (!$balance) {
            // Create default balance if not exists
            $this->create([
                'user_id' => $userId,
                'tahun' => $tahun,
                'kuota_tahunan' => 12,
                'sisa_kuota' => 12
            ]);
            
            return [
                'user_id' => $userId,
                'tahun' => $tahun,
                'kuota_tahunan' => 12,
                'sisa_kuota' => 12
            ];
        }
        
        return $balance;
    }

    /**
     * Fetch balance row without creating default record.
     * Returns null if not found.
     */
    public function getBalanceRaw($userId, $tahun) {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE user_id = ? AND tahun = ?",
            [$userId, $tahun]
        );
    }
    
    public function getTotalBalance($userId, $tahun) {
        $totalSisa = 0;
        // Determine target year and compute the three-year window: tahun-2, tahun-1, tahun
        if (!$tahun) {
            $tahun = date('Y');
        }
        $tahun = (int) $tahun;
        $years = [$tahun - 2, $tahun - 1, $tahun];
        foreach ($years as $year) {
            $balance = $this->getBalance($userId, $year);
            if (isset($balance['sisa_kuota'])) {
                $totalSisa += $balance['sisa_kuota'];
            }
        }
        
        return $totalSisa;
    }
    
    public function updateBalance($userId, $tahun, $sisaKuota) {
        return $this->db->query(
            "UPDATE {$this->table} SET sisa_kuota = ? WHERE user_id = ? AND tahun = ?",
            [$sisaKuota, $userId, $tahun]
        );
    }
    
    public function deductQuota($userId, $tahunCuti, $jumlahHari) {
        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            // Urutkan tahun: 2 tahun sebelum, 1 tahun sebelum, tahun pengajuan
            $tahunInt = (int)$tahunCuti;
            $years = [$tahunInt - 2, $tahunInt - 1, $tahunInt];
            $balances = [];
            foreach ($years as $year) {
                $balance = $this->getBalance($userId, $year);
                $balances[$year] = $balance['sisa_kuota'] ?? 0;
            }
            $remainingDeduction = $jumlahHari;
            foreach ($years as $year) {
                if ($remainingDeduction > 0 && $balances[$year] > 0) {
                    if ($balances[$year] >= $remainingDeduction) {
                        $balances[$year] -= $remainingDeduction;
                        $remainingDeduction = 0;
                    } else {
                        $remainingDeduction -= $balances[$year];
                        $balances[$year] = 0;
                    }
                    $this->updateBalance($userId, $year, $balances[$year]);
                }
            }
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }
    
    public function restoreQuota($userId, $tahunCuti, $jumlahHari) {
        // This is simplified - in real implementation, you'd need to track
        // which years were deducted from to restore properly
        $balance = $this->getBalance($userId, $tahunCuti);
        $newBalance = $balance['sisa_kuota'] + $jumlahHari;
        
        // Make sure not to exceed annual quota
        if ($newBalance > $balance['kuota_tahunan']) {
            $newBalance = $balance['kuota_tahunan'];
        }
        
        return $this->updateBalance($userId, $tahunCuti, $newBalance);
    }
    
    public function getUsersWithBalance($tahun, $leaveTypeId = 1) {
        if (!$tahun) {
            $tahun = date('Y');
        }
    $sql = "SELECT 
        u.id, u.nama, u.nip, u.jabatan, u.unit_kerja, u.golongan
        FROM users u
        WHERE u.user_type IN ('pegawai', 'atasan')
        ORDER BY u.unit_kerja, u.nama";
        
    require_once __DIR__ . '/../helpers/satker_helper.php';
    $users = $this->db->fetchAll($sql);
    $result = [];
    foreach ($users as $user) {
        $userData = $user;
        $userData['nama_satker'] = get_nama_satker($user['unit_kerja']);
            if ($leaveTypeId == 1) { // Cuti Tahunan
                // Calculate total balance from three-year window relative to $tahun
                $totalSisa = 0;
                $detailPerTahun = [];
                $tahun = (int)$tahun;
                $years = [$tahun - 2, $tahun - 1, $tahun];
                foreach ($years as $tahunCek) {
                    $balance = $this->getBalance($user['id'], $tahunCek);
                    $sisaKuota = $balance['sisa_kuota'] ?? 0;
                    $totalSisa += $sisaKuota;
                    $detailPerTahun[] = $tahunCek . ': ' . $sisaKuota;
                }
                $totalKuota = 36; // 3 years x 12 days
                $cutiTerpakai = $totalKuota - $totalSisa;
                $userData['kuota_tahunan'] = $totalKuota;
                $userData['sisa_kuota'] = $totalSisa;
                $userData['cuti_terpakai'] = $cutiTerpakai;
                $userData['persentase_terpakai'] = $totalKuota > 0 ? 
                    round(($cutiTerpakai / $totalKuota) * 100) : 0;
                $userData['detail_sisa'] = implode(', ', $detailPerTahun);
            } else if ($leaveTypeId == 2) { // Cuti Besar
                $sqlBesar = "SELECT kuota_total, sisa_kuota FROM kuota_cuti_besar WHERE user_id = ?";
                $besar = $this->db->fetch($sqlBesar, [$user['id']]);
                $kuota = $besar['kuota_total'] ?? 90;
                $sisa = $besar['sisa_kuota'] ?? 90;
                $userData['kuota_tahunan'] = $kuota;
                $userData['sisa_kuota'] = $sisa;
                $userData['cuti_terpakai'] = $kuota - $sisa;
                $userData['persentase_terpakai'] = $kuota > 0 ? round((($kuota - $sisa) / $kuota) * 100) : 0;
                $userData['detail_sisa'] = 'Cuti Besar: ' . $sisa;
            } else if ($leaveTypeId == 3) { // Cuti Sakit
                // Ambil data kuota_cuti_sakit dari DB; hanya tampilkan baris yang ada di database
                $sqlSakit = "SELECT tahun, kuota_tahunan, sisa_kuota FROM kuota_cuti_sakit WHERE user_id = ? ORDER BY tahun DESC";
                $rows = $this->db->fetchAll($sqlSakit, [$user['id']]);
                $kuota = 0; $sisa = 0; $detail = [];
                $foundForYear = false;
                foreach ($rows as $row) {
                    // Tambahkan hanya data yang ada di DB
                    $detail[] = $row['tahun'] . ': ' . $row['sisa_kuota'];
                    if ($row['tahun'] == $tahun) {
                        $kuota = $row['kuota_tahunan'];
                        $sisa = $row['sisa_kuota'];
                        $foundForYear = true;
                    }
                }
                // Jika tidak ada baris khusus untuk tahun yang dipilih, tetap gunakan nilai 0
                $userData['kuota_tahunan'] = $kuota;
                $userData['sisa_kuota'] = $sisa;
                $userData['cuti_terpakai'] = ($kuota - $sisa);
                $userData['persentase_terpakai'] = $kuota > 0 ? round((($kuota - $sisa) / $kuota) * 100) : 0;
                // Jika tidak ada satupun baris di DB, tampilkan teks penanda
                $userData['detail_sisa'] = !empty($detail) ? implode(', ', $detail) : 'Tidak ada data';
            } else if ($leaveTypeId == 4) { // Cuti Melahirkan - bukan akumulatif
                $sqlMelahirkan = "SELECT sisa_pengambilan, jumlah_pengambilan FROM kuota_cuti_melahirkan WHERE user_id = ? LIMIT 1";
                $row = $this->db->fetch($sqlMelahirkan, [$user['id']]);
                $sisaPengambilan = $row ? (int)$row['sisa_pengambilan'] : 1;
                $jumlahPengambilan = $row ? (int)$row['jumlah_pengambilan'] : 0;
                $userData['kuota_tahunan'] = 90;
                $userData['sisa_kuota'] = 90;
                $userData['cuti_terpakai'] = $jumlahPengambilan;
                $userData['persentase_terpakai'] = $jumlahPengambilan > 0 ? round(($jumlahPengambilan / 3) * 100) : 0;
                $userData['detail_sisa'] = 'Cuti Melahirkan: 90 (maksimal per sekali mengajukan)';
                $userData['sisa_pengambilan'] = $sisaPengambilan;
            } else if ($leaveTypeId == 5) { // Cuti Alasan Penting - bukan akumulatif
                $userData['kuota_tahunan'] = 30;
                $userData['sisa_kuota'] = 30;
                $userData['cuti_terpakai'] = 0;
                $userData['persentase_terpakai'] = 0;
                $userData['detail_sisa'] = 'Cuti Alasan Penting: 30 (maksimal per sekali mengajukan)';
            } else if ($leaveTypeId == 6) { // Cuti Luar Tanggungan
                $sqlLuar = "SELECT tahun, kuota_tahunan, sisa_kuota FROM kuota_cuti_luar_tanggungan WHERE user_id = ? ORDER BY tahun DESC";
                $rows = $this->db->fetchAll($sqlLuar, [$user['id']]);
                $kuota = null; $sisa = null; $detail = [];
                foreach ($rows as $row) {
                    if ($row['tahun'] == $tahun) {
                        $kuota = $row['kuota_tahunan'];
                        $sisa = $row['sisa_kuota'];
                    }
                    $detail[] = $row['tahun'] . ': ' . $row['sisa_kuota'];
                }
                if ($kuota === null) { $kuota = 365; $sisa = 365; $detail[] = $tahun . ': 365'; }
                $userData['kuota_tahunan'] = $kuota;
                $userData['sisa_kuota'] = $sisa;
                $userData['cuti_terpakai'] = $kuota - $sisa;
                $userData['persentase_terpakai'] = $kuota > 0 ? round((($kuota - $sisa) / $kuota) * 100) : 0;
                $userData['detail_sisa'] = implode(', ', $detail);
            }
            $result[] = $userData;
        }
        return $result;
    }

    // Tambahkan method ini di class LeaveBalance

    public function simulateQuotaDeduction($userId, $tahunCuti, $jumlahHari) {
        $result = [];

        // Compute the three-year window relative to the requested cuti year
        $tahunCuti = (int)$tahunCuti;
        $years = [$tahunCuti - 2, $tahunCuti - 1, $tahunCuti];

        $balances = [];
        foreach ($years as $year) {
            $balance = $this->getBalance($userId, $year);
            $balances[$year] = [
                'sisa_awal' => $balance['sisa_kuota'] ?? 0,
                'dipotong' => 0,
                'sisa_akhir' => $balance['sisa_kuota'] ?? 0
            ];
        }

        // Simulate deduction with priority: (tahun-2) -> (tahun-1) -> tahun
        $remainingDeduction = $jumlahHari;

        foreach ($years as $year) {
            if ($remainingDeduction > 0 && $balances[$year]['sisa_awal'] > 0) {
                $deduction = min($remainingDeduction, $balances[$year]['sisa_awal']);
                $balances[$year]['dipotong'] = $deduction;
                $balances[$year]['sisa_akhir'] = $balances[$year]['sisa_awal'] - $deduction;
                $remainingDeduction -= $deduction;
            }
        }

        return $balances;
    }

    public function getQuotaHistory($userId) {
        $sql = "SELECT tahun, kuota_tahunan, sisa_kuota 
                FROM {$this->table} 
                WHERE user_id = ? 
                ORDER BY tahun DESC";
        
        $balances = $this->db->fetchAll($sql, [$userId]);
        
        foreach ($balances as &$balance) {
            $balance['persentase_terpakai'] = $balance['kuota_tahunan'] > 0 ? 
                round((($balance['kuota_tahunan'] - $balance['sisa_kuota']) / $balance['kuota_tahunan']) * 100) : 0;
        }
        
        return $balances;
    }
    
    /**
     * Get sisa kuota cuti sakit untuk user tertentu
     */
    public function getSisaKuotaSakit($userId, $tahun = null) {
        if (!$tahun) {
            $tahun = date('Y');
        }
        
        $sql = "SELECT sisa_kuota FROM kuota_cuti_sakit WHERE user_id = ? AND tahun = ?";
        $result = $this->db->fetch($sql, [$userId, $tahun]);
        
        return $result ? $result['sisa_kuota'] : 0;
    }
    
    /**
     * Get sisa kuota cuti besar untuk user tertentu
     */
    public function getSisaKuotaBesar($userId) {
        $sql = "SELECT sisa_kuota FROM kuota_cuti_besar WHERE user_id = ?";
        $result = $this->db->fetch($sql, [$userId]);
        
        return $result ? $result['sisa_kuota'] : 0;
    }
    
    /**
     * Get sisa kuota cuti melahirkan untuk user tertentu
     */
    public function getSisaKuotaMelahirkan($userId) {
        // Cuti melahirkan selalu 90 hari karena bukan akumulatif
        return 90;
    }
    
    /**
     * Get sisa kuota cuti alasan penting untuk user tertentu
     */
    public function getSisaKuotaAlasanPenting($userId, $tahun = null) {
        // Cuti alasan penting selalu 30 hari karena bukan akumulatif
        return 30;
    }
    
    /**
     * Get sisa kuota cuti luar tanggungan untuk user tertentu
     */
    public function getSisaKuotaLuarTanggungan($userId, $tahun = null) {
        if (!$tahun) {
            $tahun = date('Y');
        }
        
        $sql = "SELECT sisa_kuota FROM kuota_cuti_luar_tanggungan WHERE user_id = ? AND tahun = ?";
        $result = $this->db->fetch($sql, [$userId, $tahun]);
        
        return $result ? $result['sisa_kuota'] : 0;
    }

    /**
     * Get jumlah pengambilan cuti melahirkan untuk user tertentu
     */
    public function getJumlahPengambilanMelahirkan($userId) {
        $sql = "SELECT jumlah_pengambilan FROM kuota_cuti_melahirkan WHERE user_id = ?";
        $result = $this->db->fetch($sql, [$userId]);
        return $result ? (int)$result['jumlah_pengambilan'] : 0;
    }

    /**
     * Mendapatkan total kuota cuti tahunan untuk user dalam 3 tahun terakhir
     */
    public function getTotalQuotaLastThreeYears($userId) {
        $currentYear = date('Y');
        
        $sql = "SELECT tahun, kuota_tahunan, sisa_kuota 
            FROM {$this->table} 
            WHERE user_id = ? 
            AND tahun >= ? - 2 AND tahun <= ?
                ORDER BY tahun ASC";
        
        $balances = $this->db->fetchAll($sql, [$userId, $currentYear, $currentYear]);
        
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
    public function getUsersWithThreeYearQuota() {
        $currentYear = date('Y');
        
        $sql = "SELECT u.id, u.nama, u.nip, u.unit_kerja,
                       COALESCE(SUM(lb.kuota_tahunan), 0) as total_kuota,
                       COALESCE(SUM(lb.sisa_kuota), 0) as total_sisa,
                       COALESCE(SUM(lb.kuota_tahunan - lb.sisa_kuota), 0) as total_terpakai
                FROM users u
                LEFT JOIN {$this->table} lb ON u.id = lb.user_id 
                    AND lb.tahun >= ? - 2 
                    AND lb.tahun <= ?
                WHERE u.user_type IN ('pegawai', 'atasan') AND u.is_deleted = 0
                GROUP BY u.id, u.nama, u.nip, u.unit_kerja
                ORDER BY u.nama";
        
        $users = $this->db->fetchAll($sql, [$currentYear, $currentYear]);
        
        foreach ($users as &$user) {
            $user['max_quota'] = 18;
            $user['persentase_terpakai'] = $user['total_kuota'] > 0 ? 
                round(($user['total_terpakai'] / $user['total_kuota']) * 100) : 0;
        }
        
        return $users;
    }

    /**
     * Hapus kuota cuti tahunan yang sudah 3 tahun lalu dan ke atasnya
     */
    public function deleteOldQuotas($cutoffYear) {
        $sql = "DELETE FROM {$this->table} WHERE tahun <= ?";
        return $this->db->execute($sql, [$cutoffYear]);
    }

    /**
     * Buat atau update kuota untuk tahun tertentu
     */
    public function createOrUpdateQuota($userId, $year, $kuotaTahunan, $sisaKuota) {
        $existing = $this->db->fetch(
            "SELECT id FROM {$this->table} WHERE user_id = ? AND tahun = ?",
            [$userId, $year]
        );

        if (!$existing) {
            $sql = "INSERT INTO {$this->table} (user_id, tahun, kuota_tahunan, sisa_kuota) VALUES (?, ?, ?, ?)";
            return $this->db->execute($sql, [$userId, $year, $kuotaTahunan, $sisaKuota]);
        } else {
            $sql = "UPDATE {$this->table} SET kuota_tahunan = ?, sisa_kuota = ? WHERE user_id = ? AND tahun = ?";
            return $this->db->execute($sql, [$kuotaTahunan, $sisaKuota, $userId, $year]);
        }
    }

    /**
     * Jalankan pengelolaan kuota tahunan otomatis.
     * - Menghapus kuota <= (targetYear - 3)
     * - Memindahkan kuota tahun lalu -> dua tahun lalu (sisa => 0)
     * - Memindahkan kuota tahun ini -> tahun lalu (sisa => min(sisa,6))
     * - Membuat kuota untuk targetYear dengan 12 hari
     *
     * @param int|null $targetYear If null, uses current year (date('Y')).
     * @return bool
     */
    public function runQuotaManagement($targetYear = null) {
        // Default target year is current year (create kuota for this year rollover)
        if (!$targetYear) {
            $targetYear = (int) date('Y');
        }
        // Prepare logging/backup
        $root = dirname(__DIR__, 2);
        $logDir = $root . '/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $timestamp = date('Ymd_His');
        $backupFile = $logDir . '/leave_balances_backup_' . $timestamp . '.json';

        // Backup full table to JSON before making changes
        try {
            $allRows = $this->db->fetchAll("SELECT * FROM {$this->table}");
            @file_put_contents($backupFile, json_encode($allRows, JSON_PRETTY_PRINT));
        } catch (Exception $e) {
            // Ignore backup errors but record later in log
            $backupFile = null;
        }

        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $cutoff = $targetYear - 3; // delete <= cutoff (3 years ago and earlier)
            $this->deleteOldQuotas($cutoff);

            $users = $this->db->fetchAll("SELECT id FROM users WHERE user_type IN ('pegawai', 'atasan') AND is_deleted = 0");

            // Define years explicitly to avoid confusion
            $year_delete = $targetYear - 3; // e.g., 2023 (will be removed)
            $year_two_years_ago = $targetYear - 2; // e.g., 2024 -> set sisa = 0
            $year_last = $targetYear - 1; // e.g., 2025 -> set sisa = min(sisa,6)

            $processed = 0;
            $created = 0;
            $updated = 0;

            foreach ($users as $user) {
                $uid = $user['id'];
                $processed++;

                // 1) Ensure two-years-ago ($year_two_years_ago) has sisa = 0
                $rowY2 = $this->getBalanceRaw($uid, $year_two_years_ago);
                if ($rowY2) {
                    $kuotaY2 = (int)$rowY2['kuota_tahunan'];
                    // Update existing record to have sisa = 0
                    $resY2 = $this->createOrUpdateQuota($uid, $year_two_years_ago, $kuotaY2, 0);
                    if ($resY2) $updated++;
                } else {
                    // Create with default kuota 12 and sisa 0
                    $resY2 = $this->createOrUpdateQuota($uid, $year_two_years_ago, 12, 0);
                    if ($resY2) $created++;
                }

                // 2) For last year ($year_last): set sisa = min(existing_sisa, 6)
                $rowLast = $this->getBalanceRaw($uid, $year_last);
                if ($rowLast) {
                    $kuotaLast = (int)$rowLast['kuota_tahunan'];
                    $sisaLast = (int)$rowLast['sisa_kuota'];
                    $sisaNew = $sisaLast >= 6 ? 6 : $sisaLast;
                    // Only update if changed
                    if ($sisaNew !== $sisaLast) {
                        $resLast = $this->createOrUpdateQuota($uid, $year_last, $kuotaLast, $sisaNew);
                        if ($resLast) $updated++;
                    }
                } else {
                    // If no data for last year, create default with kuota 12 and sisa 6
                    $resLast = $this->createOrUpdateQuota($uid, $year_last, 12, 6);
                    if ($resLast) $created++;
                }

                // 3) Create targetYear with kuota 12 and sisa 12 (new year)
                $rowTarget = $this->getBalanceRaw($uid, $targetYear);
                if ($rowTarget) {
                    // If exists, update to full quota
                    $resT = $this->createOrUpdateQuota($uid, $targetYear, 12, 12);
                    if ($resT) $updated++;
                } else {
                    $resT = $this->createOrUpdateQuota($uid, $targetYear, 12, 12);
                    if ($resT) $created++;
                }
            }

            $db->commit();

            // Write run log
            $logFile = $logDir . '/quota.log';
            $entry = date('Y-m-d H:i:s') . ' - runQuotaManagement year=' . $targetYear . ' processed=' . $processed . ' created=' . $created . ' updated=' . $updated;
            if ($backupFile) $entry .= ' backup=' . basename($backupFile);
            $entry .= "\n";
            @file_put_contents($logFile, $entry, FILE_APPEND);

            return [
                'success' => true,
                'processed' => $processed,
                'created' => $created,
                'updated' => $updated,
                'year' => $targetYear,
                'backup_file' => $backupFile,
                'log_file' => $logFile
            ];
        } catch (Exception $e) {
            $db->rollback();
            // Log error
            $logFile = isset($logFile) ? $logFile : ($logDir . '/quota.log');
            $errEntry = date('Y-m-d H:i:s') . ' - ERROR runQuotaManagement: ' . $e->getMessage() . "\n";
            @file_put_contents($logFile, $errEntry, FILE_APPEND);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'log_file' => $logFile,
                'backup_file' => isset($backupFile) ? $backupFile : null
            ];
        }
    }
}