<?php
class UserController extends Controller {
    private $userModel;
    private $notificationModel;
    private $leaveBalanceModel;
    private $leaveModel;
    
    public function __construct() {
        $this->userModel = $this->model('User');
        $this->notificationModel = $this->model('Notification');
        $this->leaveBalanceModel = $this->model('LeaveBalance');
        $this->leaveModel = $this->model('Leave');
    }
    
    public function profile() {
        requireLogin();
        if ($_SESSION['user_type'] == 'admin') {
            $this->view('user/profile_admin');
        } else {
            $this->view('user/profile_user');
        }
    }
    
    public function getNotifications() {
        requireLogin();
        
        $notifications = $this->notificationModel->getUserNotifications($_SESSION['user_id']);
        
        // Format dates
        foreach ($notifications as &$notif) {
            $notif['created_at'] = formatTanggal($notif['created_at']);
        }
        
        $this->jsonResponse([
            'success' => true,
            'notifications' => $notifications
        ]);
    }
    
    public function markNotificationRead() {
        requireLogin();
        
        $notificationId = cleanInput($_POST['notification_id']);
        $result = $this->notificationModel->markAsRead($notificationId, $_SESSION['user_id']);
        
        $this->jsonResponse([
            'success' => $result,
            'message' => $result ? 'Notification marked as read' : 'Failed to update notification'
        ]);
    }
    
    public function markAllNotificationsRead() {
        requireLogin();
        
        $result = $this->notificationModel->markAllAsRead($_SESSION['user_id']);
        
        $this->jsonResponse([
            'success' => $result,
            'message' => $result ? 'All notifications marked as read' : 'Failed to update notifications'
        ]);
    }
    
    public function getStatistics() {
        requireLogin();
        
        $stats = [];
        $userId = $_SESSION['user_id'];
        
        // Get database instance
        $db = Database::getInstance();
        
        if (isUser()) {
            // User statistics
            // Total balance from specific years (last 3 years)
            $stats['sisa_cuti'] = $this->leaveBalanceModel->getTotalBalance($userId, date('Y'));
            
            // Leave request counts
            $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                    FROM leave_requests 
                    WHERE user_id = ?";
            
            $result = $db->fetch($sql, [$userId]);
            
            $stats['total_pengajuan'] = $result['total'] ?? 0;
            $stats['pending'] = $result['pending'] ?? 0;
            $stats['approved'] = $result['approved'] ?? 0;
            $stats['rejected'] = $result['rejected'] ?? 0;
            $stats['completed'] = $result['completed'] ?? 0;
            $stats['menunggu_dokumen'] = ($result['approved'] ?? 0) + ($result['rejected'] ?? 0);
            
        } elseif (isAtasan()) {
            // Atasan statistics (filter by atasan_id)
            $user = $this->userModel->find($userId);

            // Untuk akun role 'atasan', harus resolve id_atasan berdasarkan NIP di tabel atasan
            $atasanId = null;
            $userNip = $user['nip'] ?? null;
            if ($userNip) {
                $row = $db->fetch("SELECT id_atasan FROM atasan WHERE NIP = ? LIMIT 1", [$userNip]);
                if ($row && isset($row['id_atasan'])) {
                    $atasanId = $row['id_atasan'];
                    error_log("[getStatistics] atasan user resolved by NIP={$userNip} -> atasanId={$atasanId}");
                } else {
                    error_log("[getStatistics] no atasan record for NIP={$userNip} (userId={$userId})");
                }
            } else {
                error_log("[getStatistics] atasan user has no NIP (userId={$userId})");
            }

            if (empty($atasanId)) {
                // Jika tetap tidak ditemukan mapping atasan, set semua statistik atasan menjadi 0
                $stats['total_users'] = 0;
                $stats['total_pengajuan'] = 0;
                $stats['pending'] = 0;
                $stats['approved'] = 0;
                $stats['rejected'] = 0;
                $stats['completed'] = 0;
                $stats['menunggu_dokumen'] = 0;
            } else {
                // Logging debug singkat
                error_log("[getStatistics] atasanId={$atasanId}, userId={$userId}");

                // Total pegawai under this atasan (sumber yang sama dengan atasan/index)
                $sql = "SELECT COUNT(*) as total FROM users WHERE atasan = ? AND is_deleted = 0";
                $result = $db->fetch($sql, [$atasanId]);
                $stats['total_users'] = $result['total'] ?? 0;

                // Leave request counts for this atasan
                $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                        SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as completed
                        FROM leave_requests
                        WHERE atasan_id = ?";

                $result = $db->fetch($sql, [$atasanId]);

                // Log hasil query counts
                error_log("[getStatistics] counts=" . json_encode($result));

                $stats['total_pengajuan'] = $result['total'] ?? 0;
                $stats['pending'] = $result['pending'] ?? 0;
                $stats['approved'] = $result['approved'] ?? 0;
                $stats['rejected'] = $result['rejected'] ?? 0;
                $stats['completed'] = $result['completed'] ?? 0;

                // menunggu_dokumen: status in ('approved','rejected','changed','postponed')
                $sql = "SELECT COUNT(*) as total FROM leave_requests WHERE atasan_id = ? AND status IN ('approved','rejected','changed','postponed')";
                $result = $db->fetch($sql, [$atasanId]);
                $stats['menunggu_dokumen'] = $result['total'] ?? 0;
            }

        } elseif (isAdmin()) {
            // Admin statistics
            $sql = "SELECT COUNT(*) as total FROM users WHERE user_type IN ('pegawai', 'atasan')";
            $result = $db->fetch($sql);
            $stats['total_users'] = $result['total'] ?? 0;
            
            // Leave request counts
            $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                    FROM leave_requests";
            
            $result = $db->fetch($sql);
            
            $stats['total_pengajuan'] = $result['total'] ?? 0;
            $stats['pending'] = $result['pending'] ?? 0;
            $stats['approved'] = $result['approved'] ?? 0;
            $stats['rejected'] = $result['rejected'] ?? 0;
            $stats['completed'] = $result['completed'] ?? 0;
            $stats['menunggu_dokumen'] = ($result['approved'] ?? 0) + ($result['rejected'] ?? 0);
        }
        
        $this->jsonResponse(['success' => true, 'data' => $stats]);
    }
    
    /**
     * Get detailed leave quotas for all leave types
     */
    public function getLeaveQuotas() {
        requireLogin();
        
        $userId = $_SESSION['user_id'];
        $db = Database::getInstance();
        
        // Get all leave types
        $sql = "SELECT * FROM leave_types ORDER BY id";
        $leaveTypes = $db->fetchAll($sql);
        
        $quotas = [];
        
        foreach ($leaveTypes as $leaveType) {
            $quotaInfo = [
                'id' => $leaveType['id'],
                'nama_cuti' => $leaveType['nama_cuti'],
                'deskripsi' => $leaveType['deskripsi'],
                'max_days' => $leaveType['max_days'],
                'is_akumulatif' => isset($leaveType['is_akumulatif']) ? $leaveType['is_akumulatif'] : 1,
                'sisa_kuota' => 0,
                'kuota_tersedia' => 0,
                'keterangan' => ''
            ];
            
            // Hitung kuota berdasarkan jenis cuti
            switch ($leaveType['id']) {
                case 1: // Cuti Tahunan (Akumulatif)
                    $quotaInfo['is_akumulatif'] = 1;
                    $totalSisa = $this->leaveBalanceModel->getTotalBalance($userId, date('Y'));
                    $quotaInfo['sisa_kuota'] = $totalSisa;
                    $quotaInfo['kuota_tersedia'] = $totalSisa;
                    $quotaInfo['keterangan'] = "Sisa kuota akumulatif dari 3 tahun terakhir";
                    break;
                    
                case 2: // Cuti Besar (Akumulatif)
                    $quotaInfo['is_akumulatif'] = 1;
                    $sql = "SELECT sisa_kuota FROM kuota_cuti_besar WHERE user_id = ? LIMIT 1";
                    $result = $db->fetch($sql, [$userId]);
                    $sisaKuota = $result ? $result['sisa_kuota'] : $leaveType['max_days'];
                    $quotaInfo['sisa_kuota'] = $sisaKuota;
                    $quotaInfo['kuota_tersedia'] = $sisaKuota;
                    $quotaInfo['keterangan'] = "Maksimal 90 hari, bersifat akumulatif";
                    break;
                    
                case 3: // Cuti Sakit (Akumulatif)
                    $quotaInfo['is_akumulatif'] = 1;
                    $tahun = date('Y');
                    $sql = "SELECT sisa_kuota FROM kuota_cuti_sakit WHERE user_id = ? AND tahun = ?";
                    $result = $db->fetch($sql, [$userId, $tahun]);
                    $sisaKuota = $result ? $result['sisa_kuota'] : $leaveType['max_days'];
                    $quotaInfo['sisa_kuota'] = $sisaKuota;
                    $quotaInfo['kuota_tersedia'] = $sisaKuota;
                    $quotaInfo['keterangan'] = "14 hari per tahun, bersifat akumulatif";
                    break;
                    
                case 4: // Cuti Melahirkan (Tidak Akumulatif)
                    $quotaInfo['is_akumulatif'] = 0;
                    $quotaInfo['sisa_kuota'] = $leaveType['max_days'];
                    $quotaInfo['kuota_tersedia'] = $leaveType['max_days'];
                    
                    // Check if user is female (digit 15 of NIP = '2')
                    $nip = isset($_SESSION['nip']) ? str_replace(' ', '', $_SESSION['nip']) : '';
                    $isFemale = (strlen($nip) >= 15 && substr($nip, 14, 1) === '2');
                    
                    if ($isFemale) {
                        // Max kesempatatan = 3
                        $sqlMelahirkan = "SELECT jumlah_pengambilan FROM kuota_cuti_melahirkan WHERE user_id = ? LIMIT 1";
                        $resMelahirkan = $db->fetch($sqlMelahirkan, [$userId]);
                        $taken = $resMelahirkan ? (int)$resMelahirkan['jumlah_pengambilan'] : 0;
                        $left = max(0, 3 - $taken);
                        $quotaInfo['keterangan'] = "Maksimal 90 hari per sekali mengajukan, sisa kesempatan mengambil: {$left} kali lagi";
                        $quotaInfo['kesempatan_sisa'] = $left;
                    } else {
                        $quotaInfo['keterangan'] = "Maksimal 90 hari per sekali mengajukan, tidak akumulatif";
                    }
                    break;
                    
                case 5: // Cuti Karena Alasan Penting (Per tahun)
                    $quotaInfo['is_akumulatif'] = 0;
                    $quotaInfo['sisa_kuota'] = $leaveType['max_days'];
                    $quotaInfo['kuota_tersedia'] = $leaveType['max_days'];
                    $quotaInfo['keterangan'] = "Maksimal 30 hari per sekali mengajukan, tidak akumulatif";
                    break;
                    
                case 6: // Cuti di Luar Tanggungan Negara (Per tahun)
                    $quotaInfo['is_akumulatif'] = 1;
                    $tahun = date('Y');
                    $sql = "SELECT sisa_kuota FROM kuota_cuti_luar_tanggungan WHERE user_id = ? AND tahun = ?";
                    $result = $db->fetch($sql, [$userId, $tahun]);
                    $sisaKuota = $result ? $result['sisa_kuota'] : $leaveType['max_days'];
                    $quotaInfo['sisa_kuota'] = $sisaKuota;
                    $quotaInfo['kuota_tersedia'] = $sisaKuota;
                    $quotaInfo['keterangan'] = "Maksimal 365 hari per tahun, bersifat akumulatif";
                    break;
                    
                default:
                    $quotaInfo['sisa_kuota'] = $leaveType['max_days'];
                    $quotaInfo['kuota_tersedia'] = $leaveType['max_days'];
                    $quotaInfo['keterangan'] = "Kuota standar";
                    break;
            }
            
            $quotas[] = $quotaInfo;
        }
        
        $this->jsonResponse(['success' => true, 'data' => $quotas]);
    }
    
    // app/controllers/UserController.php
    public function getRecentActivities() {
        requireLogin();
        $limit = 10;

        // Jika role atasan, filter activities by atasan_id (resolve id_atasan via NIP)
        if (isAtasan()) {
            $user = $this->userModel->find($_SESSION['user_id']);
            $atasanId = null;
            $atasanRole = null;
            $userNip = $user['nip'] ?? null;
            $db = Database::getInstance();
            if ($userNip) {
                $row = $db->fetch("SELECT id_atasan, role FROM atasan WHERE NIP = ? LIMIT 1", [$userNip]);
                if ($row && isset($row['id_atasan'])) {
                    $atasanId = $row['id_atasan'];
                    $atasanRole = $row['role'] ?? null;
                    error_log("[getRecentActivities] resolved atasanId={$atasanId} with role={$atasanRole} for userNIP={$userNip}");
                } else {
                    error_log("[getRecentActivities] no atasan found for userNIP={$userNip}");
                }
            } else {
                error_log("[getRecentActivities] atasan user has no NIP (userId={$_SESSION['user_id']})");
            }
            
            if ($atasanId) {
                // Construct filters for getHistory
                $filters = ['atasan_id' => $atasanId];
                
                // Add special viewer roles if atasan has a role
                if (!empty($atasanRole)) {
                    switch ($atasanRole) {
                        case 'kasubbag':
                            $filters['is_kasubbag_viewer'] = true;
                            $filters['kasubbag_id'] = $atasanId;
                            break;
                        case 'kabag':
                            $filters['is_kabag_viewer'] = true;
                            $filters['kabag_approver_id'] = $atasanId;
                            break;
                        case 'sekretaris':
                            $filters['is_sekretaris_viewer'] = true;
                            $filters['sekretaris_approver_id'] = $atasanId;
                            break;
                        case 'ketua':
                            $filters['is_ketua_viewer'] = true;
                            $filters['ketua_approver_id'] = $atasanId;
                            break;
                    }
                }
                
                $allActivities = $this->leaveModel->getHistory($filters);
                $activities = array_slice($allActivities, 0, $limit);
            } else {
                $activities = []; // No mapping for this atasan account, show no activities
            }
        } else {
            $activities = $this->leaveModel->getRecentActivities($limit);
        }

        foreach ($activities as &$activity) {
            $activity['status_badge'] = getStatusBadge($activity['status']);
            $activity['created_at_formatted'] = formatTanggal($activity['created_at']);
            // Fallback jika tanggal/jumlah_hari null/empty
            $activity['tanggal_mulai'] = empty($activity['tanggal_mulai']) ? '-' : $activity['tanggal_mulai'];
            $activity['tanggal_selesai'] = empty($activity['tanggal_selesai']) ? '-' : $activity['tanggal_selesai'];
            $activity['jumlah_hari'] = (isset($activity['jumlah_hari']) && $activity['jumlah_hari'] !== null && $activity['jumlah_hari'] !== '') ? $activity['jumlah_hari'] : '-';
        }

        $this->jsonResponse(['success' => true, 'data' => $activities]);
    }

    public function getMyRecentActivities() {
        requireLogin();
        $limit = 10;
        $userId = $_SESSION['user_id'];
        
        // Use getHistory from LeaveModel which supports user_id filter
        $activities = $this->leaveModel->getHistory(['user_id' => $userId]);
        
        // Limit to $limit items
        $activities = array_slice($activities, 0, $limit);

        foreach ($activities as &$activity) {
            $activity['status_badge'] = getStatusBadge($activity['status']);
            $activity['created_at_formatted'] = formatTanggal($activity['created_at']);
            // Fallback jika tanggal/jumlah_hari null/empty
            $activity['tanggal_mulai'] = empty($activity['tanggal_mulai']) ? '-' : $activity['tanggal_mulai'];
            $activity['tanggal_selesai'] = empty($activity['tanggal_selesai']) ? '-' : $activity['tanggal_selesai'];
            $activity['jumlah_hari'] = (isset($activity['jumlah_hari']) && $activity['jumlah_hari'] !== null && $activity['jumlah_hari'] !== '') ? $activity['jumlah_hari'] : '-';
        }

        $this->jsonResponse(['success' => true, 'data' => $activities]);
    }
    
    public function getAllUnits() {
        requireAdmin();
        
        // Return distinct unit names (nama_satker) joined from satker table when possible
        $sql = "SELECT DISTINCT COALESCE(s.nama_satker, u.unit_kerja) as unit_name
                FROM users u
                LEFT JOIN satker s ON u.unit_kerja = s.id_satker
                WHERE u.user_type IN ('pegawai', 'atasan')
                ORDER BY unit_name";
        $results = $this->db()->fetchAll($sql);

        $units = array_column($results, 'unit_name');

        $this->jsonResponse(['success' => true, 'data' => $units]);
    }
    
    public function getYears() {
        requireLogin();
        
        $sql = "SELECT DISTINCT YEAR(tanggal_mulai) as tahun 
                FROM leave_requests 
                ORDER BY tahun DESC";
        
        $results = $this->db()->fetchAll($sql);
        $years = array_column($results, 'tahun');
        
        // Add current year if not exists
        $currentYear = date('Y');
        if (!in_array($currentYear, $years)) {
            array_unshift($years, $currentYear);
        }
        
        $this->jsonResponse(['success' => true, 'data' => $years]);
    }
    
    public function manage() {
        requireAdmin();
        
        $data = [
            'title' => 'Kelola User',
            'page_title' => 'Kelola User'
        ];
        
        $this->view('user/manage', $data);
    }
    
    public function getUsers() {
        requireAdmin();
     $db = Database::getInstance();
     $sql = "SELECT u.*, a.nama_atasan, a.NIP as nip_atasan, s.nama_satker FROM users u " .
         "LEFT JOIN atasan a ON u.atasan = a.id_atasan " .
         "LEFT JOIN satker s ON u.unit_kerja = s.id_satker " .
         "ORDER BY u.id";
     $users = $db->fetchAll($sql);
        // Add quota information for each user
        foreach ($users as &$user) {
            if ($user['user_type'] == 'pegawai' || $user['user_type'] == 'atasan') {
                $totalSisa = $this->leaveBalanceModel->getTotalBalance($user['id'], date('Y'));
                $user['total_sisa_cuti'] = $totalSisa;
            } else {
                $user['total_sisa_cuti'] = '-';
            }
            // Format tanggal masuk
            if ($user['tanggal_masuk']) {
                $user['tanggal_masuk'] = formatTanggal($user['tanggal_masuk']);
            } else {
                $user['tanggal_masuk'] = '-';
            }
            // Add user status information
            if ($user['is_deleted']) {
                $user['user_status'] = 'deleted';
                $user['user_status_badge'] = '<span class="badge bg-danger">Non-Aktif</span>';
            } elseif ($user['is_modified']) {
                $user['user_status'] = 'modified';
                $user['user_status_badge'] = '<span class="badge bg-warning">Diubah</span>';
            } else {
                $user['user_status'] = 'active';
                $user['user_status_badge'] = '<span class="badge bg-success">Aktif</span>';
            }
        }
        $this->jsonResponse(['success' => true, 'data' => $users]);
    }

    public function add() {
        requireAdmin();

        $atasanModel = $this->model('Atasan');
        $atasanList = $atasanModel->getAllAtasan();

        $data = [
            'title' => 'Tambah User',
            'page_title' => 'Tambah User',
            'user' => null,
            'action' => 'add',
            'atasanList' => $atasanList
        ];

        $this->view('user/form', $data);
    }

    public function edit($id) {
        requireAdmin();

        $user = $this->userModel->getUserWithAtasan($id);
        $atasanModel = $this->model('Atasan');
        $atasanList = $atasanModel->getAllAtasan();

        $data = [
            'title' => 'Edit User',
            'page_title' => 'Edit User',
            'user' => $user,
            'action' => 'edit',
            'atasanList' => $atasanList
        ];

        $this->view('user/form', $data);
    }

    public function save() {
        requireAdmin();

        $action = $_POST['action'];
        $data = [
            'username' => cleanInput($_POST['username']),
            'nama' => cleanInput($_POST['nama']),
            'nip' => cleanInput($_POST['nip']),
            'email' => !empty($_POST['email']) ? cleanInput($_POST['email']) : null,
            'jabatan' => cleanInput($_POST['jabatan']),
            'golongan' => cleanInput($_POST['golongan']),
            'unit_kerja' => cleanInput($_POST['unit_kerja']),
            'atasan' => !empty($_POST['atasan']) ? cleanInput($_POST['atasan']) : null,
            'user_type' => cleanInput($_POST['user_type'])
        ];

        // Validasi NIP
        if (strlen($data['nip']) < 14) {
            $this->jsonResponse(['success' => false, 'message' => 'NIP harus minimal 14 digit']);
            return;
        }

        // Ekstrak tanggal masuk dari NIP
        $data['tanggal_masuk'] = extractTanggalMasukFromNIP($data['nip']);
        
        // Validasi tanggal masuk
        if ($data['tanggal_masuk'] == '1900-01-01') {
            $this->jsonResponse(['success' => false, 'message' => 'NIP tidak valid. Pastikan digit 9-14 berisi tahun dan bulan yang valid']);
            return;
        }

        // Validasi email jika diisi
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(['success' => false, 'message' => 'Format email tidak valid']);
            return;
        }

        // Definisikan userId untuk action edit
        $userId = null;
        if ($action == 'edit') {
            $userId = cleanInput($_POST['id']);
        }

        // Validasi duplikasi username
        if ($this->userModel->exists('username', $data['username'], $userId)) {
            $this->jsonResponse(['success' => false, 'message' => 'Username sudah digunakan']);
            return;
        }

        // Validasi duplikasi NIP
        if ($this->userModel->exists('nip', $data['nip'], $userId)) {
            $this->jsonResponse(['success' => false, 'message' => 'NIP sudah terdaftar']);
            return;
        }

        // Validasi duplikasi email jika diisi
        if (!empty($data['email']) && $this->userModel->exists('email', $data['email'], $userId)) {
            $this->jsonResponse(['success' => false, 'message' => 'Email sudah digunakan']);
            return;
        }
        
        // Validasi atasan jika dipilih - memastikan atasan ID valid
        if (!empty($data['atasan'])) {
            $db = Database::getInstance();
            $atasanExists = $db->fetch("SELECT id_atasan FROM atasan WHERE id_atasan = ?", [$data['atasan']]);
            
            if (!$atasanExists) {
                $this->jsonResponse(['success' => false, 'message' => 'Atasan yang dipilih tidak valid. Silakan pilih atasan yang terdaftar atau update data atasan terlebih dahulu']);
                return;
            }
        }

        // Auto-detect atasan untuk pegawai dan atasan berdasarkan jabatan
        // Jika user_type adalah pegawai atau atasan, tentukan atasannya otomatis dari jabatan
        if ($data['user_type'] == 'pegawai' || $data['user_type'] == 'atasan') {
            $autoSuperiorId = getAutomaticDirectSuperior($data['jabatan']);
            // Override atasan dengan yang otomatis ditentukan
            if ($autoSuperiorId !== null) {
                $data['atasan'] = $autoSuperiorId;
            } else {
                // Jika tidak ada atasan yang cocok, set ke NULL
                $data['atasan'] = null;
            }
        } elseif ($data['user_type'] == 'admin') {
            // Admin biasanya tidak punya atasan (NULL)
            $data['atasan'] = null;
        }

        if ($action == 'add') {
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $userId = $this->userModel->create($data);
            
            // Jika user adalah pegawai atau atasan, buat kuota cuti awal
            if (($data['user_type'] == 'pegawai' || $data['user_type'] == 'atasan') && $userId) {
                $this->createInitialQuota($userId);
            }
            
            // Jika user adalah atasan, tambahkan ke tabel atasan
            if ($data['user_type'] == 'atasan' && $userId) {
                $this->syncAtasanToDatabase($data['nama'], $data['nip'], $data['jabatan']);
            }
            
            $message = 'User berhasil ditambahkan';
        } else {
            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            // Get old user data untuk cek perubahan user_type
            $oldUser = $this->userModel->getUserWithAtasan($userId);
            
            // Handle user status for edit action
            if (isset($_POST['user_status'])) {
                $userStatus = cleanInput($_POST['user_status']);
                if ($userStatus === 'inactive') {
                    $data['is_deleted'] = 1;
                    $data['deleted_at'] = date('Y-m-d H:i:s');
                } else {
                    $data['is_deleted'] = 0;
                    $data['deleted_at'] = null;
                }
            }
            
            $this->userModel->update($userId, $data);
            
            // Handle perubahan user_type dari/ke atasan
            if ($oldUser && $oldUser['user_type'] !== $data['user_type']) {
                // Jika berubah menjadi atasan
                if ($data['user_type'] == 'atasan') {
                    $this->syncAtasanToDatabase($data['nama'], $data['nip'], $data['jabatan']);
                }
                // Jika berubah dari atasan ke admin atau pegawai
                elseif ($oldUser['user_type'] == 'atasan') {
                    $this->removeAtasanFromDatabase($oldUser['nip']);
                }
            }
            // Jika user_type tetap atasan, update data atasan jika ada perubahan
            elseif ($data['user_type'] == 'atasan') {
                $this->updateAtasanInDatabase($oldUser['nip'], $data['nama'], $data['nip'], $data['jabatan']);
            }
            
            $message = 'User berhasil diupdate';
        }

        $this->jsonResponse(['success' => true, 'message' => $message]);
    }
    
    /**
     * Tambahkan data atasan ke tabel atasan
     */
    private function syncAtasanToDatabase($nama, $nip, $jabatan) {
        $db = Database::getInstance();
        
        // Cek apakah data atasan dengan NIP ini sudah ada
        $existingAtasan = $db->fetch("SELECT id_atasan FROM atasan WHERE NIP = ?", [$nip]);
        
        if (!$existingAtasan) {
            // Jika belum ada, INSERT data baru
            $db->execute(
                "INSERT INTO atasan (nama_atasan, NIP, jabatan) VALUES (?, ?, ?)",
                [$nama, $nip, $jabatan]
            );
        }
    }
    
    /**
     * Hapus data atasan dari tabel atasan
     */
    private function removeAtasanFromDatabase($nip) {
        $db = Database::getInstance();
        $db->execute("DELETE FROM atasan WHERE NIP = ?", [$nip]);
    }
    
    /**
     * Update data atasan di tabel atasan
     */
    private function updateAtasanInDatabase($oldNip, $newNama, $newNip, $newJabatan) {
        $db = Database::getInstance();
        
        // Jika NIP berubah, delete yang lama dan insert yang baru
        if ($oldNip !== $newNip) {
            $db->execute("DELETE FROM atasan WHERE NIP = ?", [$oldNip]);
            $db->execute(
                "INSERT INTO atasan (nama_atasan, NIP, jabatan) VALUES (?, ?, ?)",
                [$newNama, $newNip, $newJabatan]
            );
        } else {
            // Jika NIP sama, hanya update nama dan jabatan
            $db->execute(
                "UPDATE atasan SET nama_atasan = ?, jabatan = ? WHERE NIP = ?",
                [$newNama, $newJabatan, $oldNip]
            );
        }
    }
    
    /**
     * Buat kuota cuti awal untuk user baru
     */
    private function createInitialQuota($userId) {
        // Kuota default untuk user baru
        $currentYear = date('Y');
        $defaultQuotas = [
            ($currentYear - 2) => ['kuota_tahunan' => 0, 'sisa_kuota' => 0],
            ($currentYear - 1) => ['kuota_tahunan' => 6, 'sisa_kuota' => 6],
            $currentYear => ['kuota_tahunan' => 12, 'sisa_kuota' => 12]
        ];

        foreach ($defaultQuotas as $year => $quota) {
            // Hapus data kuota tahun ini jika sudah ada (untuk memastikan tidak double)
            $this->db()->query("DELETE FROM leave_balances WHERE user_id = ? AND tahun = ?", [$userId, $year]);
            // Buat kuota baru dengan nilai default
            $this->leaveBalanceModel->create([
                'user_id' => $userId,
                'tahun' => $year,
                'kuota_tahunan' => $quota['kuota_tahunan'],
                'sisa_kuota' => $quota['sisa_kuota']
            ]);
        }
        
        // Buat kuota untuk jenis cuti lainnya
        createAllInitialQuota($userId);
    }
    
    /**
     * Buat data awal kuota cuti sakit untuk semua user yang belum memilikinya
     */
    public function createInitialKuotaSakit() {
        requireAdmin();
        
        $db = Database::getInstance();
        $tahun = date('Y');
        
        // Ambil semua user yang belum memiliki data kuota cuti sakit
        $sql = "SELECT u.id, u.nama FROM users u 
                WHERE u.user_type IN ('pegawai', 'atasan')
                AND u.id NOT IN (
                    SELECT DISTINCT user_id FROM kuota_cuti_sakit WHERE tahun = ?
                )";
        
        $users = $db->fetchAll($sql, [$tahun]);
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($users as $user) {
            $kuotaDefault = getKuotaFromLeaveType(3); // ID 3 untuk cuti sakit
            
            $sql = "INSERT INTO kuota_cuti_sakit 
                    (user_id, leave_type_id, tahun, kuota_tahunan, sisa_kuota) 
                    VALUES (?, 3, ?, ?, ?)";
            
            if ($db->execute($sql, [$user['id'], $tahun, $kuotaDefault, $kuotaDefault])) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }
        
        $this->jsonResponse([
            'success' => true,
            'message' => "Berhasil membuat data kuota cuti sakit untuk {$successCount} user. Error: {$errorCount}",
            'data' => [
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'total_processed' => count($users)
            ]
        ]);
    }

    public function delete() {
        requireAdmin();

        $id = cleanInput($_POST['id']);
        
        // Validasi user exists
        $user = $this->userModel->find($id);
        if (!$user) {
            $this->jsonResponse(['success' => false, 'message' => 'User tidak ditemukan']);
            return;
        }
        
        // Cek apakah user adalah admin
        if ($user['user_type'] === 'admin') {
            $this->jsonResponse(['success' => false, 'message' => 'Tidak dapat menghapus user admin']);
            return;
        }
        
        try {
            // Mulai transaction
            $this->db()->beginTransaction();
            
            // Hapus data terkait dalam urutan yang benar:
            
            // 1. Hapus leave_documents yang dibuat oleh user ini
            $this->db()->query("DELETE FROM leave_documents WHERE created_by = ?", [$id]);
            
            // 2. Update leave_requests yang approved_by user ini
            $this->db()->query("UPDATE leave_requests SET approved_by = NULL WHERE approved_by = ?", [$id]);
            
            // 3. Hapus user (leave_balances, leave_requests, notifications akan terhapus otomatis karena CASCADE)
            $this->userModel->delete($id);
            
            // Commit transaction
            $this->db()->commit();
            
            $this->jsonResponse(['success' => true, 'message' => 'User berhasil dihapus']);
            
        } catch (Exception $e) {
            // Rollback jika terjadi error
            try {
                $this->db()->rollback();
            } catch (Exception $rollbackException) {
                error_log("Rollback failed: " . $rollbackException->getMessage());
            }
            
            error_log("Error deleting user ID {$id}: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Gagal menghapus user: ' . $e->getMessage()]);
        }
    }

    /**
     * Sinkronisasi atasan otomatis untuk semua pegawai berdasarkan jabatan
     * Endpoint: POST user/syncAutoAtasan
     * POST params:
     *   mode = 'only_null'  -> hanya update user yang atasannya masih NULL
     *          'all'        -> update semua pegawai (override atasan yang sudah ada)
     */
    public function syncAutoAtasan() {
        requireAdmin();

        $db = Database::getInstance();
        $mode = isset($_POST['mode']) ? cleanInput($_POST['mode']) : 'only_null';

        // Ambil daftar user yang perlu diproses (pegawai dan atasan, not admin)
        if ($mode === 'all') {
            $sql = "SELECT id, nama, jabatan, user_type FROM users WHERE user_type IN ('pegawai', 'atasan') AND is_deleted = 0";
            $users = $db->fetchAll($sql);
        } else {
            // Hanya yang atasannya NULL
            $sql = "SELECT id, nama, jabatan, user_type FROM users WHERE user_type IN ('pegawai', 'atasan') AND atasan IS NULL AND is_deleted = 0";
            $users = $db->fetchAll($sql);
        }

        $results = [];
        $successCount = 0;
        $noMatchCount = 0;
        $errorCount = 0;

        foreach ($users as $user) {
            $userId   = $user['id'];
            $jabatan  = $user['jabatan'];
            $nama     = $user['nama'];

            try {
                $atasanId = getAutomaticDirectSuperior($jabatan);

                if ($atasanId !== null) {
                    // Ambil info atasan untuk laporan
                    $atasanInfo = $db->fetch("SELECT nama_atasan, jabatan FROM atasan WHERE id_atasan = ?", [$atasanId]);
                    $db->execute("UPDATE users SET atasan = ? WHERE id = ?", [$atasanId, $userId]);

                    $results[] = [
                        'status'        => 'berhasil',
                        'user_id'       => $userId,
                        'nama'          => $nama,
                        'jabatan'       => $jabatan,
                        'atasan_baru'   => $atasanInfo['nama_atasan'] ?? '-',
                        'jabatan_atasan'=> $atasanInfo['jabatan'] ?? '-',
                        'keterangan'    => 'Atasan berhasil ditetapkan'
                    ];
                    $successCount++;
                } else {
                    $results[] = [
                        'status'        => 'tidak_cocok',
                        'user_id'       => $userId,
                        'nama'          => $nama,
                        'jabatan'       => $jabatan,
                        'atasan_baru'   => '-',
                        'jabatan_atasan'=> '-',
                        'keterangan'    => 'Jabatan tidak cocok dengan aturan hierarki'
                    ];
                    $noMatchCount++;
                }
            } catch (Exception $e) {
                $results[] = [
                    'status'        => 'error',
                    'user_id'       => $userId,
                    'nama'          => $nama,
                    'jabatan'       => $jabatan,
                    'atasan_baru'   => '-',
                    'jabatan_atasan'=> '-',
                    'keterangan'    => 'Error: ' . $e->getMessage()
                ];
                $errorCount++;
                error_log("[syncAutoAtasan] Error processing user ID {$userId}: " . $e->getMessage());
            }
        }

        $this->jsonResponse([
            'success' => true,
            'message' => "Sinkronisasi selesai. Berhasil: {$successCount}, Tidak cocok: {$noMatchCount}, Error: {$errorCount}",
            'data' => [
                'total_diproses'  => count($users),
                'berhasil'        => $successCount,
                'tidak_cocok'     => $noMatchCount,
                'error'           => $errorCount,
                'mode'            => $mode,
                'results'         => $results
            ]
        ]);
    }

    public function db() {
        return Database::getInstance();
    }

    public function getUserQuota() {
        requireAdmin();
        
        $userId = cleanInput($_POST['user_id']);
        
        $quotas = [];
        
        // Get quota for specific years
        $currentYear = date('Y');
        $years = [$currentYear - 2, $currentYear - 1, $currentYear];
        foreach ($years as $year) {
            $balance = $this->leaveBalanceModel->getBalance($userId, $year);
            $quotas[] = [
                'tahun' => $year,
                'kuota_tahunan' => $balance['kuota_tahunan'] ?? 12,
                'sisa_kuota' => $balance['sisa_kuota'] ?? 12
            ];
        }
        
        $this->jsonResponse(['success' => true, 'data' => $quotas]);
    }

    public function updateQuota() {
        requireAdmin();
        
        $userId = cleanInput($_POST['user_id']);
        $tahun = cleanInput($_POST['tahun']);
        $kuotaTahunan = (int)cleanInput($_POST['kuota_tahunan']);
        $sisaKuota = (int)cleanInput($_POST['sisa_kuota']);
        
        // Validate user exists and is a regular user
        $user = $this->userModel->find($userId);
        if (!$user) {
            $this->jsonResponse(['success' => false, 'message' => 'User tidak ditemukan']);
            return;
        }
        
        if ($user['user_type'] !== 'pegawai' && $user['user_type'] !== 'atasan') {
            $this->jsonResponse(['success' => false, 'message' => 'Hanya user pegawai atau atasan yang dapat diatur kuotanya']);
            return;
        }
        
        // Validate year
        $currentYear = date('Y');
        $allowedYears = [$currentYear - 2, $currentYear - 1, $currentYear];
        if (!in_array($tahun, $allowedYears)) {
            $this->jsonResponse(['success' => false, 'message' => 'Tahun tidak valid. Hanya dapat mengatur kuota untuk 3 tahun terakhir']);
            return;
        }
        
        // Validation
        if ($sisaKuota > $kuotaTahunan) {
            $this->jsonResponse(['success' => false, 'message' => 'Sisa kuota tidak boleh lebih dari kuota tahunan']);
            return;
        }
        
        if ($kuotaTahunan < 0 || $sisaKuota < 0) {
            $this->jsonResponse(['success' => false, 'message' => 'Kuota tidak boleh negatif']);
            return;
        }
        
        // Check if balance exists, if not create it
        $existingBalance = $this->leaveBalanceModel->getBalance($userId, $tahun);
        
        if ($existingBalance) {
            // Update existing balance
            $db = Database::getInstance();
            $db->query(
                "UPDATE leave_balances SET kuota_tahunan = ?, sisa_kuota = ? WHERE user_id = ? AND tahun = ?",
                [$kuotaTahunan, $sisaKuota, $userId, $tahun]
            );
        } else {
            // Create new balance
            $this->leaveBalanceModel->create([
                'user_id' => $userId,
                'tahun' => $tahun,
                'kuota_tahunan' => $kuotaTahunan,
                'sisa_kuota' => $sisaKuota
            ]);
        }
        
        // Log the action for audit trail
        $adminName = $_SESSION['nama'] ?? 'Admin';
        $userName = $user['nama'];
        error_log("Admin {$adminName} updated quota for user {$userName} (ID: {$userId}) - Year: {$tahun}, Annual Quota: {$kuotaTahunan}, Remaining Quota: {$sisaKuota}");
        
        $this->jsonResponse(['success' => true, 'message' => 'Kuota berhasil diupdate']);
    }

    public function getQuotaHistory() {
        requireAdmin();
        
        $userId = cleanInput($_POST['user_id']);
        
        // Get user info
        $user = $this->userModel->find($userId);
        if (!$user) {
            $this->jsonResponse(['success' => false, 'message' => 'User tidak ditemukan']);
            return;
        }
        
        // Get quota history from model
        $history = $this->leaveBalanceModel->getQuotaHistory($userId);
        
        $this->jsonResponse([
            'success' => true, 
            'data' => [
                'user' => $user,
                'history' => $history
            ]
        ]);
    }

    public function exportQuota() {
        requireAdmin();
        
        $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
        
        // Get all users with their quota
        $sql = "SELECT u.nama, u.nip, u.jabatan, u.golongan, u.unit_kerja, 
                       COALESCE(lb.sisa_cuti, 0) as sisa_cuti,
                       COALESCE(lb.cuti_diambil, 0) as cuti_diambil,
                       COALESCE(lb.total_quota, 0) as total_quota
                FROM users u
                LEFT JOIN leave_balances lb ON u.id = lb.user_id AND lb.tahun = ?
                WHERE u.user_type IN ('pegawai', 'atasan')
                ORDER BY u.id";
        
        $users = $this->db()->fetchAll($sql, [$year]);
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="quota_cuti_' . $year . '.csv"');
        
        // Create output stream
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Add headers
        fputcsv($output, ['Nama', 'NIP', 'Jabatan', 'Golongan', 'Unit Kerja', 'Total Quota', 'Cuti Diambil', 'Sisa Cuti']);
        
        // Add data
        foreach ($users as $user) {
            fputcsv($output, [
                $user['nama'],
                $user['nip'],
                $user['jabatan'],
                $user['golongan'],
                $user['unit_kerja'],
                $user['total_quota'],
                $user['cuti_diambil'],
                $user['sisa_cuti']
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function importCSV() {
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Method tidak diizinkan'
            ]);
            return;
        }
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'File tidak ditemukan atau error saat upload'
            ]);
            return;
        }
        $file = $_FILES['csv_file'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExtension !== 'csv') {
            $this->jsonResponse([
                'success' => false,
                'message' => 'File harus berformat CSV'
            ]);
            return;
        }
        
        // Baca file untuk deteksi delimiter
        $fileContent = file_get_contents($fileTmpName);
        $delimiter = $this->detectDelimiter($fileContent);
        
        $handle = fopen($fileTmpName, 'r');
        if (!$handle) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal membaca file CSV'
            ]);
            return;
        }
        
        $results = [
            'success' => [],
            'error' => [],
            'total_processed' => 0,
            'imported_data' => [] // Tambahan untuk menyimpan data yang diimport
        ];
        $rowNumber = 0;
        $headerFound = false;
        $headerMapping = [];
        
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowNumber++;
            
            // Skip baris kosong atau baris yang hanya berisi spasi/semicolon
            if (empty(array_filter($data, function($cell) { return trim($cell) !== ''; }))) {
                continue;
            }
            
            // Cari header yang sesuai
            if (!$headerFound) {
                $headerMapping = $this->mapCSVHeaders($data);
                if (!empty($headerMapping)) {
                    $headerFound = true;
                    continue; // Skip header row
                } else {
                    // Jika belum menemukan header, lanjutkan ke baris berikutnya
                    continue;
                }
            }
            
            // Jika jumlah kolom data kurang dari mapping, skip
            if (count($data) < count($headerMapping)) {
                continue;
            }
            
            // Proses data
            $result = $this->processCSVRow($data, $headerMapping, $rowNumber);
            $results['total_processed']++;
            if ($result['success']) {
                $results['success'][] = $result['message'];
                // Simpan data yang berhasil diimport
                if (isset($result['imported_data'])) {
                    $results['imported_data'][] = $result['imported_data'];
                }
            } else {
                $results['error'][] = $result['message'];
            }
        }
        
        fclose($handle);
        $successCount = count($results['success']);
        $errorCount = count($results['error']);
        
        if ($errorCount === 0) {
            $message = "Berhasil import {$successCount} data user";
        } else {
            $message = "Import selesai. Berhasil: {$successCount}, Gagal: {$errorCount}";
        }
        
        $this->jsonResponse([
            'success' => true,
            'message' => $message,
            'details' => $results
        ]);
    }
    
    private function detectDelimiter($content) {
        // Deteksi delimiter berdasarkan konten file
        $lines = explode("\n", $content);
        $firstLine = $lines[0] ?? '';
        
        // Hitung kemunculan delimiter yang umum
        $delimiters = [',', ';', "\t"];
        $counts = [];
        
        foreach ($delimiters as $delimiter) {
            $counts[$delimiter] = substr_count($firstLine, $delimiter);
        }
        
        // Pilih delimiter dengan jumlah terbanyak
        $maxCount = max($counts);
        $detectedDelimiter = array_search($maxCount, $counts);
        
        // Jika tidak ada delimiter yang terdeteksi, gunakan comma sebagai default
        return $detectedDelimiter ?: ',';
    }
    
    private function mapCSVHeaders($headers) {
        $mapping = [];
        // Normalisasi header dari file CSV
        $normalizedHeaders = array_map(function($h) {
            // Hapus semua karakter non-alfanumerik dan spasi
            $h = preg_replace('/[^a-zA-Z0-9\s]/', '', $h);
            $h = preg_replace('/\s+/', '', $h); // Hapus semua spasi
            return strtolower($h);
        }, $headers);
        // Mapping yang diharapkan dengan berbagai kemungkinan format
        $expectedMappings = [
            'nama' => ['nama', 'namapegawai', 'namalengkap'],
            'nipnrp' => ['nipnrp', 'nip', 'nrp', 'nomorindukpegawai'],
            'jabatan' => ['jabatan', 'posisi', 'kedudukan'],
            'gol' => ['gol', 'golongan', 'pangkat'],
            'satker' => ['satker', 'unitkerja', 'unit', 'kantor', 'instansi'],
            // Tambahan kolom opsional:
            'sisa_kuota' => ['sisakuota', 'sisa_kuota', 'sisa cuti', 'sisa']
        ];
        // Cari mapping untuk setiap kolom yang diperlukan dan opsional
        foreach ($normalizedHeaders as $i => $header) {
            foreach ($expectedMappings as $key => $possibleValues) {
                if (in_array($header, $possibleValues)) {
                    $mapping[$key] = $i;
                    break;
                }
            }
        }
        // Pastikan semua kolom yang diperlukan ditemukan
        $requiredColumns = ['nama', 'nipnrp', 'jabatan', 'satker'];
        $foundColumns = array_keys($mapping);
        foreach ($requiredColumns as $required) {
            if (!in_array($required, $foundColumns)) {
                return []; // Jika ada kolom wajib yang tidak ditemukan
            }
        }
        return $mapping;
    }
    
    private function processCSVRow($data, $headerMapping, $rowNumber) {
        try {
            // Ambil data sesuai mapping
            $nama = trim($data[$headerMapping['nama']] ?? '');
            $nip = trim($data[$headerMapping['nipnrp']] ?? '');
            $jabatan = trim($data[$headerMapping['jabatan']] ?? '');
            $golongan = trim($data[$headerMapping['gol']] ?? '');
            $satker_nama = trim($data[$headerMapping['satker']] ?? '');
            // Ambil sisa_kuota jika ada di mapping dan data
            $sisa_kuota = null;
            if (isset($headerMapping['sisa_kuota'])) {
                $sisa_kuota = trim($data[$headerMapping['sisa_kuota']] ?? '');
                if ($sisa_kuota === '') $sisa_kuota = null;
            }
            // Validasi data wajib
            if (empty($nama) || empty($nip) || empty($jabatan) || empty($satker_nama)) {
                return [
                    'success' => false,
                    'message' => "Baris {$rowNumber}: Data Nama, NIP, Jabatan, dan SATKER wajib diisi"
                ];
            }
            // --- KONVERSI NIP DARI NOTASI ILMIAH JIKA PERLU ---
            if (stripos($nip, 'e+') !== false || stripos($nip, 'E+') !== false) {
                $nip = preg_replace('/[,.]/', '', $nip);
                $nip = number_format((float)$nip, 0, '', '');
            }
            $nip = preg_replace('/\D/', '', $nip);
            // --- END KONVERSI NIP ---

            // Cari id_satker berdasarkan nama satker (case-insensitive, trim)

            $satkerModel = new \Satker();
            $id_satker = $satkerModel->getIdByNamaSatker($satker_nama);
            if (!$id_satker) {
                return [
                    'success' => false,
                    'message' => "Baris {$rowNumber}: Nama SATKER '{$satker_nama}' tidak ditemukan di database"
                ];
            }

            // Cek apakah user sudah ada berdasarkan NIP
            $existingUserByNIP = $this->db()->fetch("SELECT id, username, unit_kerja, jabatan FROM users WHERE nip = ?", [$nip]);

            if ($existingUserByNIP) {
                // User sudah ada berdasarkan NIP, lakukan update data
                return $this->updateExistingUser($existingUserByNIP, $nama, $nip, $jabatan, $golongan, $id_satker, $rowNumber);
            } else {
                // User baru, lakukan insert (meskipun jabatan/unit_kerja sama, selama NIP beda, buat akun baru)
                return $this->createNewUser($nama, $nip, $jabatan, $golongan, $id_satker, $sisa_kuota, $rowNumber);
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Baris {$rowNumber}: Error - " . $e->getMessage()
            ];
        }
    }
    
    /**
     * Update data user yang sudah ada saat berpindah unit kerja
     */
    private function updateExistingUser($existingUser, $nama, $nip, $jabatan, $golongan, $satker, $rowNumber) {
        $oldUnitKerja = $existingUser['unit_kerja'];
        $oldJabatan = $existingUser['jabatan'];
        $oldNIP = $existingUser['nip'];
        $oldUsername = $existingUser['username'];
        $userId = $existingUser['id'];
        
        // Generate username ideal berdasarkan unit kerja baru
        $idealUsername = $this->generateBaseUsername($satker);
        
        // Jika unit kerja berubah, cek dan handle username
        if ($oldUnitKerja !== $satker) {
            // Cek apakah username ideal sudah ada (exclude user yang sedang diupdate)
            $usernameExists = $this->isUsernameExists($idealUsername, $userId);
            
            if ($usernameExists) {
                // Username ideal sudah ada, generate dengan suffix
                $newUsername = $this->generateUsername($satker, $userId);
            } else {
                // Username ideal tersedia, gunakan itu
                $newUsername = $idealUsername;
            }
            
            // Kosongkan username lama agar bisa digunakan user lain
            $this->clearOldUsernameForReuse($oldUsername, $userId);
        } else {
            // Unit kerja tidak berubah, username tetap sama
            $newUsername = $oldUsername;
        }
        
        // Jika unit kerja berubah, pindahkan semua kuota cuti
        if ($oldUnitKerja !== $satker) {
            $this->moveUserQuotaToNewUnit($userId, $oldUnitKerja, $satker);
            // Catat log perpindahan unit kerja
            $this->logUnitKerjaTransfer($userId, $nama, $nip, $oldUnitKerja, $satker);
        }
        
        // Update data user
        $updateData = [
            'nama' => $nama,
            'jabatan' => $jabatan,
            'golongan' => $golongan,
            'unit_kerja' => $satker
        ];
        
        // Update username jika unit kerja berubah
        if ($oldUnitKerja !== $satker) {
            // Username perlu diupdate
            $updateData['username'] = $newUsername;
        }
        
        // Update tanggal masuk dari digit 9-14 NIP jika berubah
        if (strlen($nip) >= 14) {
            $tahun = substr($nip, 8, 4);
            $bulan = substr($nip, 12, 2);
            if (is_numeric($tahun) && is_numeric($bulan) && (int)$bulan >= 1 && (int)$bulan <= 12) {
                $updateData['tanggal_masuk'] = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01';
            }
        }
        
        $success = $this->userModel->update($userId, $updateData);
        

        
        if ($success) {
            $action = "";
            if ($oldUnitKerja !== $satker) {
                if ($oldUsername !== $newUsername) {
                    $action = "dipindahkan dari {$oldUnitKerja} ke {$satker} (username: {$oldUsername} → {$newUsername})";
                } else {
                    $action = "dipindahkan dari {$oldUnitKerja} ke {$satker}";
                }
            } elseif ($oldNIP !== $nip) {
                $action = "NIP diupdate dari {$oldNIP} ke {$nip}";
            } elseif ($oldJabatan !== $jabatan) {
                $action = "jabatan diupdate dari {$oldJabatan} ke {$jabatan}";
            } else {
                $action = "diupdate";
            }
            
            require_once __DIR__ . '/../helpers/satker_helper.php';
            return [
                'success' => true,
                'message' => "Baris {$rowNumber}: Berhasil update {$nama} ({$action})",
                'imported_data' => [
                    'nama' => $nama,
                    'nip' => $nip,
                    'jabatan' => $jabatan,
                    'golongan' => $golongan,
                    'unit_kerja' => $satker,
                    'nama_satker' => get_nama_satker($satker),
                    'username' => $newUsername,
                    'action' => $action,
                    'row_number' => $rowNumber,
                    'old_unit_kerja' => get_nama_satker($oldUnitKerja),
                    'old_jabatan' => $oldJabatan,
                    'old_username' => $oldUsername
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => "Baris {$rowNumber}: Gagal update data {$nama}"
            ];
        }
    }
    
    /**
     * Timpa data user yang sudah ada dengan data baru (untuk perpindahan unit kerja)
     */
    private function overwriteExistingUser($existingUser, $nama, $nip, $jabatan, $golongan, $satker, $rowNumber) {
        $oldNama = $existingUser['nama'];
        $oldNIP = $existingUser['nip'];
        $oldJabatan = $existingUser['jabatan'];
        $oldUsername = $existingUser['username'];
        $userId = $existingUser['id'];
        
        // Username tetap mengikuti unit kerja yang sudah ada
        $newUsername = $oldUsername;
        
        // Update tanggal masuk dari digit 9-14 NIP jika berubah
        $updateData = [
            'nama' => $nama,
            'jabatan' => $jabatan,
            'golongan' => $golongan,
            'unit_kerja' => $satker
        ];
        
        // Update tanggal masuk dari digit 9-14 NIP
        if (strlen($nip) >= 14) {
            $tahun = substr($nip, 8, 4);
            $bulan = substr($nip, 12, 2);
            if (is_numeric($tahun) && is_numeric($bulan) && (int)$bulan >= 1 && (int)$bulan <= 12) {
                $updateData['tanggal_masuk'] = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01';
            }
        }
        
        $success = $this->userModel->update($userId, $updateData);
        
        if ($success) {
            // Catat log penimpaan data
            $this->logDataOverwrite($userId, $nama, $nip, $oldNama, $oldNIP);
            
            $action = "data ditimpa ({$oldNama} → {$nama}, {$oldNIP} → {$nip})";
            
            require_once __DIR__ . '/../helpers/satker_helper.php';
            return [
                'success' => true,
                'message' => "Baris {$rowNumber}: Berhasil timpa data {$nama} ({$action})",
                'imported_data' => [
                    'nama' => $nama,
                    'nip' => $nip,
                    'jabatan' => $jabatan,
                    'golongan' => $golongan,
                    'unit_kerja' => $satker,
                    'nama_satker' => get_nama_satker($satker),
                    'username' => $newUsername,
                    'action' => $action,
                    'row_number' => $rowNumber,
                    'old_nama' => $oldNama,
                    'old_nip' => $oldNIP,
                    'old_jabatan' => $oldJabatan,
                    'old_username' => $oldUsername
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => "Baris {$rowNumber}: Gagal timpa data {$nama}"
            ];
        }
    }
    
    /**
     * Buat user baru
     */
    private function createNewUser($nama, $nip, $jabatan, $golongan, $satker, $sisa_kuota, $rowNumber) {
        // Gunakan NIP sebagai username untuk user baru
        $username = $nip;
        // Pastikan username (NIP) belum dipakai user lain
        $baseUsername = $username;
        $counter = 1;
        while ($this->isUsernameExists($username)) {
            // Jika NIP sudah dipakai, tambahkan angka di belakang (sangat jarang terjadi)
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        // Cek duplikasi NIP (safeguard)
        $existingUserByNIP = $this->db()->fetch("SELECT id FROM users WHERE nip = ?", [$nip]);
        if ($existingUserByNIP) {
            return [
                'success' => false,
                'message' => "Baris {$rowNumber}: NIP '{$nip}' sudah terdaftar"
            ];
        }
        
        // Ambil tanggal masuk dari digit 9-14 NIP (format YYYYMM)
        $tanggalMasuk = '1900-01-01';
        if (strlen($nip) >= 14) {
            $tahun = substr($nip, 8, 4);
            $bulan = substr($nip, 12, 2);
            if (is_numeric($tahun) && is_numeric($bulan) && (int)$bulan >= 1 && (int)$bulan <= 12) {
                $tanggalMasuk = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01';
            }
        }
        
        // Hash password default
        $hashedPassword = password_hash('password', PASSWORD_DEFAULT);
        
        // Insert user
        $userId = $this->userModel->create([
            'username' => $username,
            'password' => $hashedPassword,
            'nama' => $nama,
            'nip' => $nip,
            'jabatan' => $jabatan,
            'golongan' => $golongan,
            'unit_kerja' => $satker,
            'user_type' => 'pegawai',
            'tanggal_masuk' => $tanggalMasuk
        ]);
        
        if ($userId) {
            $this->createInitialQuota($userId);
            // Jika ada sisa_kuota di CSV, update leave_balances tahun berjalan (2025)
            if ($sisa_kuota !== null && is_numeric($sisa_kuota)) {
                $tahunSekarang = date('Y');
                $this->leaveBalanceModel->updateBalance($userId, $tahunSekarang, (int)$sisa_kuota);
            }
            require_once __DIR__ . '/../helpers/satker_helper.php';
            return [
                'success' => true,
                'message' => "Baris {$rowNumber}: Berhasil import {$nama} ({$username})",
                'imported_data' => [
                    'nama' => $nama,
                    'nip' => $nip,
                    'jabatan' => $jabatan,
                    'golongan' => $golongan,
                    'unit_kerja' => $satker,
                    'nama_satker' => get_nama_satker($satker),
                    'username' => $username,
                    'action' => 'Tambah Baru',
                    'row_number' => $rowNumber
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => "Baris {$rowNumber}: Gagal menyimpan data {$nama}"
            ];
        }
    }
    
    /**
     * Pindahkan semua kuota cuti user ke unit kerja baru
     */
    private function moveUserQuotaToNewUnit($userId, $oldUnitKerja, $newUnitKerja) {
        $db = Database::getInstance();
        
        // 1. Pindahkan kuota cuti tahunan (leave_balances)
        $this->moveLeaveBalances($userId);
        
        // 2. Pindahkan kuota cuti sakit
        $this->moveKuotaCutiSakit($userId);
        
        // 3. Pindahkan kuota cuti besar
        $this->moveKuotaCutiBesar($userId);
        
        // 4. Pindahkan kuota cuti melahirkan
        $this->moveKuotaCutiMelahirkan($userId);
        
        // 5. Pindahkan kuota cuti alasan penting
        $this->moveKuotaCutiAlasanPenting($userId);
        
        // 6. Pindahkan kuota cuti luar tanggungan
        $this->moveKuotaCutiLuarTanggungan($userId);
    }
    
    /**
     * Pindahkan kuota cuti tahunan
     */
    private function moveLeaveBalances($userId) {
        // Kuota cuti tahunan tidak perlu dipindahkan karena sudah terkait dengan user_id
        // Hanya perlu memastikan data tetap ada
        $currentYear = date('Y');
        $years = [$currentYear - 2, $currentYear - 1, $currentYear];
        foreach ($years as $year) {
            $existing = $this->db()->fetch("SELECT id FROM leave_balances WHERE user_id = ? AND tahun = ?", [$userId, $year]);
            if (!$existing) {
                // Buat kuota default jika tidak ada
                $this->leaveBalanceModel->create([
                    'user_id' => $userId,
                    'tahun' => $year,
                    'kuota_tahunan' => 12,
                    'sisa_kuota' => 12
                ]);
            }
        }
    }
    
    /**
     * Pindahkan kuota cuti sakit
     */
    private function moveKuotaCutiSakit($userId) {
        // Kuota cuti sakit tidak perlu dipindahkan karena sudah terkait dengan user_id
        // Hanya perlu memastikan data tetap ada
        $currentYear = date('Y');
        $years = [$currentYear - 2, $currentYear - 1, $currentYear];
        foreach ($years as $year) {
            $existing = $this->db()->fetch("SELECT id FROM kuota_cuti_sakit WHERE user_id = ? AND tahun = ?", [$userId, $year]);
            if (!$existing) {
                // Buat kuota default jika tidak ada
                $this->db()->execute(
                    "INSERT INTO kuota_cuti_sakit (user_id, leave_type_id, tahun, kuota_tahunan, sisa_kuota) VALUES (?, 3, ?, 12, 12)",
                    [$userId, $year]
                );
            }
        }
    }
    
    /**
     * Pindahkan kuota cuti besar
     */
    private function moveKuotaCutiBesar($userId) {
        // Kuota cuti besar tidak perlu dipindahkan karena sudah terkait dengan user_id
        // Hanya perlu memastikan data tetap ada
        $existing = $this->db()->fetch("SELECT id FROM kuota_cuti_besar WHERE user_id = ?", [$userId]);
        if (!$existing) {
            // Buat kuota default jika tidak ada
            $this->db()->execute(
                "INSERT INTO kuota_cuti_besar (user_id, leave_type_id, kuota_total, sisa_kuota) VALUES (?, 2, 90, 90)",
                [$userId]
            );
        }
    }
    
    /**
     * Pindahkan kuota cuti melahirkan
     */
    private function moveKuotaCutiMelahirkan($userId) {
        // Kuota cuti melahirkan tidak perlu dipindahkan karena sudah terkait dengan user_id
        // Hanya perlu memastikan data tetap ada
        $existing = $this->db()->fetch("SELECT id FROM kuota_cuti_melahirkan WHERE user_id = ?", [$userId]);
        if (!$existing) {
            // Buat kuota default jika tidak ada
            $this->db()->execute(
                "INSERT INTO kuota_cuti_melahirkan (user_id, leave_type_id, kuota_total, jumlah_pengambilan, sisa_pengambilan, status) VALUES (?, 4, 90, 0, 3, 'tersedia')",
                [$userId]
            );
        }
    }
    
    /**
     * Pindahkan kuota cuti alasan penting
     */
    private function moveKuotaCutiAlasanPenting($userId) {
        // Kuota cuti alasan penting tidak perlu dipindahkan karena sudah terkait dengan user_id
        // Hanya perlu memastikan data tetap ada
        $currentYear = date('Y');
        $years = [$currentYear - 2, $currentYear - 1, $currentYear];
        foreach ($years as $year) {
            $existing = $this->db()->fetch("SELECT id FROM kuota_cuti_alasan_penting WHERE user_id = ? AND tahun = ?", [$userId, $year]);
            if (!$existing) {
                // Buat kuota default jika tidak ada
                $this->db()->execute(
                    "INSERT INTO kuota_cuti_alasan_penting (user_id, leave_type_id, tahun, kuota_tahunan) VALUES (?, 5, ?, 30)",
                    [$userId, $year]
                );
            }
        }
    }
    
    /**
     * Pindahkan kuota cuti luar tanggungan
     */
    private function moveKuotaCutiLuarTanggungan($userId) {
        // Kuota cuti luar tanggungan tidak perlu dipindahkan karena sudah terkait dengan user_id
        // Hanya perlu memastikan data tetap ada
        $currentYear = date('Y');
        $years = [$currentYear - 2, $currentYear - 1, $currentYear];
        foreach ($years as $year) {
            $existing = $this->db()->fetch("SELECT id FROM kuota_cuti_luar_tanggungan WHERE user_id = ? AND tahun = ?", [$userId, $year]);
            if (!$existing) {
                // Buat kuota default jika tidak ada
                $this->db()->execute(
                    "INSERT INTO kuota_cuti_luar_tanggungan (user_id, leave_type_id, tahun, kuota_tahunan, sisa_kuota) VALUES (?, 6, ?, 30, 30)",
                    [$userId, $year]
                );
            }
        }
    }
    
    /**
     * Catat log perpindahan unit kerja
     */
    private function logUnitKerjaTransfer($userId, $nama, $nip, $oldUnitKerja, $newUnitKerja) {
        $adminId = $_SESSION['user_id'] ?? 0;
        $adminName = $_SESSION['nama'] ?? 'System';
        
        $logMessage = "User {$nama} (NIP: {$nip}) dipindahkan dari {$oldUnitKerja} ke {$newUnitKerja} oleh admin {$adminName}";
        
        // Catat ke file log
        $logFile = __DIR__ . '/../../logs/unit_kerja_transfer.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] {$logMessage}\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Catat ke database jika ada tabel log
        try {
            $this->db()->execute(
                "INSERT INTO activity_logs (user_id, admin_id, action, description, created_at) VALUES (?, ?, ?, ?, NOW())",
                [$userId, $adminId, 'unit_kerja_transfer', $logMessage]
            );
        } catch (Exception $e) {
            // Jika tabel log tidak ada, abaikan error
        }
    }
    
    /**
     * Catat log penimpaan data user
     */
    private function logDataOverwrite($userId, $nama, $nip, $oldNama, $oldNip) {
        $adminId = $_SESSION['user_id'] ?? 0;
        $adminName = $_SESSION['nama'] ?? 'System';
        
        $logMessage = "Data user ditimpa: {$oldNama} (NIP: {$oldNip}) → {$nama} (NIP: {$nip}) oleh admin {$adminName}";
        
        // Catat ke file log
        $logFile = __DIR__ . '/../../logs/unit_kerja_transfer.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] {$logMessage}\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Catat ke database jika ada tabel log
        try {
            $this->db()->execute(
                "INSERT INTO activity_logs (user_id, admin_id, action, description, created_at) VALUES (?, ?, ?, ?, NOW())",
                [$userId, $adminId, 'data_overwrite', $logMessage]
            );
        } catch (Exception $e) {
            // Jika tabel log tidak ada, abaikan error
        }
    }
    
    /**
     * Generate username dasar tanpa suffix
     */
    private function generateBaseUsername($satker) {
        // Aturan username: jika mengandung 'Pengadilan Tinggi Agama' → pta_lokasi, jika 'Pengadilan Agama' → pa_lokasi
        $satkerLower = strtolower(trim($satker));
        $username = '';
        if (strpos($satkerLower, 'pengadilan tinggi agama') !== false) {
            $lokasi = trim(str_replace('pengadilan tinggi agama', '', $satkerLower));
            $lokasi = preg_replace('/\s+/', '_', $lokasi);
            $username = 'pta_' . $lokasi;
        } elseif (strpos($satkerLower, 'pengadilan agama') !== false) {
            $lokasi = trim(str_replace('pengadilan agama', '', $satkerLower));
            $lokasi = preg_replace('/\s+/', '_', $lokasi);
            $username = 'pa_' . $lokasi;
        } else {
            // fallback: semua lowercase, spasi jadi underscore
            $username = preg_replace('/\s+/', '_', $satkerLower);
        }
        
        return $username;
    }
    
    private function generateUsername($satker, $excludeUserId = null) {
        // Generate username dasar
        $username = $this->generateBaseUsername($satker);
        
        // Cek apakah username sudah ada (kecuali user yang sedang diupdate)
        $baseUsername = $username;
        $counter = 1;
        while ($this->isUsernameExists($username, $excludeUserId)) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        return $username;
    }
    
    /**
     * Cek apakah username sudah ada di database
     */
    private function isUsernameExists($username, $excludeUserId = null) {
        if ($excludeUserId) {
            $sql = "SELECT COUNT(*) as count FROM users WHERE username = ? AND id != ?";
            $result = $this->db()->fetch($sql, [$username, $excludeUserId]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM users WHERE username = ?";
            $result = $this->db()->fetch($sql, [$username]);
        }
        return $result['count'] > 0;
    }
    
    /**
     * Kosongkan username lama agar bisa digunakan user lain
     */
    private function clearOldUsername($oldUsername, $currentUserId) {
        // Cek apakah ada user lain yang menggunakan username lama
        $sql = "SELECT id, nama, unit_kerja FROM users WHERE username = ? AND id != ?";
        $existingUsers = $this->db()->fetchAll($sql, [$oldUsername, $currentUserId]);
        
        if (!empty($existingUsers)) {
            // Jika ada user lain yang menggunakan username lama, 
            // berikan username baru dengan suffix untuk user tersebut
            foreach ($existingUsers as $user) {
                // Generate username baru berdasarkan unit kerja user tersebut
                $newUsername = $this->generateUsername($user['unit_kerja'], $user['id']);
                
                // Update username user tersebut
                $this->userModel->update($user['id'], ['username' => $newUsername]);
                
                // Log perubahan username
                error_log("CLEAR_USERNAME: User {$user['nama']} (ID: {$user['id']}) username diubah dari {$oldUsername} ke {$newUsername} karena konflik");
            }
        }
        
        // Log bahwa username lama telah dikosongkan
        error_log("CLEAR_USERNAME: Username '{$oldUsername}' telah dikosongkan untuk user ID {$currentUserId}");
    }
    
    /**
     * Kosongkan username lama untuk digunakan kembali oleh user lain
     */
    private function clearOldUsernameForReuse($oldUsername, $currentUserId) {
        // Cek apakah ada user lain yang menggunakan username lama
        $sql = "SELECT id, nama, unit_kerja FROM users WHERE username = ? AND id != ?";
        $existingUsers = $this->db()->fetchAll($sql, [$oldUsername, $currentUserId]);
        
        if (!empty($existingUsers)) {
            // Jika ada user lain yang menggunakan username lama, 
            // berikan username baru dengan suffix untuk user tersebut
            foreach ($existingUsers as $user) {
                // Generate username baru berdasarkan unit kerja user tersebut
                $newUsername = $this->generateUsername($user['unit_kerja'], $user['id']);
                
                // Update username user tersebut
                $this->userModel->update($user['id'], ['username' => $newUsername]);
                
                // Log perubahan username
                error_log("CLEAR_FOR_REUSE: User {$user['nama']} (ID: {$user['id']}) username diubah dari {$oldUsername} ke {$newUsername} untuk mengosongkan username lama");
            }
        }
        
        // Log bahwa username lama telah dikosongkan untuk digunakan kembali
        error_log("CLEAR_FOR_REUSE: Username '{$oldUsername}' telah dikosongkan untuk digunakan kembali");
    }
    

    
    // ===== ENDPOINT UNTUK KUOTA CUTI BERDASARKAN JENIS =====
    
    /**
     * Get kuota cuti besar
     */
    public function getKuotaBesar() {
        requireAdmin();
        
        $userId = cleanInput($_POST['user_id']);
        $db = Database::getInstance();
        
        $sql = "SELECT * FROM kuota_cuti_besar WHERE user_id = ? LIMIT 1";
        $result = $db->fetch($sql, [$userId]);
        
        if ($result) {
            $this->jsonResponse([
                'success' => true,
                'data' => $result
            ]);
        } else {
            // Jika belum ada data, buat data default
            $kuotaDefault = getKuotaFromLeaveType(2); // ID 2 untuk cuti besar
            $data = [
                'user_id' => $userId,
                'leave_type_id' => 2,
                'kuota_total' => $kuotaDefault,
                'sisa_kuota' => $kuotaDefault,
                'status' => 'belum_berhak',
                'tanggal_berhak' => null,
                'catatan' => null
            ];
            
            $this->jsonResponse([
                'success' => false,
                'data' => $data,
                'message' => 'Data kuota cuti besar belum ada'
            ]);
        }
    }
    
    /**
     * Update kuota cuti besar
     */
    public function updateKuotaBesar() {
        requireAdmin();
        
        $userId = cleanInput($_POST['user_id']);
        $kuotaTotal = (int)cleanInput($_POST['kuota_total']);
        $sisaKuota = (int)cleanInput($_POST['sisa_kuota']);
        $status = cleanInput($_POST['status']);
        $tanggalBerhak = cleanInput($_POST['tanggal_berhak']);
        
        $db = Database::getInstance();
        
        // Cek apakah data sudah ada
        $sql = "SELECT id FROM kuota_cuti_besar WHERE user_id = ?";
        $existing = $db->fetch($sql, [$userId]);
        
        if ($existing) {
            // Update data yang ada
            $sql = "UPDATE kuota_cuti_besar SET 
                    kuota_total = ?, 
                    sisa_kuota = ?, 
                    status = ?, 
                    tanggal_berhak = ?,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE user_id = ?";
            $result = $db->execute($sql, [$kuotaTotal, $sisaKuota, $status, $tanggalBerhak, $userId]);
        } else {
            // Insert data baru
            $sql = "INSERT INTO kuota_cuti_besar 
                    (user_id, leave_type_id, kuota_total, sisa_kuota, status, tanggal_berhak) 
                    VALUES (?, 2, ?, ?, ?, ?)";
            $result = $db->execute($sql, [$userId, $kuotaTotal, $sisaKuota, $status, $tanggalBerhak]);
        }
        
        if ($result) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Kuota cuti besar berhasil diupdate'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal mengupdate kuota cuti besar'
            ]);
        }
    }
    
    /**
     * Get kuota cuti melahirkan
     */
    public function getKuotaMelahirkan() {
        requireAdmin();
        
        $userId = cleanInput($_POST['user_id']);
        $db = Database::getInstance();
        
        $sql = "SELECT * FROM kuota_cuti_melahirkan WHERE user_id = ? LIMIT 1";
        $result = $db->fetch($sql, [$userId]);
        
        if ($result) {
            $result['jumlah_pengambilan'] = isset($result['jumlah_pengambilan']) ? (int)$result['jumlah_pengambilan'] : 0;
            $this->jsonResponse([
                'success' => true,
                'data' => $result
            ]);
        } else {
            // Jika belum ada data, buat data default
            $data = [
                'user_id' => $userId,
                'leave_type_id' => 4,
                'kuota_total' => 90,
                'sisa_kuota' => 90,
                'status' => 'tersedia',
                'tanggal_penggunaan' => null,
                'catatan' => null
            ];
            
            $this->jsonResponse([
                'success' => false,
                'data' => $data,
                'message' => 'Data kuota cuti melahirkan belum ada'
            ]);
        }
    }
    
    /**
     * Update kuota cuti melahirkan
     */
    public function updateKuotaMelahirkan() {
        requireAdmin();
        $userId = cleanInput($_POST['user_id']);
        $kuotaTotal = (int)cleanInput($_POST['kuota_total']);
        $status = cleanInput($_POST['status']);
        $tanggalPenggunaan = cleanInput($_POST['tanggal_penggunaan']);
        $jumlahPengambilan = isset($_POST['jumlah_pengambilan_melahirkan']) ? (int)cleanInput($_POST['jumlah_pengambilan_melahirkan']) : null;
        if ($jumlahPengambilan !== null) {
            if ($jumlahPengambilan < 0) $jumlahPengambilan = 0;
            if ($jumlahPengambilan > 3) $jumlahPengambilan = 3;
        }
        $db = Database::getInstance();
        $sql = "SELECT id, jumlah_pengambilan, sisa_pengambilan FROM kuota_cuti_melahirkan WHERE user_id = ?";
        $existing = $db->fetch($sql, [$userId]);
        if ($existing) {
            $jumlahPengambilanLama = (int)$existing['jumlah_pengambilan'];
            $sisaPengambilanLama = isset($existing['sisa_pengambilan']) ? (int)$existing['sisa_pengambilan'] : (3 - $jumlahPengambilanLama);
            $selisih = 0;
            if ($jumlahPengambilan !== null) {
                $selisih = $jumlahPengambilan - $jumlahPengambilanLama;
            }
            $sisaPengambilanBaru = $sisaPengambilanLama - $selisih;
            if ($sisaPengambilanBaru < 0) $sisaPengambilanBaru = 0;
            if ($sisaPengambilanBaru > 3) $sisaPengambilanBaru = 3;
            $sql = "UPDATE kuota_cuti_melahirkan SET 
                    kuota_total = ?, 
                    status = ?, 
                    tanggal_penggunaan = ?,
                    updated_at = CURRENT_TIMESTAMP";
            $params = [$kuotaTotal, $status, $tanggalPenggunaan];
            if ($jumlahPengambilan !== null) {
                $sql .= ", jumlah_pengambilan = ?, sisa_pengambilan = ?";
                $params[] = $jumlahPengambilan;
                $params[] = $sisaPengambilanBaru;
            }
            $sql .= " WHERE user_id = ?";
            $params[] = $userId;
            $result = $db->execute($sql, $params);
        } else {
            $jumlahPengambilanBaru = $jumlahPengambilan !== null ? $jumlahPengambilan : 0;
            if ($jumlahPengambilanBaru > 3) $jumlahPengambilanBaru = 3;
            if ($jumlahPengambilanBaru < 0) $jumlahPengambilanBaru = 0;
            $sisaPengambilanBaru = 3 - $jumlahPengambilanBaru;
            if ($sisaPengambilanBaru < 0) $sisaPengambilanBaru = 0;
            $sql = "INSERT INTO kuota_cuti_melahirkan 
                    (user_id, leave_type_id, kuota_total, jumlah_pengambilan, sisa_pengambilan, status, tanggal_penggunaan) 
                    VALUES (?, 4, ?, ?, ?, ?, ?)";
            $result = $db->execute($sql, [$userId, $kuotaTotal, $jumlahPengambilanBaru, $sisaPengambilanBaru, $status, $tanggalPenggunaan]);
        }
        if ($result) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Kuota cuti melahirkan berhasil diupdate'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal mengupdate kuota cuti melahirkan'
            ]);
        }
    }
    
    /**
     * Get kuota tahunan lainnya (sakit, alasan penting, luar tanggungan)
     */
    public function getKuotaTahunanLain() {
        requireAdmin();
        
        $userId = cleanInput($_POST['user_id']);
        $jenisKuota = cleanInput($_POST['jenis_kuota']);
        $tahun = cleanInput($_POST['tahun']);
        
        $db = Database::getInstance();
        
        // Mapping jenis kuota ke leave_type_id
        $leaveTypeMapping = [
            'sakit' => 3,
            'alasan_penting' => 5,
            'luar_tanggungan' => 6
        ];
        
        $leaveTypeId = $leaveTypeMapping[$jenisKuota] ?? 0;
        if (!$leaveTypeId) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Jenis kuota tidak valid'
            ]);
            return;
        }
        
        // Tentukan tabel berdasarkan jenis kuota
        $tableName = '';
        switch ($jenisKuota) {
            case 'sakit':
                $tableName = 'kuota_cuti_sakit';
                break;
            case 'alasan_penting':
                $tableName = 'kuota_cuti_alasan_penting';
                break;
            case 'luar_tanggungan':
                $tableName = 'kuota_cuti_luar_tanggungan';
                break;
        }
        
        if (!$tableName) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Tabel tidak ditemukan'
            ]);
            return;
        }
        
        $sql = "SELECT * FROM {$tableName} WHERE user_id = ? AND tahun = ? LIMIT 1";
        $result = $db->fetch($sql, [$userId, $tahun]);
        
        if ($result) {
            $this->jsonResponse([
                'success' => true,
                'data' => $result
            ]);
        } else {
            // Jika belum ada data, buat data default
            $kuotaDefault = getKuotaFromLeaveType($leaveTypeId);
            
            // Untuk alasan penting, tidak ada kuota akumulatif – hanya batasan per-pengajuan.
            // Kembalikan respons informatif dan tidak insert ke DB kuota.
            if ($jenisKuota === 'alasan_penting') {
                $this->jsonResponse([
                    'success' => true,
                    'data' => [
                        'user_id' => $userId,
                        'leave_type_id' => 5,
                        'tahun' => $tahun,
                        'kuota_tahunan' => null,
                        'sisa_kuota' => null,
                        'is_per_submission' => true,
                        'max_days_regular' => 10,
                        'max_days_hakim_tinggi' => 30
                    ],
                    'message' => 'Cuti Alasan Penting tidak memiliki kuota akumulatif. Batas: 10 hari/pengajuan (Hakim Tinggi: 30 hari/pengajuan).'
                ]);
                return;
            }
            
            // Coba buat data default
            $sql = "INSERT INTO {$tableName} 
                    (user_id, leave_type_id, tahun, kuota_tahunan, sisa_kuota) 
                    VALUES (?, ?, ?, ?, ?)";
            $insertResult = $db->execute($sql, [$userId, $leaveTypeId, $tahun, $kuotaDefault, $kuotaDefault]);
            
            if ($insertResult) {
                // Ambil data yang baru dibuat
                $sql = "SELECT * FROM {$tableName} WHERE user_id = ? AND tahun = ? LIMIT 1";
                $result = $db->fetch($sql, [$userId, $tahun]);
                
                $this->jsonResponse([
                    'success' => true,
                    'data' => $result,
                    'message' => "Data kuota {$jenisKuota} tahun {$tahun} berhasil dibuat"
                ]);
            } else {
                $data = [
                    'user_id' => $userId,
                    'leave_type_id' => $leaveTypeId,
                    'tahun' => $tahun,
                    'kuota_tahunan' => $kuotaDefault,
                    'sisa_kuota' => $kuotaDefault,
                    'catatan' => null
                ];
                
                $this->jsonResponse([
                    'success' => false,
                    'data' => $data,
                    'message' => "Data kuota {$jenisKuota} tahun {$tahun} belum ada dan gagal dibuat"
                ]);
            }
        }
    }
    
    /**
     * Update kuota tahunan lainnya
     */
    public function updateKuotaTahunanLain() {
        requireAdmin();
        
        $userId = cleanInput($_POST['user_id']);
        $jenisKuota = cleanInput($_POST['jenis_kuota']);
        $tahun = cleanInput($_POST['tahun']);
        // Untuk alasan penting, tidak ada kuota akumulatif – hanya batasan per-pengajuan
        if ($jenisKuota === 'alasan_penting') {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Cuti Alasan Penting tidak memiliki kuota akumulatif. Batas per pengajuan: 10 hari (Hakim Tinggi: 30 hari). Tidak ada data yang perlu diupdate.'
            ]);
            return;
        }
        $kuotaTahunan = isset($_POST['kuota_tahunan']) && $_POST['kuota_tahunan'] !== '' ? (int)cleanInput($_POST['kuota_tahunan']) : 14;
        $sisaKuota = (int)cleanInput($_POST['sisa_kuota']);
        
        $db = Database::getInstance();
        
        // Mapping jenis kuota ke leave_type_id dan tabel
        $leaveTypeMapping = [
            'sakit' => ['id' => 3, 'table' => 'kuota_cuti_sakit'],
            'alasan_penting' => ['id' => 5, 'table' => 'kuota_cuti_alasan_penting'],
            'luar_tanggungan' => ['id' => 6, 'table' => 'kuota_cuti_luar_tanggungan']
        ];
        
        $mapping = $leaveTypeMapping[$jenisKuota] ?? null;
        if (!$mapping) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Jenis kuota tidak valid'
            ]);
            return;
        }
        
        $tableName = $mapping['table'];
        $leaveTypeId = $mapping['id'];
        
        // Cek apakah data sudah ada
        $sql = "SELECT id FROM {$tableName} WHERE user_id = ? AND tahun = ?";
        $existing = $db->fetch($sql, [$userId, $tahun]);
        
        if ($existing) {
            // Update data yang ada
            $sql = "UPDATE {$tableName} SET 
                    kuota_tahunan = ?, 
                    sisa_kuota = ?,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE user_id = ? AND tahun = ?";
            $result = $db->execute($sql, [$kuotaTahunan, $sisaKuota, $userId, $tahun]);
        } else {
            // Insert data baru
            $sql = "INSERT INTO {$tableName} 
                    (user_id, leave_type_id, tahun, kuota_tahunan, sisa_kuota) 
                    VALUES (?, ?, ?, ?, ?)";
            $result = $db->execute($sql, [$userId, $leaveTypeId, $tahun, $kuotaTahunan, $sisaKuota]);
        }
        
        if ($result) {
            $this->jsonResponse([
                'success' => true,
                'message' => "Kuota {$jenisKuota} tahun {$tahun} berhasil diupdate"
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => "Gagal mengupdate kuota {$jenisKuota} tahun {$tahun}"
            ]);
        }
    }
    
    /**
     * Update kuota tahunan untuk beberapa tahun sekaligus (batch)
     */
    public function updateQuotaAll() {
        requireAdmin();
        $userId = isset($_POST['user_id']) ? cleanInput($_POST['user_id']) : null;
        $dataKuota = isset($_POST['data_kuota']) ? $_POST['data_kuota'] : null;
        // Jika data_kuota dikirim sebagai JSON string, decode dulu
        if (is_string($dataKuota)) {
            $decoded = json_decode($dataKuota, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $dataKuota = $decoded;
            }
        }
        if (!$userId || !$dataKuota || !is_array($dataKuota)) {
            $this->jsonResponse(['success' => false, 'message' => 'Data tidak lengkap']);
            return;
        }
        $errors = [];
        foreach ($dataKuota as $item) {
            $tahun = isset($item['tahun']) ? (int)$item['tahun'] : null;
            $kuotaTahunan = isset($item['kuota_tahunan']) ? (int)$item['kuota_tahunan'] : null;
            $sisaKuota = isset($item['sisa_kuota']) ? (int)$item['sisa_kuota'] : null;
            if (!$tahun || $kuotaTahunan === null || $sisaKuota === null) {
                $errors[] = "Data tidak lengkap untuk tahun $tahun";
                continue;
            }
            // Panggil logic updateQuota langsung (tanpa response JSON)
            $user = $this->userModel->find($userId);
            if (!$user || ($user['user_type'] !== 'pegawai' && $user['user_type'] !== 'atasan')) {
                $errors[] = "User tidak valid untuk tahun $tahun";
                continue;
            }
            $currentYear = date('Y');
            $allowedYears = [$currentYear - 2, $currentYear - 1, $currentYear];
            if (!in_array($tahun, $allowedYears)) {  
                $errors[] = "Tahun $tahun tidak valid";
                continue;
            }
            if ($sisaKuota > $kuotaTahunan) {
                $errors[] = "Sisa kuota tidak boleh lebih dari kuota tahunan ($tahun)";
                continue;
            }
            if ($kuotaTahunan < 0 || $sisaKuota < 0) {
                $errors[] = "Kuota tidak boleh negatif ($tahun)";
                continue;
            }
            $existingBalance = $this->leaveBalanceModel->getBalance($userId, $tahun);
            $db = Database::getInstance();
            if ($existingBalance) {
                $db->query(
                    "UPDATE leave_balances SET kuota_tahunan = ?, sisa_kuota = ? WHERE user_id = ? AND tahun = ?",
                    [$kuotaTahunan, $sisaKuota, $userId, $tahun]
                );
            } else {
                $this->leaveBalanceModel->create([
                    'user_id' => $userId,
                    'tahun' => $tahun,
                    'kuota_tahunan' => $kuotaTahunan,
                    'sisa_kuota' => $sisaKuota
                ]);
            }
        }
        if (count($errors) > 0) {
            $this->jsonResponse(['success' => false, 'message' => implode('; ', $errors)]);
        } else {
            $this->jsonResponse(['success' => true, 'message' => 'Seluruh kuota tahunan berhasil diupdate']);
        }
    }

    /**
     * Update user's email
     */
    public function updateUserEmail() {
        requireLogin();
        
        // Only non-admin users can update their own email
        if ($_SESSION['user_type'] == 'admin') {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Admin tidak dapat mengubah email melalui fitur ini'
            ]);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $newEmail = trim(cleanInput($_POST['email'] ?? ''));
        
        // Validation
        if (empty($newEmail)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Email tidak boleh kosong'
            ]);
            return;
        }
        
        // Validate email format
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Format email tidak valid'
            ]);
            return;
        }
        
        // Check if email already exists (excluding current user)
        $db = Database::getInstance();
        $existingEmail = $db->fetch(
            "SELECT id FROM users WHERE email = ? AND id != ?",
            [$newEmail, $userId]
        );
        
        if ($existingEmail) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Email sudah digunakan oleh pengguna lain'
            ]);
            return;
        }
        
        // Update email
        $result = $this->userModel->update($userId, ['email' => $newEmail]);
        
        if ($result) {
            // Update session email
            $_SESSION['email'] = $newEmail;
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Email berhasil diperbarui'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal memperbarui email. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Delete user's email
     */
    public function deleteUserEmail() {
        requireLogin();
        
        // Only non-admin users can delete their own email
        if ($_SESSION['user_type'] == 'admin') {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Admin tidak dapat menghapus email melalui fitur ini'
            ]);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Delete email by setting it to NULL
        $result = $this->userModel->update($userId, ['email' => null]);
        
        if ($result) {
            // Update session email
            $_SESSION['email'] = null;
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Email berhasil dihapus'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal menghapus email. Silakan coba lagi.'
            ]);
        }
    }

    public function signatureManagement() {
        requireLogin();
        
        // Include signature helper
        require_once __DIR__ . '/../helpers/signature_helper.php';
        
        // Get signature data using helper
        $userType = $_SESSION['user_type'];
        $signatureType = getSignatureTypeByUserType($userType);
        $signature = getUserSignature($_SESSION['user_id'], $signatureType);
        
        // Output the signature management view
        include __DIR__ . '/../views/user/signature_manage.php';
    }

    public function parafManagement() {
        requireLogin();

        // Include signature helper for paraf functions
        require_once __DIR__ . '/../helpers/signature_helper.php';

        // Get paraf data using helper
        $paraf = getUserSignature($_SESSION['user_id'], 'paraf');

        // Output the paraf management view
        include __DIR__ . '/../views/user/paraf_manage.php';
    }

    /**
     * API endpoint untuk preview atasan otomatis berdasarkan jabatan
     * Called via AJAX dari form user
     */
    public function getAutoDirectSuperior() {
        requireAdmin();
        
        if (!isset($_POST['jabatan']) || empty($_POST['jabatan'])) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Jabatan harus diisi'
            ]);
            return;
        }

        $jabatan = cleanInput($_POST['jabatan']);
        $userType = isset($_POST['user_type']) ? cleanInput($_POST['user_type']) : 'pegawai';

        // Untuk pegawai dan atasan, tentukan atasan otomatis berdasarkan jabatan
        if ($userType == 'pegawai' || $userType == 'atasan') {
            $atasanId = getAutomaticDirectSuperior($jabatan);
            
            if ($atasanId !== null) {
                // Ambil data atasan
                $db = Database::getInstance();
                $atasan = $db->fetch(
                    "SELECT id_atasan, nama_atasan, NIP, jabatan FROM atasan WHERE id_atasan = ?",
                    [$atasanId]
                );

                if ($atasan) {
                    $this->jsonResponse([
                        'success' => true,
                        'message' => 'Atasan otomatis ditemukan',
                        'atasan_id' => $atasan['id_atasan'],
                        'atasan_nama' => $atasan['nama_atasan'],
                        'atasan_nip' => $atasan['NIP'],
                        'atasan_jabatan' => $atasan['jabatan']
                    ]);
                    return;
                }
            }

            // Tidak ada atasan yang cocok
            $this->jsonResponse([
                'success' => false,
                'message' => 'Tidak ada atasan yang cocok untuk jabatan ini',
                'atasan_id' => null
            ]);
        } else {
            // Untuk admin, tidak ada atasan langsung
            $this->jsonResponse([
                'success' => true,
                'message' => 'User type ini tidak memiliki atasan',
                'atasan_id' => null,
                'note' => 'Admin tidak memiliki atasan langsung'
            ]);
        }
    }
}
