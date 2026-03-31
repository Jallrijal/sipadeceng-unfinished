<?php
require_once dirname(__DIR__) . '/helpers/signature_helper.php';
require_once dirname(__DIR__) . '/helpers/response_helper.php';

class SignatureController extends BaseController {
    public function index() {
        requireLogin();
        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];
        $signatureType = getSignatureTypeByUserType($userType);
        $signature = getUserSignature($userId, $signatureType);
        $placeholders = getSignaturePlaceholders();
        $view = new View();
        $view->render('user/signature_manage', [
            'signature' => $signature,
            'signatureType' => $signatureType,
            'placeholders' => $placeholders
        ]);
    }

    public function paraf() {
        requireLogin();
        // Hanya admin yang bisa mengakses halaman paraf
        if ($_SESSION['user_type'] !== 'admin') {
            $_SESSION['error'] = 'Akses Ditolak. Halaman ini hanya untuk Admin.';
            $this->redirect('dashboard');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $paraf = getUserSignature($userId, 'paraf');
        $view = new View();
        $view->render('user/paraf_manage', [
            'paraf' => $paraf
        ]);
    }

    public function upload() {
        requireLogin();
        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];
        $signatureType = getSignatureTypeByUserType($userType);
        if (!isset($_FILES['signature_file'])) {
            jsonResponse(['success' => false, 'message' => 'File tidak ditemukan.']);
            return;
        }
        $file = $_FILES['signature_file'];
        $result = uploadSignatureFile($file, $userId, $signatureType);
        if ($result['success']) {
            saveUserSignature($userId, $signatureType, $result['filename'], $result['size'], $result['type']);
            jsonResponse(['success' => true, 'message' => 'Tanda tangan berhasil diupload.', 'filename' => $result['filename'], 'url' => getSignatureUrl($result['filename'])]);
        } else {
            jsonResponse(['success' => false, 'message' => $result['message']]);
        }
    }

    public function uploadParaf() {
        requireLogin();
        // Hanya admin yang bisa upload paraf
        if ($_SESSION['user_type'] !== 'admin') {
            jsonResponse(['success' => false, 'message' => 'Akses Ditolak. Fitur ini hanya untuk Admin.']);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        if (!isset($_FILES['paraf_file'])) {
            jsonResponse(['success' => false, 'message' => 'File tidak ditemukan.']);
            return;
        }
        $file = $_FILES['paraf_file'];
        $result = uploadParafFile($file, $userId);
        if ($result['success']) {
            saveUserSignature($userId, 'paraf', $result['filename'], $result['size'], $result['type']);
            jsonResponse(['success' => true, 'message' => 'Paraf berhasil diupload.', 'filename' => $result['filename'], 'url' => getSignatureUrl($result['filename'])]);
        } else {
            jsonResponse(['success' => false, 'message' => $result['message']]);
        }
    }

    public function preview() {
        requireLogin();
        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];
        $signatureType = getSignatureTypeByUserType($userType);
        $signature = getUserSignature($userId, $signatureType);
        if ($signature) {
            jsonResponse(['success' => true, 'url' => getSignatureUrl($signature['signature_file'])]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Tanda tangan belum diupload.']);
        }
    }

    public function previewParaf() {
        requireLogin();
        // Hanya admin yang bisa preview paraf
        if ($_SESSION['user_type'] !== 'admin') {
            jsonResponse(['success' => false, 'message' => 'Akses Ditolak. Fitur ini hanya untuk Admin.']);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $paraf = getUserSignature($userId, 'paraf');
        if ($paraf) {
            jsonResponse(['success' => true, 'url' => getSignatureUrl($paraf['signature_file'])]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Paraf belum diupload.']);
        }
    }

    public function delete() {
        requireLogin();
        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];
        $signatureType = getSignatureTypeByUserType($userType);
        $signature = getUserSignature($userId, $signatureType);
        if ($signature) {
            deleteSignatureFile($signature['signature_file']);
            deleteUserSignature($userId, $signatureType);
            jsonResponse(['success' => true, 'message' => 'Tanda tangan berhasil dihapus.']);
        } else {
            jsonResponse(['success' => false, 'message' => 'Tanda tangan tidak ditemukan.']);
        }
    }

    public function deleteParaf() {
        requireLogin();
        // Hanya admin yang bisa hapus paraf
        if ($_SESSION['user_type'] !== 'admin') {
            jsonResponse(['success' => false, 'message' => 'Akses Ditolak. Fitur ini hanya untuk Admin.']);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $paraf = getUserSignature($userId, 'paraf');
        if ($paraf) {
            deleteSignatureFile($paraf['signature_file']);
            deleteUserSignature($userId, 'paraf');
            jsonResponse(['success' => true, 'message' => 'Paraf berhasil dihapus.']);
        } else {
            jsonResponse(['success' => false, 'message' => 'Paraf tidak ditemukan.']);
        }
    }

    // Untuk admin: melihat semua tanda tangan user
    public function all() {
        requireAdmin();
        $signatures = getAllUserSignatures();
        $view = new View();
        $view->render('admin/signature_list', [
            'signatures' => $signatures
        ]);
    }

    /**
     * Paraf khusus untuk atasan cuti (kasubbag, kabag, sekretaris)
     */
    public function parafAtasanCuti() {
        requireLogin();
        
        // Hanya atasan yang bisa mengakses
        if ($_SESSION['user_type'] !== 'atasan') {
            $_SESSION['error'] = 'Halaman ini hanya untuk atasan cuti.';
            $this->redirect('dashboard');
            return;
        }

        $userId = $_SESSION['user_id'];
        $atasanModel = $this->model('Atasan');
        $userModel = $this->model('User');
        
        // Cari atasan yang terkait dengan user ini
        $user = $userModel->find($userId);

        // Jika akun adalah atasan, resolve id_atasan berdasarkan NIP di tabel atasan
        $atasanId = null;
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'atasan') {
            $db = Database::getInstance();
            $userNip = $user['nip'] ?? null;
            if ($userNip) {
                $row = $db->fetch("SELECT id_atasan FROM atasan WHERE NIP = ? LIMIT 1", [$userNip]);
                if ($row && isset($row['id_atasan'])) {
                    $atasanId = $row['id_atasan'];
                }
            }
        } else {
            // Untuk pegawai biasa, gunakan kolom atasan di tabel users
            $atasanId = $user['atasan'] ?? null;
        }

        if (!$user || !$atasanId) {
            $_SESSION['error'] = 'Data atasan tidak ditemukan.';
            $this->redirect('dashboard');
            return;
        }

        $atasan = $atasanModel->find($atasanId);
        if (!$atasan || !$atasan['role'] || !in_array($atasan['role'], ['kasubbag', 'kabag', 'sekretaris'])) {
            $_SESSION['error'] = 'Anda tidak memiliki role yang sesuai untuk mengupload paraf khusus.';
            $this->redirect('dashboard');
            return;
        }

        $role = $atasan['role'];
        $signatureType = 'paraf_' . $role; // paraf_kasubbag, paraf_kabag, paraf_sekretaris
        
        $paraf = getUserSignature($userId, $signatureType);
        
        $view = new View();
        $view->render('atasan/paraf_khusus_cuti', [
            'paraf' => $paraf,
            'signatureType' => $signatureType,
            'role' => $role,
            'atasanData' => $atasan
        ]);
    }

    /**
     * Upload paraf khusus untuk atasan cuti
     */
    public function uploadParafAtasanCuti() {
        requireLogin();
        
        // Hanya atasan yang bisa mengupload
        if ($_SESSION['user_type'] !== 'atasan') {
            jsonResponse(['success' => false, 'message' => 'Fitur ini hanya untuk atasan cuti.']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $userModel = $this->model('User');
        $atasanModel = $this->model('Atasan');
        
        // Validasi user adalah atasan dengan role tertentu
        $user = $userModel->find($userId);
        // resolve atasan id (support accounts that are themselves atasan)
        $atasanId = null;
        if ($user) {
            if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'atasan') {
                $db = Database::getInstance();
                $userNip = $user['nip'] ?? null;
                if ($userNip) {
                    $row = $db->fetch("SELECT id_atasan FROM atasan WHERE NIP = ? LIMIT 1", [$userNip]);
                    if ($row && isset($row['id_atasan'])) {
                        $atasanId = $row['id_atasan'];
                    }
                }
            } else {
                $atasanId = $user['atasan'] ?? null;
            }
        }

        if (!$user || !$atasanId) {
            jsonResponse(['success' => false, 'message' => 'Data atasan tidak ditemukan.']);
            return;
        }

        $atasan = $atasanModel->find($atasanId);
        if (!$atasan || !$atasan['role'] || !in_array($atasan['role'], ['kasubbag', 'kabag', 'sekretaris'])) {
            jsonResponse(['success' => false, 'message' => 'Anda tidak memiliki role yang sesuai.']);
            return;
        }

        if (!isset($_FILES['paraf_file'])) {
            jsonResponse(['success' => false, 'message' => 'File tidak ditemukan.']);
            return;
        }

        $file = $_FILES['paraf_file'];
        $role = $atasan['role'];
        $signatureType = 'paraf_' . $role;
        
        $result = uploadParafAtasanCutiFile($file, $userId, $role);
        
        if ($result['success']) {
            saveUserSignature($userId, $signatureType, $result['filename'], $result['size'], $result['type']);
            jsonResponse([
                'success' => true, 
                'message' => 'Paraf khusus atasan cuti berhasil diupload.', 
                'filename' => $result['filename'], 
                'url' => getSignatureUrl($result['filename'])
            ]);
        } else {
            jsonResponse(['success' => false, 'message' => $result['message']]);
        }
    }

    /**
     * Delete paraf khusus untuk atasan cuti
     */
    public function deleteParafAtasanCuti() {
        requireLogin();
        
        // Hanya atasan yang bisa menghapus
        if ($_SESSION['user_type'] !== 'atasan') {
            jsonResponse(['success' => false, 'message' => 'Fitur ini hanya untuk atasan cuti.']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $userModel = $this->model('User');
        $atasanModel = $this->model('Atasan');
        
        // Validasi user adalah atasan dengan role tertentu
        $user = $userModel->find($userId);
        // resolve atasan id like upload method
        $atasanId = null;
        if ($user) {
            if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'atasan') {
                $db = Database::getInstance();
                $userNip = $user['nip'] ?? null;
                if ($userNip) {
                    $row = $db->fetch("SELECT id_atasan FROM atasan WHERE NIP = ? LIMIT 1", [$userNip]);
                    if ($row && isset($row['id_atasan'])) {
                        $atasanId = $row['id_atasan'];
                    }
                }
            } else {
                $atasanId = $user['atasan'] ?? null;
            }
        }
        if (!$user || !$atasanId) {
            jsonResponse(['success' => false, 'message' => 'Data atasan tidak ditemukan.']);
            return;
        }

        $atasan = $atasanModel->find($atasanId);
        if (!$atasan || !$atasan['role'] || !in_array($atasan['role'], ['kasubbag', 'kabag', 'sekretaris'])) {
            jsonResponse(['success' => false, 'message' => 'Anda tidak memiliki role yang sesuai.']);
            return;
        }

        $role = $atasan['role'];
        $signatureType = 'paraf_' . $role;
        
        $paraf = getUserSignature($userId, $signatureType);
        if ($paraf) {
            deleteSignatureFile($paraf['signature_file']);
            deleteUserSignature($userId, $signatureType);
            jsonResponse(['success' => true, 'message' => 'Paraf khusus atasan cuti berhasil dihapus.']);
        } else {
            jsonResponse(['success' => false, 'message' => 'Paraf tidak ditemukan.']);
        }
    }
}