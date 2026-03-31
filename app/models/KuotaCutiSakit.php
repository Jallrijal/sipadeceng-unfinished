<?php
class KuotaCutiSakit extends Model {
    protected $table = 'kuota_cuti_sakit';

    /**
     * Jalankan reset kuota cuti sakit untuk tahun target.
     * - Hapus semua data tahun sebelumnya (targetYear - 1)
     * - Tambah data untuk targetYear dengan kuota_tahunan = sisa_kuota = $defaultQuota
     * @param int|null $targetYear
     * @param int $defaultQuota
     * @return array
     */
    public function runSickQuotaManagement($targetYear = null, $defaultQuota = 14) {
        if (!$targetYear) $targetYear = (int) date('Y');
        $yearToDelete = $targetYear - 1;

        $root = dirname(__DIR__, 2);
        $logDir = $root . '/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        $timestamp = date('Ymd_His');
        $backupFile = $logDir . '/kuota_cuti_sakit_backup_' . $timestamp . '.json';

        try {
            // Backup table
            $allRows = $this->db->fetchAll("SELECT * FROM {$this->table}");
            @file_put_contents($backupFile, json_encode($allRows, JSON_PRETTY_PRINT));
        } catch (Exception $e) {
            $backupFile = null;
        }

        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            // Delete entries for previous year
            $deleted = 0;
            $delSql = "DELETE FROM {$this->table} WHERE tahun = ?";
            $deleted = $this->db->execute($delSql, [$yearToDelete]);

            // Prepare users list
            $users = $this->db->fetchAll("SELECT id FROM users WHERE user_type IN ('pegawai', 'atasan') AND is_deleted = 0");
            $processed = 0; $created = 0; $updated = 0;
            foreach ($users as $u) {
                $uid = $u['id'];
                $processed++;
                // Check existing for target year
                $exists = $this->db->fetch("SELECT id, kuota_tahunan, sisa_kuota FROM {$this->table} WHERE user_id = ? AND tahun = ?", [$uid, $targetYear]);
                if ($exists) {
                    // Update to default values
                    $res = $this->db->execute("UPDATE {$this->table} SET kuota_tahunan = ?, sisa_kuota = ?, updated_at = ? WHERE id = ?", [$defaultQuota, $defaultQuota, date('Y-m-d H:i:s'), $exists['id']]);
                    if ($res) $updated++;
                } else {
                    $res = $this->db->execute("INSERT INTO {$this->table} (user_id, leave_type_id, tahun, kuota_tahunan, sisa_kuota, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)", [$uid, 3, $targetYear, $defaultQuota, $defaultQuota, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);
                    if ($res) $created++;
                }
            }

            $db->commit();

            // Write log
            $logFile = $logDir . '/sick_quota.log';
            $entry = date('Y-m-d H:i:s') . " - runSickQuotaManagement year={$targetYear} deleted_year={$yearToDelete} processed={$processed} created={$created} updated={$updated} deleted={$deleted}\n";
            if ($backupFile) $entry .= ' backup=' . basename($backupFile) . "\n";
            @file_put_contents($logFile, $entry, FILE_APPEND);

            return [
                'success' => true,
                'processed' => $processed,
                'created' => $created,
                'updated' => $updated,
                'deleted' => $deleted,
                'year' => $targetYear,
                'deleted_year' => $yearToDelete,
                'backup_file' => $backupFile,
                'log_file' => $logFile
            ];
        } catch (Exception $e) {
            $db->rollback();
            $logFile = isset($logFile) ? $logFile : ($logDir . '/sick_quota.log');
            $errEntry = date('Y-m-d H:i:s') . ' - ERROR runSickQuotaManagement: ' . $e->getMessage() . "\n";
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
