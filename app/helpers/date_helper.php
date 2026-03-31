<?php
// Helper functions untuk tanggal

function formatTanggal($date) {
    if (empty($date)) return '-';
    
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $tanggal = date('d', strtotime($date));
    $bulan_num = date('n', strtotime($date));
    $tahun = date('Y', strtotime($date));
    
    return $tanggal . ' ' . $bulan[$bulan_num] . ' ' . $tahun;
}

function hitungHariKerja($tanggal_mulai, $tanggal_selesai) {
    $start = new DateTime($tanggal_mulai);
    $end = new DateTime($tanggal_selesai);
    $end->modify('+1 day');
    
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end);
    
    $hari_kerja = 0;
    foreach ($period as $date) {
        // 0 = Minggu, 6 = Sabtu
        if ($date->format('w') != 0 && $date->format('w') != 6) {
            $hari_kerja++;
        }
    }
    
    return $hari_kerja;
}

function hitungMasaKerja($tanggal_masuk) {
    if (empty($tanggal_masuk)) {
        return '-';
    }
    
    $start = new DateTime($tanggal_masuk);
    $end = new DateTime();
    $interval = $start->diff($end);
    
    $tahun = $interval->y;
    $bulan = $interval->m;
    
    if ($tahun > 0 && $bulan > 0) {
        return $tahun . ' Tahun ' . $bulan . ' Bulan';
    } elseif ($tahun > 0) {
        return $tahun . ' Tahun';
    } elseif ($bulan > 0) {
        return $bulan . ' Bulan';
    } else {
        return $interval->d . ' Hari';
    }
}

function getCurrentDate() {
    return date('Y-m-d');
}

function getCurrentDateTime() {
    return date('Y-m-d H:i:s');
}

function formatDateTime($datetime) {
    if (empty($datetime)) return '-';
    
    return date('d/m/Y H:i', strtotime($datetime));
}

function formatDateShort($date) {
    if (empty($date)) return '-';
    
    return date('d/m/Y', strtotime($date));
}

function getDayName($date) {
    $hari = array(
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    );
    
    $dayName = date('l', strtotime($date));
    return $hari[$dayName];
}

function isWeekend($date) {
    $dayOfWeek = date('w', strtotime($date));
    return ($dayOfWeek == 0 || $dayOfWeek == 6);
}

function formatDateTimeIndonesian($datetime) {
    if (empty($datetime)) return '-';
    
    $hari = array(
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    );
    
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $timestamp = strtotime($datetime);
    $dayName = $hari[date('l', $timestamp)];
    $tanggal = date('d', $timestamp);
    $bulan_num = date('n', $timestamp);
    $tahun = date('Y', $timestamp);
    $jam = date('H:i', $timestamp);
    
    return $dayName . ', ' . $tanggal . ' ' . $bulan[$bulan_num] . ' ' . $tahun . ' ' . $jam;
}

function addBusinessDays($startDate, $businessDays) {
    $date = new DateTime($startDate);
    $i = 0;
    
    while ($i < $businessDays) {
        $date->modify('+1 day');
        if ($date->format('N') < 6) { // 1-5 are weekdays
            $i++;
        }
    }
    
    return $date->format('Y-m-d');
}

function getMonthName($month) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    return isset($bulan[$month]) ? $bulan[$month] : '';
}

function getYearRange($startYear = null, $endYear = null) {
    if (!$startYear) $startYear = date('Y') - 5;
    if (!$endYear) $endYear = date('Y') + 1;
    
    $years = [];
    for ($i = $endYear; $i >= $startYear; $i--) {
        $years[] = $i;
    }
    
    return $years;
}

/**
 * Ekstrak tanggal masuk dari NIP
 * Format NIP: 197203031997031001
 * Digit 9-14: 199703 (tahun-bulan)
 * @param string $nip
 * @return string tanggal dalam format Y-m-d
 */
function extractTanggalMasukFromNIP($nip) {
    if (strlen($nip) < 14) {
        return '1900-01-01'; // default jika NIP tidak valid
    }
    
    $tahun = substr($nip, 8, 4); // digit 9-12
    $bulan = substr($nip, 12, 2); // digit 13-14
    
    // Validasi tahun dan bulan
    if (!is_numeric($tahun) || !is_numeric($bulan)) {
        return '1900-01-01';
    }
    
    if ($bulan < 1 || $bulan > 12) {
        return '1900-01-01';
    }
    
    return sprintf('%04d-%02d-01', $tahun, $bulan);
}

/**
 * Format tanggal masuk dari NIP ke format yang lebih readable
 * @param string $nip
 * @return string format: "Maret 1997"
 */
function formatTanggalMasukFromNIP($nip) {
    $tanggal = extractTanggalMasukFromNIP($nip);
    if ($tanggal == '1900-01-01') {
        return 'NIP tidak valid';
    }
    
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $bulan_num = date('n', strtotime($tanggal));
    $tahun = date('Y', strtotime($tanggal));
    
    return $bulan[$bulan_num] . ' ' . $tahun;
}