<?php
class LeaveController extends Controller
{
    private $leaveModel;
    private $leaveTypeModel;
    private $leaveBalanceModel;

    public function __construct()
    {
        $this->leaveModel = $this->model('Leave');
        $this->leaveTypeModel = $this->model('LeaveType');
        $this->leaveBalanceModel = $this->model('LeaveBalance');
    }

    public function index()
    {
        if (!isLoggedIn()) {
            $this->redirect('auth/login');
        }

        $data = [
            'title' => 'Riwayat & Status Pengajuan Cuti',
            'page_title' => 'Riwayat & Status Pengajuan Cuti'
        ];

        $this->view('leave/history', $data);
    }

    public function history()
    {
        if (!isLoggedIn()) {
            $this->redirect('auth/login');
        }

        $data = [
            'title' => 'Riwayat & Status Pengajuan Cuti',
            'page_title' => 'Riwayat & Status Pengajuan Cuti'
        ];

        $this->view('leave/history', $data);
    }

    public function form()
    {
        if (!isUser()) {
            $this->redirect('dashboard');
        }

        $data = [
            'title' => 'Ajukan Cuti',
            'page_title' => 'Ajukan Cuti',
            'catatan_cuti' => '' // Catatan kosong saat form dibuka, akan diisi setelah user memilih jenis cuti
        ];

        $this->view('leave/form', $data);
    }

    /**
     * Generate catatan cuti otomatis via AJAX
     * Called when user selects leave type and/or date
     */
    public function generateCatatan()
    {
        if (!isUser()) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized']);
        }

        $leaveTypeId = isset($_POST['leave_type_id']) ? intval($_POST['leave_type_id']) : 1;
        $tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d');

        // Validasi tanggal format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            $tanggal = date('Y-m-d');
        }

        // Get satker_id dari session user
        $satker_id = isset($_SESSION['unit_kerja']) ? $_SESSION['unit_kerja'] : 1;

        // Generate catatan cuti otomatis
        $catatan = generateCatatanCuti($satker_id, $leaveTypeId, $tanggal);

        $this->jsonResponse([
            'success' => true,
            'data' => [
                'catatan' => $catatan,
                'leave_type_id' => $leaveTypeId,
                'tanggal' => $tanggal
            ]
        ]);
    }

    public function submit()
    {
        if (!isUser()) {
            $this->jsonResponse(['success' => false, 'message' => 'Hanya pegawai dan atasan yang dapat mengajukan cuti']);
        }

        // Validasi input
        $leaveTypeId = cleanInput($_POST['leave_type_id']);
        $tanggalMulai = cleanInput($_POST['tanggal_mulai']);
        $tanggalSelesai = cleanInput($_POST['tanggal_selesai']);
        $alasan = cleanInput($_POST['alasan']);
        $alamatCuti = cleanInput($_POST['alamat_cuti']);
        $teleponCuti = cleanInput($_POST['telepon_cuti']);
        $catatanCuti = isset($_POST['catatan_cuti']) ? cleanInput($_POST['catatan_cuti']) : null;

        // Validasi semua field wajib
        if (empty($leaveTypeId) || empty($tanggalMulai) || empty($tanggalSelesai) ||
        empty($alasan) || empty($alamatCuti) || empty($teleponCuti)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Semua field wajib harus diisi! Alamat dan nomor telepon selama cuti wajib diisi.'
            ]);
        }

        // Validasi format nomor telepon
        if (!preg_match('/^[0-9\-\+\(\)\ ]+$/', $teleponCuti) || strlen($teleponCuti) < 10) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Format nomor telepon tidak valid! Minimal 10 digit.'
            ]);
        }

        // Validasi tanggal
        if (strtotime($tanggalMulai) > strtotime($tanggalSelesai)) {
            $this->jsonResponse(['success' => false, 'message' => 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai!']);
        }

        // Hitung jumlah hari
        $jumlahHari = hitungHariKerja($tanggalMulai, $tanggalSelesai);

        // Validasi kuota cuti berdasarkan jenis cuti
        $validation = validateKuotaCuti($_SESSION['user_id'], $leaveTypeId, $jumlahHari, date('Y'));
        if (!$validation['valid']) {
            $this->jsonResponse(['success' => false, 'message' => $validation['message']]);
        }

        // Untuk cuti sakit, jika kuota tidak mencukupi, cek jabatan user
        if ($leaveTypeId == 3) {
            $sisaKuotaSakit = getSisaKuotaByType($_SESSION['user_id'], 3, date('Y'));
            $jabatanUser = isset($_SESSION['jabatan']) ? strtolower($_SESSION['jabatan']) : '';
            if (strpos($jabatanUser, 'ketua') !== false || strpos($jabatanUser, 'hakim') !== false) {
                // Jika jabatan mengandung 'ketua' atau 'hakim', kosongkan catatan cuti apapun kondisinya
                $catatanCuti = '';
            }
            else if ($jumlahHari > $sisaKuotaSakit) {
                $hariKurang = $jumlahHari - $sisaKuotaSakit;
                $catatanOtomatis = "Kuota cuti sakit kurang {$hariKurang} hari dan dikenakan pemotongan gaji sebesar 5% per kuota yang kurang";
                // Hanya isi otomatis jika catatan cuti kosong atau belum berisi informasi pemotongan gaji
                if (empty($catatanCuti) || !strpos($catatanCuti, 'pemotongan gaji')) {
                    $catatanCuti = $catatanOtomatis;
                }
            }
        }

        // Catatan: Validasi maksimal hari cuti alasan penting (id=5) sudah ditangani
        // di validateKuotaCuti() dengan pembeda jabatan (hakim tinggi vs pegawai biasa).

        // Tambahan validasi: Cuti Melahirkan hanya untuk pegawai perempuan
        if ($leaveTypeId == 4) {
            // Ambil NIP dari session (lebih pasti tersedia saat submit)
            $nipRaw = $_SESSION['nip'] ?? '';
            $nipDigits = preg_replace('/\D/', '', $nipRaw);
            if (strlen($nipDigits) < 15) {
                $this->jsonResponse(['success' => false, 'message' => 'Tidak dapat menentukan jenis kelamin dari NIP. Silakan perbarui data NIP Anda di profil atau hubungi administrator.']);
            }
            // Digit ke-15 (1-based) => index 14 (0-based)
            $digit15 = $nipDigits[14];
            if ($digit15 === '1') {
                // Laki-laki -> tolak pengajuan cuti melahirkan
                $this->jsonResponse(['success' => false, 'message' => 'Cuti melahirkan hanya diperuntukkan untuk pegawai perempuan. Pengajuan dibatalkan.']);
            }
        // Jika digit = 2 => lanjutkan
        }

        // Generate nomor surat
        $nomorSurat = generateNomorSurat($_SESSION['unit_kerja']);

        // Handle file upload dokumen pendukung
        $dokumenPendukung = '';
        $hasDokumen = false;
        if (isset($_FILES['dokumen_pendukung']) && $_FILES['dokumen_pendukung']['error'] == 0) {
            $uploadResult = uploadDokumen($_FILES['dokumen_pendukung']);
            if ($uploadResult['success']) {
                $dokumenPendukung = $uploadResult['filename'];
                $hasDokumen = true;
            }
            else {
                $this->jsonResponse(['success' => false, 'message' => $uploadResult['message']]);
            }
        }

        // Tentukan status berdasarkan jenis cuti dan keberadaan dokumen
        $status = 'draft'; // default
        if ($leaveTypeId == 3 || $leaveTypeId == 5) { // Cuti Sakit atau Cuti Alasan Penting
            if ($hasDokumen) {
                $status = 'pending'; // Langsung ke atasan
            }
            else {
                $status = 'pending_admin_upload'; // Tunggu admin upload dokumen
            }
        }
        else {
            // Untuk jenis cuti lainnya, gunakan logika existing
            if (isset($_POST['status'])) {
                $reqStatus = cleanInput($_POST['status']);
                if (in_array($reqStatus, ['draft', 'pending'])) {
                    $status = $reqStatus;
                }
            }
        }

        // Ambil atasan_id dari user yang sedang login
        $userModel = $this->model('User');
        $currentUser = $userModel->find($_SESSION['user_id']);
        $atasanId = $currentUser['atasan'] ?? null;

        // Validasi: pastikan user memiliki atasan
        if (!$atasanId) {
            $this->jsonResponse(['success' => false, 'message' => 'Akun Anda belum terhubung dengan akun atasan Anda. Silakan hubungi staff kepegawaian untuk mengatur data atasan.']);
        }

        // Simpan ke database
        $data = [
            'user_id' => $_SESSION['user_id'],
            'leave_type_id' => $leaveTypeId,
            'nomor_surat' => $nomorSurat,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'jumlah_hari' => $jumlahHari,
            'alasan' => $alasan,
            'alamat_cuti' => $alamatCuti,
            'telepon_cuti' => $teleponCuti,
            'dokumen_pendukung' => $dokumenPendukung,
            'status' => $status,
            'catatan_cuti' => $catatanCuti,
            'atasan_id' => $atasanId
        ];

        $leaveId = $this->leaveModel->create($data);

        if ($leaveId) {
            // Kirim notifikasi ke admin jika status pending_admin_upload
            if ($status === 'pending_admin_upload') {
                $notificationModel = $this->model('Notification');
                $notificationModel->notifyAdmins(
                    "Pengajuan cuti baru memerlukan upload dokumen pendukung: {$_SESSION['nama']}",
                    'warning',
                    $leaveId
                );
            }
            // Generate dokumen Word
            $helperPath = dirname(dirname(__DIR__)) . '/app/helpers/document_helper.php';
            if (!file_exists($helperPath)) {
                $this->jsonResponse(['success' => false, 'message' => 'Helper file tidak ditemukan']);
            }
            require_once $helperPath;

            // Pastikan composer autoload di-load
            $autoloadPath = dirname(dirname(__DIR__)) . '/vendor/autoload.php';
            if (!file_exists($autoloadPath)) {
                $this->jsonResponse(['success' => false, 'message' => 'Composer autoload tidak ditemukan']);
            }
            require_once $autoloadPath;

            $userData = [
                'id' => $_SESSION['user_id'],
                'nama' => $_SESSION['nama'],
                'nip' => $_SESSION['nip'],
                'jabatan' => $_SESSION['jabatan'],
                'golongan' => $_SESSION['golongan'],
                'unit_kerja' => $_SESSION['unit_kerja'],
                'tanggal_masuk' => $_SESSION['tanggal_masuk']
            ];
            // Ambil data atasan user dari database
            require_once dirname(dirname(__DIR__)) . '/app/models/User.php';
            $userModel = new User();
            $userDb = $userModel->find($_SESSION['user_id']);
            if ($userDb && isset($userDb['atasan'])) {
                $userData['atasan'] = $userDb['atasan'];
            }
            else {
                $userData['atasan'] = null;
            }

            // Ambil data yang sudah disimpan di database
            $savedLeaveData = $this->leaveModel->find($leaveId);

            $result = generateLeaveDocument($savedLeaveData, $userData, 'blanko_cuti_template.docx', false, false);

            if ($result['success']) {
                // Simpan info dokumen ke database
                $documentModel = $this->model('DocumentModel');
                $documentModel->createDocument([
                    'leave_request_id' => $leaveId,
                    'filename' => $result['filename'],
                    'document_type' => 'generated',
                    'status' => 'draft',
                    'created_by' => $_SESSION['user_id']
                ]);

                // Tailor response message depending on requested status
                if ($status === 'pending_admin_upload') {
                    $message = 'Pengajuan cuti berhasil dibuat. Admin akan mengupload dokumen pendukung yang diperlukan.';
                }
                elseif ($status === 'pending') {
                    $message = 'Pengajuan cuti berhasil diajukan.';
                }
                else {
                    $message = 'Pengajuan cuti berhasil dibuat! Silakan download, tanda tangan, dan upload kembali dokumen.';
                }
                $response = [
                    'success' => true,
                    'message' => $message,
                    'leave_id' => $leaveId,
                    'document_url' => baseUrl('leave/downloadGeneratedDoc?id=' . $leaveId)
                ];
                // Jika cuti sakit, tambahkan info sisa kuota sakit tahun ini
                if ($leaveTypeId == 3) {
                    $sisaKuotaSakit = getSisaKuotaByType($_SESSION['user_id'], 3, date('Y'));
                    $response['leave_type_id'] = 3;
                    $response['sisa_kuota_sakit'] = $sisaKuotaSakit;
                }


                $this->jsonResponse($response);
            }
            else {
                // Hapus pengajuan jika gagal generate dokumen
                $this->leaveModel->delete($leaveId);
                $this->jsonResponse(['success' => false, 'message' => 'Gagal membuat dokumen: ' . $result['message']]);
            }
        }
        else {
            $this->jsonResponse(['success' => false, 'message' => 'Gagal menyimpan pengajuan cuti!']);
        }
    }

    public function getHistory()
    {
        if (!isLoggedIn()) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized']);
        }

        $filters = [];

        if (isAtasan()) {
            // Load kasubbag workflow helper
            require_once dirname(__DIR__) . '/helpers/kasubbag_workflow_helper.php';

            // Atasan melihat pengajuan yang ditujukan kepada mereka
            // Cari id_atasan dengan join ke tabel atasan berdasarkan NIP user yang login
            $atasanId = $this->db()->fetch(
                "SELECT a.id_atasan, a.role FROM atasan a 
                 WHERE a.NIP = ? LIMIT 1",
            [$_SESSION['nip']]
            );

            if ($atasanId && !empty($atasanId['id_atasan'])) {
                // Store atasan role in session for reference
                $_SESSION['atasan_role'] = $atasanId['role'] ?? null;

                // Atasan juga dapat melihat pengajuan cuti mereka sendiri
                $filters['include_own_requests'] = $_SESSION['user_id'];

                // Build custom filter logic based on role
                if ($atasanId['role'] === 'kasubbag') {
                    // Kasubbag sees:
                    // 1. Pending leaves where they are direct atasan
                    // 2. Pending_kasubbag leaves where they are kasubbag approver
                    $filters['atasan_id'] = $atasanId['id_atasan'];
                    $filters['kasubbag_id'] = $atasanId['id_atasan'];
                    $filters['is_kasubbag_viewer'] = true; // Flag for custom query handling
                }
                else if ($atasanId['role'] === 'kabag') {
                    // Kabag sees:
                    // 1. Pending leaves where they are direct atasan (approval level 1)
                    // 2. Pending_kabag leaves where they are kabag approver (level 3)
                    $filters['atasan_id'] = $atasanId['id_atasan'];
                    $filters['kabag_approver_id'] = $atasanId['id_atasan'];
                    $filters['is_kabag_viewer'] = true;
                }
                else if ($atasanId['role'] === 'sekretaris') {
                    // Sekretaris sees:
                    // 1. Pending leaves where they are direct atasan (approval level 1)
                    //    - this allows sekretaris to approve staff for whom they happen to
                    //      be the immediate boss
                    // 2. Pending_sekretaris leaves where they are sekretaris approver (level 4)
                    //    - these may originate from any atasan (e.g. kabag) once the
                    //      workflow reaches level 4.
                    // Note: the model will build a compound OR clause and ignore the
                    // `atasan_id` filter when necessary; the flag is supplied here for
                    // convenience and for cases where the sekretaris is also the
                    // direct atasan of the request.
                    $filters['atasan_id'] = $atasanId['id_atasan'];
                    $filters['sekretaris_approver_id'] = $atasanId['id_atasan'];
                    $filters['is_sekretaris_viewer'] = true;
                }
                else if ($atasanId['role'] === 'ketua') {
                    // Ketua sees:
                    // 1. Pending leaves where they are direct atasan (approval level 1)
                    // 2. Awaiting_pimpinan leaves where they are ketua approver (level 5)
                    $filters['atasan_id'] = $atasanId['id_atasan'];
                    $filters['ketua_approver_id'] = $atasanId['id_atasan'];
                    $filters['is_ketua_viewer'] = true;
                }
                else {
                    // Regular atasan: only see pending leaves as direct atasan
                    $filters['atasan_id'] = $atasanId['id_atasan'];
                }
            }
        }
        elseif (isPegawai()) {
            // Regular pegawai (non-atasan) only melihat pengajuan mereka sendiri
            $filters['user_id'] = $_SESSION['user_id'];
        }
        elseif (isAdmin()) {
            // Admin melihat semua pengajuan cuti dari seluruh pegawai untuk pemantauan alur
            // Tidak ada pembatasan status
            if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
                $filters['user_id'] = cleanInput($_POST['user_id']);
            }
        }

        if (isset($_POST['status']) && !empty($_POST['status'])) {
            $filters['status'] = cleanInput($_POST['status']);
        }

        if (isset($_POST['tahun']) && !empty($_POST['tahun'])) {
            $filters['tahun'] = cleanInput($_POST['tahun']);
        }

        if (isset($_POST['leave_type_id']) && !empty($_POST['leave_type_id'])) {
            $filters['leave_type_id'] = cleanInput($_POST['leave_type_id']);
        }

        if (isAdmin() && isset($_POST['unit_kerja']) && !empty($_POST['unit_kerja'])) {
            $rawUnit = cleanInput($_POST['unit_kerja']);
            // if numeric, assume it's an id; otherwise try to resolve nama_satker -> id
            if (ctype_digit(strval($rawUnit))) {
                $filters['unit_kerja'] = $rawUnit;
            }
            else {
                $row = $this->db()->fetch("SELECT id_satker FROM satker WHERE nama_satker = ? LIMIT 1", [$rawUnit]);
                if ($row && isset($row['id_satker'])) {
                    $filters['unit_kerja'] = $row['id_satker'];
                }
            }
        }

        if (isAdmin() && isset($_POST['user_status']) && !empty($_POST['user_status'])) {
            $filters['user_status'] = cleanInput($_POST['user_status']);
        }

        // Get data with role-based filtering
        $data = $this->leaveModel->getHistory($filters);

        // compute derived flags for each row (admin/atasan permissions)
        require_once dirname(__DIR__) . '/helpers/general_helper.php';
        foreach ($data as &$row) {
            $row['can_download_generated'] = canDownloadGeneratedDocRow($row);
        }
        unset($row);

        // Include whether current atasan is kasubbag, sekretaris, or ketua so frontend can adapt UI
        $isKasubbag = function_exists('isKasubbag') && isKasubbag();
        $isSekretaris = function_exists('isSekretaris') && isSekretaris();
        $isKetua = function_exists('isKetua') && isKetua();
        $isAdmin = function_exists('isAdmin') && isAdmin();

        $this->jsonResponse([
            'success' => true,
            'data' => $data,
            'is_kasubbag' => $isKasubbag,
            'is_sekretaris' => $isSekretaris,
            'is_ketua' => $isKetua,
            'is_admin' => $isAdmin
        ]);
    }

    /**
     * Endpoint untuk kalender: mengembalikan events di rentang tanggal (start, end)
     * Diterima params POST: start (YYYY-MM-DD), end (YYYY-MM-DD), optional unit_kerja
     */
    public function calendarEvents()
    {
        if (!isLoggedIn()) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized']);
        }

        $start = isset($_POST['start']) ? cleanInput($_POST['start']) : null;
        $end = isset($_POST['end']) ? cleanInput($_POST['end']) : null;

        // Validasi format tanggal (YYYY-MM-DD)
        if ($start && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start)) {
            $start = null;
        }
        if ($end && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
            $end = null;
        }

        $filters = [];

        if (isUser()) {
            $filters['user_id'] = $_SESSION['user_id'];
        }
        elseif (isAtasan()) {
            $atasanId = $this->db()->fetch(
                "SELECT a.id_atasan FROM atasan a 
                 WHERE a.NIP = ? LIMIT 1",
            [$_SESSION['nip']]
            );
            if ($atasanId && !empty($atasanId['id_atasan'])) {
                $filters['atasan_id'] = $atasanId['id_atasan'];
            }
        }
        elseif (isAdmin()) {
            // Pimpinan sebaiknya tidak melihat draft/pending
            $filters['exclude_status'] = ['draft', 'pending'];
        }

        // Only show entries that have been fully approved and completed
        $filters['status'] = 'approved';
        $filters['is_completed'] = 1;

        if (isset($_POST['unit_kerja']) && !empty($_POST['unit_kerja'])) {
            $rawUnit = cleanInput($_POST['unit_kerja']);
            if (ctype_digit(strval($rawUnit))) {
                $filters['unit_kerja'] = $rawUnit;
            }
            else {
                $row = $this->db()->fetch("SELECT id_satker FROM satker WHERE nama_satker = ? LIMIT 1", [$rawUnit]);
                if ($row && isset($row['id_satker'])) {
                    $filters['unit_kerja'] = $row['id_satker'];
                }
            }
        }

        $eventsRaw = $this->leaveModel->getEvents($start, $end, $filters);

        // Load satker mapping
        $satkerMap = [];
        $satkerRows = $this->db()->fetchAll("SELECT id_satker, nama_satker FROM satker");
        foreach ($satkerRows as $s) {
            $satkerMap[$s['id_satker']] = $s['nama_satker'];
        }

        $events = [];
        foreach ($eventsRaw as $item) {
            $color = '#6c757d';
            if ($item['status'] === 'approved')
                $color = '#198754';
            else if ($item['status'] === 'awaiting_pimpinan')
                $color = '#ffc107';
            else if ($item['status'] === 'rejected')
                $color = '#dc3545';
            else if (in_array($item['status'], ['changed', 'postponed']))
                $color = '#0d6efd';

            $endExclusive = date('Y-m-d', strtotime($item['tanggal_selesai'] . ' +1 day'));

            $events[] = [
                'id' => $item['id'],
                'title' => ($item['nama'] ?? 'Unknown') . ' — ' . $item['nama_cuti'],
                'start' => $item['tanggal_mulai'],
                'end' => $endExclusive,
                'allDay' => true,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'status' => $item['status'],
                'jumlah_hari' => $item['jumlah_hari'],
                'unit_kerja' => isset($satkerMap[$item['unit_kerja']]) ? $satkerMap[$item['unit_kerja']] : $item['unit_kerja'],
                'nama' => $item['nama'],
                'leave_type' => $item['nama_cuti']
            ];
        }

        $this->jsonResponse(['success' => true, 'data' => $events]);
    }

    public function getTypes()
    {
        if (!isLoggedIn()) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized']);
        }

        $types = $this->leaveTypeModel->all();
        $this->jsonResponse(['success' => true, 'data' => $types]);
    }

    public function getBalance()
    {
        if (!isLoggedIn()) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized']);
        }

        $tahun = isset($_POST['tahun']) ? cleanInput($_POST['tahun']) : date('Y');
        $userId = isset($_POST['user_id']) ? cleanInput($_POST['user_id']) : $_SESSION['user_id'];

        $balance = $this->leaveBalanceModel->getBalance($userId, $tahun);

        $this->jsonResponse(['success' => true, 'data' => $balance]);
    }

    public function cancel()
    {
        if (!isUser()) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized']);
        }

        $leaveId = cleanInput($_POST['leave_id']);

        // Cek apakah pengajuan milik user dan masih pending
        $leave = $this->leaveModel->find($leaveId);

        if ($leave && $leave['user_id'] == $_SESSION['user_id'] && ($leave['status'] == 'pending' || $leave['status'] == 'pending_admin_upload')) {
            $this->leaveModel->delete($leaveId);
            $this->jsonResponse(['success' => true, 'message' => 'Pengajuan cuti berhasil dibatalkan']);
        }
        else {
            $this->jsonResponse(['success' => false, 'message' => 'Pengajuan tidak dapat dibatalkan']);
        }
    }

    public function draft($leaveId = null)
    {
        requireUser();

        if (!$leaveId) {
            $this->redirect('leave');
        }

        $leave = $this->leaveModel->find($leaveId);

        // Validasi akses dan status
        if (!$leave || $leave['user_id'] != $_SESSION['user_id'] || $leave['status'] != 'draft') {
            $_SESSION['error'] = 'Pengajuan tidak ditemukan atau sudah diproses';
            $this->redirect('leave');
        }

        $data = [
            'title' => 'Upload Dokumen Cuti',
            'page_title' => 'Upload Dokumen Cuti',
            'leaveId' => $leaveId
        ];

        $this->view('leave/draft', $data);
    }

    public function generateBlanko()
    {
        requireLogin();

        $leaveId = cleanInput($_GET['id']);

        // Here you would generate the Word document
        // For now, just redirect back with message
        $_SESSION['message'] = 'Fitur generate blanko dalam pengembangan';
        $this->redirect('leave');
    }

    public function downloadDocument()
    {
        requireLogin();
        $filename = cleanInput($_GET['file']);
        $filepath = "public/uploads/documents/" . $filename;
        if (file_exists($filepath)) {
            $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
            if ($ext === 'pdf') {
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . basename($filepath) . '"');
            }
            else {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            }
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
        }
        else {
            $_SESSION['error'] = 'File tidak ditemukan';
            $this->redirect('leave');
        }
    }

    public function downloadGeneratedDoc()
    {
        requireLogin();

        $leaveId = cleanInput($_GET['id']);
        $leave = $this->leaveModel->find($leaveId);

        // Validasi akses: pemilik, admin, atau atasan yang bertanggung jawab (termasuk kabag, sekretaris, ketua)
        $accessAllowed = false;
        if ($leave && $leave['user_id'] == $_SESSION['user_id']) {
            $accessAllowed = true;
        }
        if (!$accessAllowed && isAdmin()) {
            $accessAllowed = true;
        }
        if (!$accessAllowed && function_exists('isAtasan') && isAtasan()) {
            // Cari id_atasan dari tabel atasan berdasarkan NIP session
            $atasanRow = $this->db()->fetch("SELECT a.id_atasan, a.role FROM atasan a WHERE a.NIP = ? LIMIT 1", [$_SESSION['nip']]);
            if ($atasanRow && !empty($atasanRow['id_atasan'])) {
                $id_atasan = $atasanRow['id_atasan'];
                $atasan_role = $atasanRow['role'] ?? null;

                // Check akses berdasarkan role
                if ($atasan_role === 'kasubbag') {
                    // Kasubbag bisa akses jika dia adalah atasan direkt atau kasubbag approver
                    if (isset($leave['atasan_id']) && $leave['atasan_id'] == $id_atasan) {
                        $accessAllowed = true;
                    }
                    elseif (isset($leave['kasubbag_id']) && $leave['kasubbag_id'] == $id_atasan && $leave['status'] === 'pending_kasubbag') {
                        $accessAllowed = true;
                    }
                }
                elseif ($atasan_role === 'kabag') {
                    // Kabag bisa akses jika dia adalah kabag_approver pada pending_kabag
                    if (isset($leave['kabag_approver_id']) && $leave['kabag_approver_id'] == $id_atasan && $leave['status'] === 'pending_kabag') {
                        $accessAllowed = true;
                    }
                }
                elseif ($atasan_role === 'sekretaris') {
                    // Sekretaris bisa akses jika dia adalah sekretaris_approver pada pending_sekretaris
                    if (isset($leave['sekretaris_approver_id']) && $leave['sekretaris_approver_id'] == $id_atasan && $leave['status'] === 'pending_sekretaris') {
                        $accessAllowed = true;
                    }
                }
                elseif ($atasan_role === 'ketua') {
                    // Ketua sebagai atasan langsung (level 1): boleh akses saat status 'pending'
                    // Ketua sebagai final approver: boleh akses saat awaiting_pimpinan, approved, dst.
                    $allowedStatuses = ['pending', 'pending_kasubbag', 'pending_kabag', 'pending_sekretaris', 'awaiting_pimpinan', 'approved', 'rejected', 'changed', 'postponed'];
                    if (in_array($leave['status'], $allowedStatuses)) {
                        if ((isset($leave['ketua_approver_id']) && $leave['ketua_approver_id'] == $id_atasan) ||
                        (isset($leave['atasan_id']) && $leave['atasan_id'] == $id_atasan)) {
                            $accessAllowed = true;
                        }
                    }
                }
                else {
                    // Regular atasan: hanya atasan direkt
                    if (isset($leave['atasan_id']) && $leave['atasan_id'] == $id_atasan) {
                        $accessAllowed = true;
                    }
                }
            }
        }

        if (!$leave || !$accessAllowed) {
            $_SESSION['error'] = 'Akses ditolak';
            $this->redirect('leave');
        }

        // Get document info
        $documentModel = $this->model('DocumentModel');
        $doc = $documentModel->getLatestByLeaveId($leaveId, 'generated');

        if (!$doc) {
            // Jika dokumen tidak ditemukan di database, coba generate ulang
            $this->regenerateDocument($leaveId);
            $doc = $documentModel->getLatestByLeaveId($leaveId, 'generated');

            if (!$doc) {
                $_SESSION['error'] = 'Dokumen tidak dapat di-generate';
                $this->redirect('leave');
            }
        }

        $baseDir = dirname(dirname(__DIR__)) . '/public/uploads/documents/';
        $filepath = $baseDir . 'temp/' . $doc['filename'];

        // Jika file tidak ada di disk di folder temp, coba beberapa lokasi alternatif
        if (!file_exists($filepath)) {
            $altPaths = [
                $baseDir . $doc['filename'],
                $baseDir . 'generated/' . $doc['filename'],
                $baseDir . 'temp/' . $doc['filename']
            ];
            $found = false;
            foreach ($altPaths as $p) {
                if (file_exists($p)) {
                    $filepath = $p;
                    $found = true;
                    break;
                }
            }

            // Jika belum ditemukan, hanya coba regenerate jika status == draft (backwards-compat)
            if (!$found && $leave['status'] === 'draft') {
                $this->regenerateDocument($leaveId);
                $doc = $documentModel->getLatestByLeaveId($leaveId, 'generated');
                if ($doc) {
                    $filepath = $baseDir . 'temp/' . $doc['filename'];
                    $found = file_exists($filepath);
                }
            }

            if (!$found) {
                $_SESSION['error'] = 'File tidak ditemukan';
                $this->redirect('leave');
            }
        }

        if (file_exists($filepath)) {
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="Blanko_Cuti_' . $leave['id'] . '_' . date('YmdHis') . '.docx"');
            header('Content-Length: ' . filesize($filepath));
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            readfile($filepath);
            exit;
        }
        else {
            $_SESSION['error'] = 'File tidak ditemukan';
            $this->redirect('leave');
        }
    }

    private function regenerateDocument($leaveId)
    {
        $leave = $this->leaveModel->find($leaveId);
        if (!$leave || $leave['status'] != 'draft') {
            return false;
        }

        // Get full leave data
        $sql = "SELECT lr.*, lt.nama_cuti, u.*, lr.catatan_approval, lr.approved_by
                FROM leave_requests lr
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                JOIN users u ON lr.user_id = u.id
                WHERE lr.id = ?";

        $fullData = $this->db()->fetch($sql, [$leaveId]);

        // Prepare data for document generation
        $leaveData = [
            'leave_type_id' => $fullData['leave_type_id'],
            'nomor_surat' => $fullData['nomor_surat'],
            'tanggal_mulai' => $fullData['tanggal_mulai'],
            'tanggal_selesai' => $fullData['tanggal_selesai'],
            'jumlah_hari' => $fullData['jumlah_hari'],
            'alasan' => $fullData['alasan'],
            'alamat_cuti' => $fullData['alamat_cuti'],
            'telepon_cuti' => $fullData['telepon_cuti'],
            'catatan_cuti' => $fullData['catatan_cuti'] ?? '',
            'status' => $fullData['status'],
            // include approver IDs so regenerateDocument can fill p1/p2/p3 correctly
            'atasan_id' => isset($fullData['atasan_id']) ? $fullData['atasan_id'] : null,
            'kasubbag_id' => isset($fullData['kasubbag_id']) ? $fullData['kasubbag_id'] : null,
            'kabag_approver_id' => isset($fullData['kabag_approver_id']) ? $fullData['kabag_approver_id'] : null,
            'sekretaris_approver_id' => isset($fullData['sekretaris_approver_id']) ? $fullData['sekretaris_approver_id'] : null,
            'ketua_approver_id' => isset($fullData['ketua_approver_id']) ? $fullData['ketua_approver_id'] : null,
            'approved_by' => $fullData['approved_by'],
            'catatan_approval' => $fullData['catatan_approval'] ?? ''
        ];

        $userData = [
            'id' => $fullData['user_id'],
            'nama' => $fullData['nama'],
            'nip' => $fullData['nip'],
            'jabatan' => $fullData['jabatan'],
            'golongan' => $fullData['golongan'],
            'unit_kerja' => $fullData['unit_kerja'],
            'tanggal_masuk' => $fullData['tanggal_masuk']
        ];

        // Generate document
        require_once dirname(dirname(__DIR__)) . '/app/helpers/document_helper.php';
        require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

        $isAfterApprove = ($leaveData['status'] === 'approved');
        $result = generateLeaveDocument($leaveData, $userData, 'blanko_cuti_template.docx', false, $isAfterApprove);

        if ($result['success']) {
            // Update or create document record
            $documentModel = $this->model('DocumentModel');
            $existingDoc = $documentModel->getLatestByLeaveId($leaveId, 'generated');

            if ($existingDoc) {
                // Delete old file if exists
                $oldPath = dirname(dirname(__DIR__)) . '/public/uploads/documents/temp/' . $existingDoc['filename'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }

                // Update record
                $documentModel->update($existingDoc['id'], [
                    'filename' => $result['filename'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            else {
                // Create new record
                $documentModel->createDocument([
                    'leave_request_id' => $leaveId,
                    'filename' => $result['filename'],
                    'document_type' => 'generated',
                    'status' => 'draft',
                    'created_by' => $_SESSION['user_id']
                ]);
            }

            return true;
        }

        return false;
    }

    public function uploadSignedDoc()
    {
        requireLogin();

        $leaveId = cleanInput($_POST['leave_id']);
        $leave = $this->leaveModel->find($leaveId);

        // Validasi akses dan status
        if (!$leave || $leave['user_id'] != $_SESSION['user_id']) {
            $this->jsonResponse(['success' => false, 'message' => 'Akses ditolak']);
        }

        if ($leave['status'] != 'draft') {
            $this->jsonResponse(['success' => false, 'message' => 'Pengajuan sudah diproses']);
        }

        if (!isset($_FILES['signed_document']) || $_FILES['signed_document']['error'] != 0) {
            $this->jsonResponse(['success' => false, 'message' => 'File tidak ditemukan']);
        }

        // Process upload
        require_once 'app/helpers/document_helper.php';
        $uploadResult = processUploadedDocument($_FILES['signed_document'], $leaveId, $_SESSION['user_id'], 'user_signed');

        if ($uploadResult['success']) {
            // Save to database
            $documentModel = $this->model('DocumentModel');
            $documentModel->createDocument([
                'leave_request_id' => $leaveId,
                'filename' => $uploadResult['filename'],
                'document_type' => 'user_signed',
                'status' => 'active',
                'created_by' => $_SESSION['user_id'],
                'upload_date' => date('Y-m-d H:i:s')
            ]);

            // Update leave status dan mark blanko uploaded
            $this->leaveModel->markBlankoUploaded($leaveId);

            // Send notification to the user's linked atasan (supervisor) only.
            $notificationModel = $this->model('Notification');
            $userModel = $this->model('User');
            $user = $userModel->find($_SESSION['user_id']);
            $notificationSent = false;
            $message = '';

            if ($user && !empty($user['atasan'])) {
                // Find the atasan account in users table that has user_type='atasan' and atasan column matching user's atasan id
                // The query should match: users with user_type='atasan' whose 'atasan' field matches the pegawai's 'atasan' id
                $atasanAccount = $this->db()->fetch(
                    "SELECT * FROM users WHERE user_type = 'atasan' AND atasan = ? LIMIT 1",
                [$user['atasan']]
                );

                if ($atasanAccount) {
                    $notificationModel->sendNotification(
                        $atasanAccount['id'],
                        "Pengajuan cuti baru dari " . $_SESSION['nama'] . " (" . $_SESSION['unit_kerja'] . ") menunggu rekomendasi Anda.",
                        'info',
                        $leaveId
                    );
                    $notificationSent = true;
                    $message = 'Blanko yang ditandatangani berhasil diupload. Pengajuan telah dikirim ke atasan untuk direkomendasikan ke admin.';
                }
            }

            if (!$notificationSent) {
                // Fallback: notify all admins (pimpinan) if no specific atasan account found
                $notificationModel->notifyAdmins("Pengajuan cuti baru dari " . $_SESSION['nama'] . " (" . $_SESSION['unit_kerja'] . ")", 'info', $leaveId);
                $message = 'Blanko yang ditandatangani berhasil diupload. Pengajuan telah dikirim ke admin untuk diproses.';
            }

            $this->jsonResponse([
                'success' => true,
                'message' => $message
            ]);
        }
        else {
            $this->jsonResponse(['success' => false, 'message' => $uploadResult['message']]);
        }
    }

    public function uploadApprovedDoc()
    {
        requireAdmin();

        $leaveId = cleanInput($_POST['leave_id']);
        $leave = $this->leaveModel->find($leaveId);

        // Validasi status - sekarang bisa untuk approved, rejected, changed, postponed
        if (!$leave || !in_array($leave['status'], ['approved', 'rejected', 'changed', 'postponed'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Pengajuan belum diproses']);
        }

        if (!isset($_FILES['approved_document']) || $_FILES['approved_document']['error'] != 0) {
            $this->jsonResponse(['success' => false, 'message' => 'File tidak ditemukan']);
        }

        // Process upload
        require_once 'app/helpers/document_helper.php';
        $uploadResult = processUploadedDocument($_FILES['approved_document'], $leaveId, $_SESSION['user_id'], 'admin_signed');

        if ($uploadResult['success']) {
            // Save to database
            $documentModel = $this->model('DocumentModel');
            $documentModel->createDocument([
                'leave_request_id' => $leaveId,
                'filename' => $uploadResult['filename'],
                'document_type' => 'admin_signed',
                'status' => 'final',
                'created_by' => $_SESSION['user_id'],
                'upload_date' => date('Y-m-d H:i:s')
            ]);

            // Update leave status to completed dan mark final blanko sent
            $this->leaveModel->markFinalBlankoSent($leaveId);

            // Log admin activity
            $adminActivityModel = $this->model('AdminActivity');
            $adminActivityModel->logActivity($_SESSION['user_id'], 'send_final_blanko', $leaveId);

            // Refresh data setelah update
            $leave = $this->leaveModel->find($leaveId);

            // === Pemotongan kuota hanya jika cuti disetujui (status=approved) dan sudah selesai (is_completed=1) ===
            if ($leave['status'] == 'approved' && $leave['is_completed'] == 1 && !$leave['quota_deducted']) {
                $tahunCuti = date('Y', strtotime($leave['tanggal_mulai']));
                kurangiKuotaByType($leave['user_id'], $leave['leave_type_id'], $leave['jumlah_hari'], $tahunCuti);
                $this->leaveModel->markQuotaDeducted($leaveId);
            }

            // Kirim notifikasi ke user sesuai dengan keputusan
            $notificationModel = $this->model('Notification');
            if ($leave['status'] == 'approved') {
                $notificationModel->sendNotification(
                    $leave['user_id'],
                    "Pengajuan cuti Anda telah disetujui oleh " . $_SESSION['nama'] . ". Blanko final telah tersedia untuk diunduh.",
                    'success',
                    $leaveId
                );
            }
            else {
                $notificationModel->sendNotification(
                    $leave['user_id'],
                    "Pengajuan cuti Anda telah ditolak oleh " . $_SESSION['nama'] . ". Catatan: " . $leave['catatan_approval'] . ". Blanko final telah tersedia untuk diunduh.",
                    'warning',
                    $leaveId
                );
            }

            $this->jsonResponse([
                'success' => true,
                'message' => 'Blanko final berhasil diupload dan notifikasi telah dikirim ke user.'
            ]);
        }
        else {
            $this->jsonResponse(['success' => false, 'message' => $uploadResult['message']]);
        }
    }

    public function downloadFinalDoc()
    {
        requireLogin();

        $leaveId = cleanInput($_GET['id']);
        $leave = $this->leaveModel->find($leaveId);

        // Validasi akses
        if (!$leave || ($leave['user_id'] != $_SESSION['user_id'] && !isAdmin())) {
            $_SESSION['error'] = 'Akses ditolak';
            $this->redirect('leave');
        }

        // Get final document
        $documentModel = $this->model('DocumentModel');
        $doc = $documentModel->getLatestByLeaveId($leaveId, 'admin_signed');

        if (!$doc) {
            $_SESSION['error'] = 'Dokumen final tidak ditemukan';
            $this->redirect('leave');
        }

        $filepath = dirname(dirname(__DIR__)) . '/public/uploads/documents/signed/' . $doc['filename'];

        if (file_exists($filepath)) {
            $extension = pathinfo($doc['filename'], PATHINFO_EXTENSION);
            $contentType = 'application/octet-stream';

            if ($extension == 'pdf') {
                $contentType = 'application/pdf';
            }
            elseif (in_array($extension, ['doc', 'docx'])) {
                $contentType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            }

            header('Content-Type: ' . $contentType);
            header('Content-Disposition: attachment; filename="Cuti_Approved_' . $leave['id'] . '_' . date('YmdHis') . '.' . $extension . '"');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        }
        else {
            $_SESSION['error'] = 'File tidak ditemukan';
            $this->redirect('leave');
        }
    }

    public function downloadSignedDoc()
    {
        requireLogin();

        $leaveId = cleanInput($_GET['id']);
        $leave = $this->leaveModel->find($leaveId);

        // Validasi akses
        if (!$leave || ($leave['user_id'] != $_SESSION['user_id'] && !isAdmin())) {
            $_SESSION['error'] = 'Akses ditolak';
            $this->redirect('leave');
        }

        // Get user signed document
        $documentModel = $this->model('DocumentModel');
        $doc = $documentModel->getLatestByLeaveId($leaveId, 'user_signed');

        if (!$doc) {
            $_SESSION['error'] = 'Dokumen tidak ditemukan';
            $this->redirect('leave');
        }

        $filepath = dirname(dirname(__DIR__)) . '/public/uploads/documents/signed/' . $doc['filename'];

        if (file_exists($filepath)) {
            $extension = pathinfo($doc['filename'], PATHINFO_EXTENSION);
            $contentType = 'application/octet-stream';

            if ($extension == 'pdf') {
                $contentType = 'application/pdf';
            }
            elseif (in_array($extension, ['doc', 'docx'])) {
                $contentType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            }

            header('Content-Type: ' . $contentType);
            header('Content-Disposition: attachment; filename="Cuti_User_Signed_' . $leave['id'] . '_' . date('YmdHis') . '.' . $extension . '"');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        }
        else {
            $_SESSION['error'] = 'File tidak ditemukan';
            $this->redirect('leave');
        }
    }

    public function documents()
    {
        requireUser();

        $data = [
            'title' => 'Dokumen Saya',
            'page_title' => 'Dokumen Saya'
        ];

        $this->view('leave/documents', $data);
    }

    public function deleteDraft()
    {
        requireUser();

        $leaveId = cleanInput($_POST['leave_id']);
        $leave = $this->leaveModel->find($leaveId);

        if ($leave && $leave['user_id'] == $_SESSION['user_id'] && $leave['status'] == 'draft') {
            // Delete associated documents
            $documentModel = $this->model('DocumentModel');
            $docs = $documentModel->getByLeaveId($leaveId);

            foreach ($docs as $doc) {
                // Delete physical files
                $paths = [
                    dirname(dirname(__DIR__)) . '/public/uploads/documents/temp/' . $doc['filename'],
                    dirname(dirname(__DIR__)) . '/public/uploads/documents/signed/' . $doc['filename']
                ];

                foreach ($paths as $path) {
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
            }

            // Delete from database (documents will be cascade deleted)
            $this->leaveModel->delete($leaveId);

            $this->jsonResponse(['success' => true, 'message' => 'Draft berhasil dihapus']);
        }
        else {
            $this->jsonResponse(['success' => false, 'message' => 'Draft tidak dapat dihapus']);
        }
    }
    public function getAlasanCuti()
    {
        if (!isLoggedIn()) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized']);
        }
        $leaveTypeId = isset($_POST['leave_type_id']) ? intval($_POST['leave_type_id']) : 0;
        if (!$leaveTypeId) {
            $this->jsonResponse(['success' => false, 'message' => 'ID jenis cuti tidak valid']);
        }
        $alasanCutiModel = $this->model('AlasanCuti');
        $alasanList = $alasanCutiModel->getByLeaveType($leaveTypeId);
        $this->jsonResponse(['success' => true, 'data' => $alasanList]);
    }

    // Tambahan: Hapus dokumen pendukung
    public function deleteSupportingDoc()
    {
        requireLogin();
        $leaveId = isset($_POST['leave_id']) ? intval($_POST['leave_id']) : 0;
        if (!$leaveId) {
            $this->jsonResponse(['success' => false, 'message' => 'ID pengajuan tidak valid']);
        }
        $leave = $this->leaveModel->find($leaveId);
        if (!$leave || $leave['user_id'] != $_SESSION['user_id'] || $leave['status'] != 'draft') {
            $this->jsonResponse(['success' => false, 'message' => 'Akses ditolak atau status bukan draft']);
        }
        // Hapus file fisik jika ada
        if (!empty($leave['dokumen_pendukung'])) {
            $filePath = dirname(dirname(__DIR__)) . '/public/uploads/documents/' . $leave['dokumen_pendukung'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        // Update database: kosongkan field dokumen_pendukung
        $this->leaveModel->update($leaveId, ['dokumen_pendukung' => '']);
        $this->jsonResponse(['success' => true, 'message' => 'Dokumen pendukung berhasil dihapus']);
    }
}