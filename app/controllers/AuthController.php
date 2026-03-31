<?php
class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = $this->model('User');
    }
    
    public function welcome() {
        // Render halaman Welcome
        $this->view('auth/Welcome', [], false);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = cleanInput($_POST['username']);
            $password = cleanInput($_POST['password']);
            
            if (empty($username) || empty($password)) {
                $this->jsonResponse(['success' => false, 'message' => 'Username dan password harus diisi!']);
                return;
            }
            
            $user = $this->userModel->findByUsername($username);
            
            // Jika user tidak ada, jangan beri detail yang berlebihan
            if (!$user) {
                // sedikit delay untuk mengurangi brute force/enum
                usleep(200000);
                $this->jsonResponse(['success' => false, 'message' => 'Username atau password salah!']);
                return;
            }
            
            // Check if user is active (not deleted/inactive)
            if ($user['is_deleted']) {
                $this->jsonResponse(['success' => false, 'message' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.']);
                return;
            }

            // Cek apakah akun sedang terkunci
            if (!empty($user['lock_until']) && strtotime($user['lock_until']) > time()) {
                $remaining = strtotime($user['lock_until']) - time();
                $minutes = ceil($remaining / 60);
                $this->jsonResponse(['success' => false, 'message' => "Akun terkunci karena terlalu banyak percobaan login salah. Silakan coba lagi dalam $minutes menit."]);
                return;
            }
            
            if ($user && password_verify($password, $user['password'])) {
                // Reset failed attempts dan lock
                $this->userModel->update($user['id'], ['failed_login_attempts' => 0, 'lock_until' => null]);

                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['nip'] = $user['nip'];
                $_SESSION['jabatan'] = $user['jabatan'];
                $_SESSION['golongan'] = $user['golongan'];
                $_SESSION['unit_kerja'] = $user['unit_kerja'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['tanggal_masuk'] = $user['tanggal_masuk'];
                
                // If atasan, fetch their role from atasan table
                if ($user['user_type'] === 'atasan') {
                    $atasanInfo = $this->db()->fetch(
                        "SELECT role FROM atasan WHERE NIP = ? LIMIT 1",
                        [$user['nip']]
                    );
                    $_SESSION['atasan_role'] = $atasanInfo['role'] ?? null;
                }
                
                // Initialize last activity for session timeout
                $_SESSION['last_activity'] = time();
                
                // Redirect to generic dashboard; DashboardController akan merutekan berdasarkan role
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'redirect' => $this->baseUrl('dashboard')
                ]);
            } else {
                // Increment failed attempts
                $attempts = isset($user['failed_login_attempts']) ? (int)$user['failed_login_attempts'] + 1 : 1;
                $data = ['failed_login_attempts' => $attempts];
                $message = 'Username atau password salah!';

                if ($attempts >= 3) {
                    // Lock account for 1 hour
                    $lockUntil = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    $data['lock_until'] = $lockUntil;
                    $message = 'Anda telah memasukkan password salah 3 kali. Akun terkunci selama 1 jam.';
                } else {
                    $remaining = 3 - $attempts;
                    $message = "Username atau password salah! Tersisa $remaining percobaan.";
                }

                $this->userModel->update($user['id'], $data);
                $this->jsonResponse(['success' => false, 'message' => $message]);
            }
        } else {
            // Pass false to disable layout for login page
            $this->view('auth/login', [], false);
        }
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('auth/login');
    }
    
    // Keep session alive (AJAX)
    public function keepalive() {
        if (!isLoggedIn()) {
            $this->jsonResponse(['success' => false, 'message' => 'Not logged in']);
        }

        $_SESSION['last_activity'] = time();
        $this->jsonResponse(['success' => true, 'message' => 'OK', 'remaining' => SESSION_TIMEOUT_SECONDS]);
    }

    // Return session status (remaining seconds)
    public function sessionStatus() {
        if (!isLoggedIn()) {
            $this->jsonResponse(['success' => false, 'message' => 'Not logged in']);
        }

        $last = $_SESSION['last_activity'] ?? time();
        $remaining = SESSION_TIMEOUT_SECONDS - (time() - $last);
        if ($remaining < 0) $remaining = 0;

        $this->jsonResponse([
            'success' => true,
            'remaining' => $remaining,
            'warning' => SESSION_WARNING_SECONDS,
            'timeout' => SESSION_TIMEOUT_SECONDS,
            'throttle' => SESSION_KEEPALIVE_THROTTLE_SECONDS
        ]);
    }

    public function changePassword() {
        if (!isLoggedIn()) {
            $this->jsonResponse(['success' => false, 'message' => 'Not logged in']);
        }
        
        $oldPassword = cleanInput($_POST['old_password']);
        $newPassword = cleanInput($_POST['new_password']);
        $confirmPassword = cleanInput($_POST['confirm_password']);
        
        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            $this->jsonResponse(['success' => false, 'message' => 'Semua field harus diisi!']);
        }
        
        if ($newPassword !== $confirmPassword) {
            $this->jsonResponse(['success' => false, 'message' => 'Password baru dan konfirmasi tidak cocok!']);
        }
        
        if (strlen($newPassword) < 6) {
            $this->jsonResponse(['success' => false, 'message' => 'Password minimal 6 karakter!']);
        }
        
        $user = $this->userModel->find($_SESSION['user_id']);
        
        if (!password_verify($oldPassword, $user['password'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Password lama salah!']);
        }
        
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->userModel->update($_SESSION['user_id'], ['password' => $newPasswordHash]);
        
        $this->jsonResponse(['success' => true, 'message' => 'Password berhasil diubah!']);
    }
}