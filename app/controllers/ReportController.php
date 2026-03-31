<?php

class ReportController extends Controller {
    private $leaveModel;
    private $leaveBalanceModel;
    
    public function __construct() {
        $this->leaveModel = $this->model('Leave');
        $this->leaveBalanceModel = $this->model('LeaveBalance');
    }
    
    public function index() {
        requireAdmin();
        
        $data = [
            'title' => 'Laporan',
            'page_title' => 'Laporan'
        ];
        
        $this->view('report/index', $data);
    }
    
    public function getStatistics() {
    requireAdmin();
    
    try {
        // Get database instance  
        $db = Database::getInstance();
        
        $stats = [];
        
        // Total users (pegawai and atasan)
        $sql = "SELECT COUNT(*) as total FROM users WHERE user_type IN ('pegawai', 'atasan')";
        $result = $db->fetch($sql);
        $stats['total_users'] = $result['total'] ?? 0;
        
        // Total pengajuan by status
        $sql = "SELECT status, COUNT(*) as total FROM leave_requests GROUP BY status";
        $results = $db->fetchAll($sql);
        
        $stats['pending'] = 0;
        $stats['approved'] = 0;
        $stats['rejected'] = 0;
        
        foreach ($results as $row) {
            if (isset($row['status'])) {
                $stats[$row['status']] = $row['total'];
            }
        }
        
        // Monthly stats (last 6 months)
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $sql = "SELECT COUNT(*) as total FROM leave_requests WHERE DATE_FORMAT(created_at, '%Y-%m') = ?";
            $result = $db->fetch($sql, [$month]);
            
            $monthlyStats[] = [
                'month' => date('M Y', strtotime($month . '-01')),
                'total' => $result['total'] ?? 0
            ];
        }
        
        // Leave type stats
        $sql = "SELECT lt.nama_cuti, COUNT(lr.id) as total
                FROM leave_types lt
                LEFT JOIN leave_requests lr ON lt.id = lr.leave_type_id
                GROUP BY lt.id, lt.nama_cuti
                ORDER BY lt.id";
        $leaveTypeStats = $db->fetchAll($sql);
        
        // Ensure all leave types have a value
        foreach ($leaveTypeStats as &$stat) {
            $stat['total'] = $stat['total'] ?? 0;
        }
        
        // Unit stats (show nama_satker when available)
        $sql = "SELECT COALESCE(s.nama_satker, u.unit_kerja) AS unit_kerja, COUNT(lr.id) as total
            FROM users u
            LEFT JOIN leave_requests lr ON u.id = lr.user_id AND lr.status = 'approved'
            LEFT JOIN satker s ON u.unit_kerja = s.id_satker
            WHERE u.user_type IN ('pegawai', 'atasan')
            GROUP BY s.nama_satker, u.unit_kerja
            ORDER BY total DESC
            LIMIT 5";
        $unitStats = $db->fetchAll($sql);
        
        $this->jsonResponse([
            'success' => true,
            'stats' => $stats,
            'monthly_stats' => $monthlyStats,
            'leave_type_stats' => $leaveTypeStats,
            'unit_stats' => $unitStats
        ]);
        } catch (Exception $e) {
            error_log("Error in getStatistics: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat statistik',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getAvailableReportDates() {
        requireAdmin();

        try {
            $db = Database::getInstance();

            // Collect distinct years from tanggal_mulai and tanggal_selesai
            $sql = "SELECT DISTINCT YEAR(tanggal_mulai) AS y FROM leave_requests WHERE tanggal_mulai IS NOT NULL"
                   . " UNION "
                   . "SELECT DISTINCT YEAR(tanggal_selesai) AS y FROM leave_requests WHERE tanggal_selesai IS NOT NULL"
                   . " ORDER BY y";

            $rows = $db->fetchAll($sql);
            $years = [];
            foreach ($rows as $r) {
                if (isset($r['y']) && $r['y']) {
                    $years[] = (int)$r['y'];
                }
            }

            $months_by_year = [];
            foreach ($years as $y) {
                $m1 = $db->fetchAll("SELECT DISTINCT MONTH(tanggal_mulai) AS m FROM leave_requests WHERE YEAR(tanggal_mulai) = ? AND tanggal_mulai IS NOT NULL", [$y]);
                $m2 = $db->fetchAll("SELECT DISTINCT MONTH(tanggal_selesai) AS m FROM leave_requests WHERE YEAR(tanggal_selesai) = ? AND tanggal_selesai IS NOT NULL", [$y]);

                $months = [];
                foreach ($m1 as $mm) {
                    if (isset($mm['m']) && $mm['m']) $months[] = (int)$mm['m'];
                }
                foreach ($m2 as $mm) {
                    if (isset($mm['m']) && $mm['m']) $months[] = (int)$mm['m'];
                }

                $months = array_values(array_unique($months));
                sort($months, SORT_NUMERIC);
                $months_by_year[$y] = $months;
            }

            $this->jsonResponse([
                'success' => true,
                'years' => $years,
                'months_by_year' => $months_by_year
            ]);
        } catch (Exception $e) {
            error_log("Error in getAvailableReportDates: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal mengambil data tahun/bulan dari database',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getMonthlyReport() {
        requireAdmin();

        $month = isset($_POST['month']) ? cleanInput($_POST['month']) : null;
        $year = isset($_POST['year']) ? cleanInput($_POST['year']) : null;

        if (empty($month) || empty($year)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Parameter bulan atau tahun tidak lengkap'
            ]);
            return;
        }

        try {
            $db = Database::getInstance();

                $sql = "SELECT COALESCE(s.nama_satker, u.unit_kerja, '(Tidak Ditetapkan)') AS unit_kerja,
                        COUNT(lr.id) AS total_pengajuan,
                        SUM(CASE WHEN lr.status = 'approved' THEN 1 ELSE 0 END) AS disetujui,
                        SUM(CASE WHEN lr.status = 'rejected' THEN 1 ELSE 0 END) AS ditolak,
                        SUM(CASE WHEN lr.status = 'pending' THEN 1 ELSE 0 END) AS pending
                    FROM leave_requests lr
                    JOIN users u ON lr.user_id = u.id
                    LEFT JOIN satker s ON u.unit_kerja = s.id_satker
                    WHERE (YEAR(lr.tanggal_mulai) = ? AND MONTH(lr.tanggal_mulai) = ?)
                       OR (YEAR(lr.tanggal_selesai) = ? AND MONTH(lr.tanggal_selesai) = ?)
                    GROUP BY s.nama_satker, u.unit_kerja
                    ORDER BY total_pengajuan DESC";

            $params = [$year, ltrim($month, '0'), $year, ltrim($month, '0')];
            $results = $db->fetchAll($sql, $params);

            $this->jsonResponse([
                'success' => true,
                'data' => $results
            ]);
        } catch (Exception $e) {
            error_log("Error in getMonthlyReport: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal mengambil data laporan bulanan',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getYearlyReport() {
        requireAdmin();

        $year = isset($_POST['year']) ? cleanInput($_POST['year']) : null;

        if (empty($year)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Parameter tahun tidak lengkap'
            ]);
            return;
        }

        try {
            $db = Database::getInstance();

            $sql = "SELECT 
                        COUNT(lr.id) AS total_pengajuan,
                        SUM(CASE WHEN lr.status = 'approved' THEN 1 ELSE 0 END) AS disetujui,
                        SUM(CASE WHEN lr.status = 'rejected' THEN 1 ELSE 0 END) AS ditolak,
                        SUM(CASE WHEN lr.status = 'pending' THEN 1 ELSE 0 END) AS pending
                    FROM leave_requests lr
                    WHERE YEAR(lr.tanggal_mulai) = ? OR YEAR(lr.tanggal_selesai) = ?";

            $params = [$year, $year];
            $result = $db->fetch($sql, $params);

            $this->jsonResponse([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            error_log("Error in getYearlyReport: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal mengambil data laporan tahunan',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function getAdminActivityDates() {
        requireAdmin();
        
        try {
            $db = Database::getInstance();
            
            // Get distinct years from admin_activities for current admin
            $sql = "SELECT DISTINCT YEAR(created_at) as year FROM admin_activities WHERE admin_id = ? ORDER BY year DESC";
            $years = $db->fetchAll($sql, [$_SESSION['user_id']]);
            
            $yearList = [];
            $monthsByYear = [];
            
            foreach ($years as $row) {
                if ($row['year']) {
                    $yearList[] = (int)$row['year'];
                    
                    // Get months for this year
                    $monthSql = "SELECT DISTINCT MONTH(created_at) as month FROM admin_activities 
                                WHERE admin_id = ? AND YEAR(created_at) = ? 
                                ORDER BY month DESC";
                    $months = $db->fetchAll($monthSql, [$_SESSION['user_id'], $row['year']]);
                    
                    $monthList = [];
                    foreach ($months as $m) {
                        if ($m['month']) {
                            $monthList[] = (int)$m['month'];
                        }
                    }
                    
                    $monthsByYear[$row['year']] = $monthList;
                }
            }
            
            $this->jsonResponse([
                'success' => true,
                'years' => $yearList,
                'months_by_year' => $monthsByYear
            ]);
        } catch (Exception $e) {
            error_log("Error in getAdminActivityDates: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal mengambil data bulan dan tahun',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function adminPerformanceReport() {
        requireAdmin();
        
        $month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
        $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
        
        $adminActivityModel = $this->model('AdminActivity');
        $activities = $adminActivityModel->getMonthlyReport($_SESSION['user_id'], $month, $year);
        
        // Get available dates for dropdown
        $db = Database::getInstance();
        $yearSql = "SELECT DISTINCT YEAR(created_at) as year FROM admin_activities WHERE admin_id = ? ORDER BY year DESC";
        $years = $db->fetchAll($yearSql, [$_SESSION['user_id']]);
        
        $availableYears = [];
        $monthsByYear = [];
        foreach ($years as $row) {
            if ($row['year']) {
                $availableYears[] = (int)$row['year'];
                
                $monthSql = "SELECT DISTINCT MONTH(created_at) as month FROM admin_activities 
                            WHERE admin_id = ? AND YEAR(created_at) = ? 
                            ORDER BY month DESC";
                $months = $db->fetchAll($monthSql, [$_SESSION['user_id'], $row['year']]);
                
                $monthList = [];
                foreach ($months as $m) {
                    if ($m['month']) {
                        $monthList[] = (int)$m['month'];
                    }
                }
                
                $monthsByYear[$row['year']] = $monthList;
            }
        }
        
        $data = [
            'title' => 'Laporan Kinerja Admin',
            'page_title' => 'Laporan Kinerja Admin',
            'activities' => $activities,
            'month' => $month,
            'year' => $year,
            'available_years' => $availableYears,
            'months_by_year' => $monthsByYear
        ];
        
        // For AJAX requests, return HTML content directly
        extract($data);
        ob_start();
        include __DIR__ . '/../views/report/admin_performance.php';
        $html = ob_get_clean();
        echo $html;
        exit;
        
        $this->view('report/admin_performance', $data);
    }
    
    public function downloadAdminPerformanceExcel() {
        requireAdmin();

        $month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
        $year  = isset($_GET['year'])  ? (int)$_GET['year']  : date('Y');

        $adminActivityModel = $this->model('AdminActivity');
        $activities = $adminActivityModel->getMonthlyReport($_SESSION['user_id'], $month, $year);

        $monthNames = ['','Januari','Februari','Maret','April','Mei','Juni',
                       'Juli','Agustus','September','Oktober','November','Desember'];
        $monthName  = $monthNames[$month] ?? $month;
        $adminNama  = $_SESSION['nama'] ?? 'Admin';

        $filename = 'Laporan_Kinerja_Admin_' . $monthName . '_' . $year;

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta charset="UTF-8"></head><body>';
        echo '<table border="1" style="border-collapse:collapse;">';

        // Title rows
        echo '<tr><td colspan="6" style="font-weight:bold;font-size:14pt;background:#1565c0;color:white;text-align:center;">Laporan Kinerja Admin</td></tr>';
        echo '<tr><td colspan="6" style="background:#e3f2fd;">Bulan: ' . $monthName . ' ' . $year . '</td></tr>';
        echo '<tr><td colspan="6" style="background:#e3f2fd;">Admin: ' . htmlspecialchars($adminNama) . '</td></tr>';
        echo '<tr><td colspan="6"></td></tr>';

        // Column headers
        echo '<tr style="background:#1565c0;color:white;font-weight:bold;">';
        echo '<td>No</td><td>Tanggal</td><td>Aktivitas</td><td>Nomor Surat</td><td>Pegawai</td><td>Jenis Cuti</td>';
        echo '</tr>';

        if (empty($activities)) {
            echo '<tr><td colspan="6" style="text-align:center;">Tidak ada aktivitas untuk periode ini</td></tr>';
        } else {
            $no = 1;
            foreach ($activities as $activity) {
                $actLabel = ($activity['activity_type'] === 'send_final_blanko')
                    ? 'Kirim Blanko Cuti Final'
                    : 'Upload Dokumen Pendukung';
                echo '<tr>';
                echo '<td>' . $no++ . '</td>';
                echo '<td>' . htmlspecialchars(date('d/m/Y H:i', strtotime($activity['created_at']))) . '</td>';
                echo '<td>' . htmlspecialchars($actLabel) . '</td>';
                echo '<td>' . htmlspecialchars($activity['nomor_surat'] ?? '-') . '</td>';
                echo '<td>' . htmlspecialchars($activity['pegawai_nama'] ?? '-') . '</td>';
                echo '<td>' . htmlspecialchars($activity['jenis_cuti'] ?? '-') . '</td>';
                echo '</tr>';
            }
        }

        echo '</table>';
        echo '</body></html>';
        exit;
    }
    
    public function getEmployeeList() {
        requireAdmin();
        
        try {
            $db = Database::getInstance();
            
            $sql = "SELECT id, nama, nip FROM users WHERE user_type IN ('pegawai', 'atasan') ORDER BY nama ASC";
            $results = $db->fetchAll($sql);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $results
            ]);
        } catch (Exception $e) {
            error_log("Error in getEmployeeList: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal mengambil data pegawai',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function getEmployeeLeaveReport() {
        requireAdmin();
        
        $userId = isset($_POST['user_id']) ? cleanInput($_POST['user_id']) : null;
        
        if (empty($userId)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Parameter user_id tidak lengkap'
            ]);
            return;
        }
        
        try {
            $db = Database::getInstance();
            
            // Get employee name
            $sqlUser = "SELECT nama, nip FROM users WHERE id = ?";
            $user = $db->fetch($sqlUser, [$userId]);
            
            if (!$user) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Pegawai tidak ditemukan'
                ]);
                return;
            }
            
            // Get leave requests
            $sqlLeave = "SELECT lr.tanggal_mulai, lr.tanggal_selesai, lr.jumlah_hari, lt.nama_cuti, lr.status FROM leave_requests lr JOIN leave_types lt ON lr.leave_type_id = lt.id WHERE lr.user_id = ? ORDER BY lr.tanggal_mulai DESC";
            $leaveRequests = $db->fetchAll($sqlLeave, [$userId]);
            
            // Get total sisa_kuota from all years (sum all sisa_kuota for this user)
            $sqlTotalKuota = "SELECT COALESCE(SUM(sisa_kuota), 0) as total_sisa_kuota FROM leave_balances WHERE user_id = ?";
            $totalKuotaResult = $db->fetch($sqlTotalKuota, [$userId]);
            $totalSisaKuota = $totalKuotaResult ? $totalKuotaResult['total_sisa_kuota'] : 0;
            
            // Calculate cuti terpakai from approved leave requests
            $sqlCutiTerpakai = "SELECT COALESCE(SUM(jumlah_hari), 0) as total_terpakai FROM leave_requests WHERE user_id = ? AND status = 'approved'";
            $cutiTerpakaiResult = $db->fetch($sqlCutiTerpakai, [$userId]);
            $cutiTerpakai = $cutiTerpakaiResult ? $cutiTerpakaiResult['total_terpakai'] : 0;
            
            // Calculate sisa_kuota as kuota_tahunan - cuti_terpakai
            $sisaKuota = $totalSisaKuota - $cutiTerpakai;
            
            $this->jsonResponse([
                'success' => true,
                'data' => [
                    'nama' => $user['nama'],
                    'nip' => $user['nip'],
                    'leave_requests' => $leaveRequests,
                    'sisa_kuota' => $sisaKuota,  // Calculated: kuota_tahunan - cuti_terpakai
                    'kuota_tahunan' => $totalSisaKuota,  // Total sisa kuota dari semua tahun
                    'cuti_terpakai' => $cutiTerpakai
                ]
            ]);
        } catch (Exception $e) {
            error_log("Error in getEmployeeLeaveReport: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal mengambil data laporan pegawai',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getAvailableMonthsYear() {
        requireAdmin();

        try {
            $db = Database::getInstance();

            // Collect distinct years from tanggal_mulai and tanggal_selesai
            $sql = "SELECT DISTINCT YEAR(tanggal_mulai) AS y FROM leave_requests WHERE tanggal_mulai IS NOT NULL"
                   . " UNION "
                   . "SELECT DISTINCT YEAR(tanggal_selesai) AS y FROM leave_requests WHERE tanggal_selesai IS NOT NULL"
                   . " ORDER BY y";

            $rows = $db->fetchAll($sql);
            $years = [];
            foreach ($rows as $r) {
                if (isset($r['y']) && $r['y']) {
                    $years[] = (int)$r['y'];
                }
            }

            $months_by_year = [];
            foreach ($years as $y) {
                $m1 = $db->fetchAll("SELECT DISTINCT MONTH(tanggal_mulai) AS m FROM leave_requests WHERE YEAR(tanggal_mulai) = ? AND tanggal_mulai IS NOT NULL", [$y]);
                $m2 = $db->fetchAll("SELECT DISTINCT MONTH(tanggal_selesai) AS m FROM leave_requests WHERE YEAR(tanggal_selesai) = ? AND tanggal_selesai IS NOT NULL", [$y]);

                $months = [];
                foreach ($m1 as $mm) {
                    if (isset($mm['m']) && $mm['m']) $months[] = (int)$mm['m'];
                }
                foreach ($m2 as $mm) {
                    if (isset($mm['m']) && $mm['m']) $months[] = (int)$mm['m'];
                }

                $months = array_values(array_unique($months));
                sort($months, SORT_NUMERIC);
                $months_by_year[$y] = $months;
            }

            $this->jsonResponse([
                'success' => true,
                'years' => $years,
                'months_by_year' => $months_by_year
            ]);
        } catch (Exception $e) {
            error_log("Error in getAvailableMonthsYear: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal mengambil data tahun/bulan dari database',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getMonthlyLeaveReport() {
        requireAdmin();

        $month = isset($_POST['month']) ? cleanInput($_POST['month']) : null;
        $year = isset($_POST['year']) ? cleanInput($_POST['year']) : null;

        if (empty($month) || empty($year)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Parameter bulan atau tahun tidak lengkap'
            ]);
            return;
        }

        try {
            $db = Database::getInstance();

            // Get leave requests for the selected month/year with employee names
            // Filter: status must be 'approved' AND is_completed must be 1
            $sql = "SELECT DISTINCT
                        u.id,
                        u.nama,
                        lr.tanggal_mulai,
                        lr.tanggal_selesai
                    FROM leave_requests lr
                    JOIN users u ON lr.user_id = u.id
                    WHERE ((YEAR(lr.tanggal_mulai) = ? AND MONTH(lr.tanggal_mulai) = ?)
                       OR (YEAR(lr.tanggal_selesai) = ? AND MONTH(lr.tanggal_selesai) = ?))
                    AND lr.status = 'approved'
                    AND lr.is_completed = 1
                    ORDER BY u.nama ASC, lr.tanggal_mulai ASC";

            $params = [$year, (int)ltrim($month, '0'), $year, (int)ltrim($month, '0')];
            $results = $db->fetchAll($sql, $params);

            // Remove duplicates and calculate total sisa_kuota for each user
            $uniqueResults = [];
            foreach ($results as $result) {
                $key = $result['id'] . '_' . $result['tanggal_mulai'] . '_' . $result['tanggal_selesai'];
                if (!isset($uniqueResults[$key])) {
                    // Get total sisa_kuota for this user (sum from all years)
                    $sqlKuota = "SELECT COALESCE(SUM(sisa_kuota), 0) as total_sisa_kuota 
                                 FROM leave_balances 
                                 WHERE user_id = ?";
                    $kuotaResult = $db->fetch($sqlKuota, [$result['id']]);
                    $totalSisaKuota = $kuotaResult ? $kuotaResult['total_sisa_kuota'] : 0;

                    $uniqueResults[$key] = [
                        'id' => $result['id'],
                        'nama' => $result['nama'],
                        'tanggal_mulai' => $result['tanggal_mulai'],
                        'tanggal_selesai' => $result['tanggal_selesai'],
                        'sisa_kuota' => (int)$totalSisaKuota
                    ];
                }
            }

            $this->jsonResponse([
                'success' => true,
                'data' => array_values($uniqueResults)
            ]);
        } catch (Exception $e) {
            error_log("Error in getMonthlyLeaveReport: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal mengambil data laporan bulanan',
                'error' => $e->getMessage()
            ]);
        }
    }

    
    public function downloadPerorang() {
        requireAdmin();

        $userId = isset($_GET['user_id']) ? cleanInput($_GET['user_id']) : null;

        if (empty($userId)) {
            die('Parameter user_id tidak lengkap');
        }

        try {
            $db = Database::getInstance();

            // Get employee info
            $user = $db->fetch("SELECT nama, nip FROM users WHERE id = ?", [$userId]);
            if (!$user) die('Pegawai tidak ditemukan');

            // Get leave requests
            $leaveRequests = $db->fetchAll(
                "SELECT lt.nama_cuti, lr.tanggal_mulai, lr.tanggal_selesai, lr.jumlah_hari, lr.status
                 FROM leave_requests lr
                 JOIN leave_types lt ON lr.leave_type_id = lt.id
                 WHERE lr.user_id = ?
                 ORDER BY lr.tanggal_mulai DESC",
                [$userId]
            );

            // Get quota info
            $totalSisaKuota = (int)($db->fetch(
                "SELECT COALESCE(SUM(sisa_kuota), 0) as total FROM leave_balances WHERE user_id = ?",
                [$userId]
            )['total'] ?? 0);

            $cutiTerpakai = (int)($db->fetch(
                "SELECT COALESCE(SUM(jumlah_hari), 0) as total FROM leave_requests WHERE user_id = ? AND status = 'approved'",
                [$userId]
            )['total'] ?? 0);

            $sisaKuota = $totalSisaKuota - $cutiTerpakai;

            $filename = 'Laporan_Cuti_' . preg_replace('/[^A-Za-z0-9_]/', '_', $user['nama']) . '_' . date('Ymd');

            // Output XLS headers
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
            header('Cache-Control: max-age=0');

            $statusMap = ['approved' => 'Disetujui', 'rejected' => 'Ditolak', 'pending' => 'Pending'];

            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta charset="UTF-8"></head><body>';
            echo '<table border="1" style="border-collapse:collapse;">';
            // Title
            echo '<tr><td colspan="5" style="font-weight:bold;font-size:14pt;background:#1b5e20;color:white;text-align:center;">Laporan Cuti Pegawai</td></tr>';
            echo '<tr><td colspan="5"></td></tr>';
            // Employee info
            echo '<tr><td style="font-weight:bold;background:#e8f5e9;">Nama</td><td colspan="4">' . htmlspecialchars($user['nama']) . '</td></tr>';
            echo '<tr><td style="font-weight:bold;background:#e8f5e9;">NIP</td><td colspan="4">' . htmlspecialchars($user['nip'] ?? '-') . '</td></tr>';
            echo '<tr><td style="font-weight:bold;background:#e8f5e9;">Kuota Tahunan</td><td colspan="4">' . $totalSisaKuota . ' hari</td></tr>';
            echo '<tr><td style="font-weight:bold;background:#e8f5e9;">Cuti Terpakai</td><td colspan="4">' . $cutiTerpakai . ' hari</td></tr>';
            echo '<tr><td style="font-weight:bold;background:#e8f5e9;">Sisa Kuota</td><td colspan="4">' . $sisaKuota . ' hari</td></tr>';
            echo '<tr><td colspan="5"></td></tr>';
            // Leave history header
            echo '<tr style="background:#1b5e20;color:white;font-weight:bold;">';
            echo '<td>No</td><td>Jenis Cuti</td><td>Tanggal Mulai</td><td>Tanggal Selesai</td><td>Status</td>';
            echo '</tr>';
            if (empty($leaveRequests)) {
                echo '<tr><td colspan="5" style="text-align:center;">Tidak ada riwayat cuti</td></tr>';
            } else {
                $no = 1;
                foreach ($leaveRequests as $lr) {
                    $statusLabel = $statusMap[$lr['status']] ?? $lr['status'];
                    echo '<tr>';
                    echo '<td>' . $no++ . '</td>';
                    echo '<td>' . htmlspecialchars($lr['nama_cuti'] ?? '-') . '</td>';
                    echo '<td>' . htmlspecialchars($lr['tanggal_mulai'] ?? '-') . '</td>';
                    echo '<td>' . htmlspecialchars($lr['tanggal_selesai'] ?? '-') . '</td>';
                    echo '<td>' . $statusLabel . '</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
            echo '</body></html>';
            exit;

        } catch (Exception $e) {
            error_log("Error in downloadPerorang: " . $e->getMessage());
            die('Gagal membuat file Excel: ' . $e->getMessage());
        }
    }

    public function downloadBulanan() {
        requireAdmin();

        $month = isset($_GET['month']) ? cleanInput($_GET['month']) : null;
        $year  = isset($_GET['year'])  ? cleanInput($_GET['year'])  : null;

        if (empty($month) || empty($year)) {
            die('Parameter bulan atau tahun tidak lengkap');
        }

        $monthNames = ['','Januari','Februari','Maret','April','Mei','Juni',
                       'Juli','Agustus','September','Oktober','November','Desember'];
        $monthName  = $monthNames[(int)ltrim($month, '0')] ?? $month;

        try {
            $db = Database::getInstance();

            $sql = "SELECT DISTINCT
                        u.id,
                        u.nama,
                        lr.tanggal_mulai,
                        lr.tanggal_selesai
                    FROM leave_requests lr
                    JOIN users u ON lr.user_id = u.id
                    WHERE ((YEAR(lr.tanggal_mulai) = ? AND MONTH(lr.tanggal_mulai) = ?)
                       OR (YEAR(lr.tanggal_selesai) = ? AND MONTH(lr.tanggal_selesai) = ?))
                    AND lr.status = 'approved'
                    AND lr.is_completed = 1
                    ORDER BY u.nama ASC, lr.tanggal_mulai ASC";

            $params = [$year, (int)ltrim($month, '0'), $year, (int)ltrim($month, '0')];
            $results = $db->fetchAll($sql, $params);

            // Remove duplicates & enrich with sisa_kuota
            $uniqueResults = [];
            foreach ($results as $result) {
                $key = $result['id'] . '_' . $result['tanggal_mulai'] . '_' . $result['tanggal_selesai'];
                if (!isset($uniqueResults[$key])) {
                    $kuota = $db->fetch(
                        "SELECT COALESCE(SUM(sisa_kuota), 0) as total FROM leave_balances WHERE user_id = ?",
                        [$result['id']]
                    );
                    $uniqueResults[$key] = [
                        'nama'            => $result['nama'],
                        'tanggal_mulai'   => $result['tanggal_mulai'],
                        'tanggal_selesai' => $result['tanggal_selesai'],
                        'sisa_kuota'      => (int)($kuota['total'] ?? 0),
                    ];
                }
            }
            $uniqueResults = array_values($uniqueResults);

            $filename = 'Laporan_Bulanan_' . $monthName . '_' . $year;

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
            header('Cache-Control: max-age=0');

            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta charset="UTF-8"></head><body>';
            echo '<table border="1" style="border-collapse:collapse;">';
            // Title
            echo '<tr><td colspan="4" style="font-weight:bold;font-size:14pt;background:#0d47a1;color:white;text-align:center;">Laporan Bulanan - ' . $monthName . ' ' . $year . '</td></tr>';
            echo '<tr><td colspan="4"></td></tr>';
            // Column headers
            echo '<tr style="background:#0d47a1;color:white;font-weight:bold;">';
            echo '<td>No</td><td>Nama Pegawai</td><td>Tanggal Mulai</td><td>Tanggal Selesai</td><td>Sisa Kuota</td>';
            echo '</tr>';
            if (empty($uniqueResults)) {
                echo '<tr><td colspan="4" style="text-align:center;">Tidak ada data cuti untuk periode ini</td></tr>';
            } else {
                $no = 1;
                foreach ($uniqueResults as $row) {
                    echo '<tr>';
                    echo '<td>' . $no++ . '</td>';
                    echo '<td>' . htmlspecialchars($row['nama'] ?? '-') . '</td>';
                    echo '<td>' . htmlspecialchars($row['tanggal_mulai'] ?? '-') . '</td>';
                    echo '<td>' . htmlspecialchars($row['tanggal_selesai'] ?? '-') . '</td>';
                    echo '<td>' . $row['sisa_kuota'] . ' hari</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
            echo '</body></html>';
            exit;

        } catch (Exception $e) {
            error_log("Error in downloadBulanan: " . $e->getMessage());
            die('Gagal membuat file Excel: ' . $e->getMessage());
        }
    }

    public function export() {
        requireAdmin();
        
        $type = cleanInput($_POST['type']);
        $tahun = isset($_POST['tahun']) ? cleanInput($_POST['tahun']) : date('Y');
        
        $data = [];
        $headers = [];
        $filename = '';
        
        switch ($type) {
            case 'monthly':
                $month = isset($_POST['month']) ? cleanInput($_POST['month']) : null;
                $year = isset($_POST['year']) ? cleanInput($_POST['year']) : null;

                if (empty($month) || empty($year)) {
                    $this->jsonResponse(['success' => false, 'message' => 'Parameter bulan/tahun tidak lengkap']);
                    return;
                }

                $db = Database::getInstance();
                $sql = "SELECT COALESCE(s.nama_satker, u.unit_kerja, '(Tidak Ditetapkan)') AS 'Unit Kerja',
                            COUNT(lr.id) AS 'Total Pengajuan',
                            SUM(CASE WHEN lr.status = 'approved' THEN 1 ELSE 0 END) AS 'Disetujui',
                            SUM(CASE WHEN lr.status = 'rejected' THEN 1 ELSE 0 END) AS 'Ditolak',
                            SUM(CASE WHEN lr.status = 'pending' THEN 1 ELSE 0 END) AS 'Pending'
                        FROM leave_requests lr
                    JOIN users u ON lr.user_id = u.id
                    LEFT JOIN satker s ON u.unit_kerja = s.id_satker
                        WHERE (YEAR(lr.tanggal_mulai) = ? AND MONTH(lr.tanggal_mulai) = ?)
                           OR (YEAR(lr.tanggal_selesai) = ? AND MONTH(lr.tanggal_selesai) = ?)
                    GROUP BY s.nama_satker, u.unit_kerja
                        ORDER BY `Total Pengajuan` DESC";

                $params = [$year, ltrim($month, '0'), $year, ltrim($month, '0')];
                $results = $db->fetchAll($sql, $params);

                $headers = [];
                if (count($results) > 0) {
                    $headers = array_keys($results[0]);
                } else {
                    $headers = ['Unit Kerja', 'Total Pengajuan', 'Disetujui', 'Ditolak', 'Pending'];
                }

                $filename = 'Laporan_Bulanan_' . $year . '_' . str_pad(ltrim($month, '0'), 2, '0', STR_PAD_LEFT);

                $this->jsonResponse([
                    'success' => true,
                    'headers' => $headers,
                    'data' => $results,
                    'filename' => $filename
                ]);
                break;
            case 'history':
                $sql = "SELECT 
                    u.nama as 'Nama',
                    u.nip as 'NIP',
                    u.jabatan as 'Jabatan',
                    COALESCE(s.nama_satker, u.unit_kerja) as 'Unit Kerja',
                    lt.nama_cuti as 'Jenis Cuti',
                    lr.tanggal_mulai as 'Tanggal Mulai',
                    lr.tanggal_selesai as 'Tanggal Selesai',
                    lr.jumlah_hari as 'Jumlah Hari',
                    lr.alasan as 'Alasan',
                    lr.status as 'Status',
                    au.nama as 'Disetujui Oleh',
                    lr.approval_date as 'Tanggal Persetujuan',
                    lr.catatan_approval as 'Catatan'
                    FROM leave_requests lr
                    JOIN users u ON lr.user_id = u.id
                    LEFT JOIN satker s ON u.unit_kerja = s.id_satker
                    JOIN leave_types lt ON lr.leave_type_id = lt.id
                    LEFT JOIN users au ON lr.approved_by = au.id AND au.is_deleted = 0";
                
                if (!empty($tahun)) {
                    $sql .= " WHERE YEAR(lr.tanggal_mulai) = " . $tahun;
                }
                
                $sql .= " ORDER BY lr.created_at DESC";
                $filename = 'Riwayat_Cuti_' . $tahun;
                
                // For history type
                $results = $this->db()->fetchAll($sql);
                
                if (count($results) > 0) {
                    $headers = array_keys($results[0]);
                    $data = $results;
                }
                
                $this->jsonResponse([
                    'success' => true,
                    'headers' => $headers,
                    'data' => $data,
                    'filename' => $filename
                ]);
                break;
                
            case 'balance':
                // Get balance data
                $users = $this->leaveBalanceModel->getUsersWithBalance($tahun);
                
                $headers = ['Nama', 'NIP', 'Jabatan', 'Unit Kerja', 'Golongan', 
                           'Total Kuota', 'Terpakai', 'Sisa', 'Detail Sisa'];
                
                foreach ($users as $user) {
                    $data[] = [
                        $user['nama'],
                        $user['nip'],
                        $user['jabatan'],
                        $user['unit_kerja'],
                        $user['golongan'],
                        $user['kuota_tahunan'],
                        $user['cuti_terpakai'],
                        $user['sisa_kuota'],
                        $user['detail_sisa']
                    ];
                }
                
                $filename = 'Kuota_Cuti_' . $tahun;
                
                $this->jsonResponse([
                    'success' => true,
                    'headers' => $headers,
                    'data' => $data,
                    'filename' => $filename
                ]);
                break;
        }
    }
}