<?php
/**
 * Script Cron Job untuk Pengelolaan Kuota Cuti Otomatis
 * Jalankan setiap 1 Januari jam 00:01
 * 
 * Cara setup cron job:
 * 1 0 1 1 * /usr/bin/php /path/to/sistem-cuti/cron/annual_quota_management.php
 */

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Include database config first
require_once dirname(__DIR__) . '/config/database.php';

// Include bootstrap
require_once dirname(__DIR__) . '/app/core/bootstrap.php';

// Include helper yang sudah diperbaiki
require_once dirname(__DIR__) . '/app/helpers/quota_scheduler_helper_fixed.php';

// Log file
$logFile = dirname(__DIR__) . '/logs/quota_management_' . date('Y-m-d') . '.log';

function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    
    // Write to log file
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    
    // Also output to console if running from command line
    if (php_sapi_name() === 'cli') {
        echo $logMessage;
    }
}

try {
    writeLog("=== MULAI PENGELOLAAN KUOTA CUTI OTOMATIS ===");
    
    // Cek apakah sudah dijalankan hari ini
    if (isQuotaManagementAlreadyRun()) {
        writeLog("Proses pengelolaan kuota sudah dijalankan hari ini. Skipping...");
        exit(0);
    }
    
    // Cek apakah hari ini adalah 1 Januari
    $today = date('Y-m-d');
    $isNewYear = date('m-d') === '01-01';
    
    if (!$isNewYear) {
        writeLog("Hari ini bukan 1 Januari. Skipping...");
        exit(0);
    }
    
    writeLog("Memulai proses pengelolaan kuota cuti tahunan...");
    
    // Jalankan proses pengelolaan kuota
    $result = runAnnualQuotaManagement();
    
    if ($result['success']) {
        writeLog("SUKSES: " . $result['message']);
        writeLog("Tahun: " . $result['year']);
        
        // Log aktivitas
        logQuotaManagement('annual_quota_management', [
            'year' => $result['year'],
            'status' => 'success',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
        writeLog("=== SELESAI PENGELOLAAN KUOTA CUTI OTOMATIS ===");
        exit(0);
        
    } else {
        writeLog("ERROR: " . $result['message']);
        
        // Log error
        logQuotaManagement('annual_quota_management_error', [
            'error' => $result['message'],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
        writeLog("=== GAGAL PENGELOLAAN KUOTA CUTI OTOMATIS ===");
        exit(1);
    }
    
} catch (Exception $e) {
    writeLog("FATAL ERROR: " . $e->getMessage());
    writeLog("Stack trace: " . $e->getTraceAsString());
    
    // Log fatal error
    logQuotaManagement('annual_quota_management_fatal_error', [
        'error' => $e->getMessage(),
        'stack_trace' => $e->getTraceAsString(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
    writeLog("=== GAGAL PENGELOLAAN KUOTA CUTI OTOMATIS ===");
    exit(1);
} 