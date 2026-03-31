<?php
class UserSnapshotController extends Controller {
    private $userSnapshotModel;
    private $userModel;
    
    public function __construct() {
        $this->userSnapshotModel = $this->model('UserSnapshot');
        $this->userModel = $this->model('User');
    }
    
    /**
     * Menampilkan daftar user snapshots
     */
    public function index() {
        if (!isAdmin()) {
            $this->redirect('dashboard');
        }
        
        $filters = [];
        if (isset($_GET['type'])) {
            $filters['type'] = $_GET['type'];
        }
        if (isset($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        
        $snapshots = $this->userSnapshotModel->getAll($filters);
        
        $data = [
            'title' => 'Riwayat Perubahan User',
            'page_title' => 'Riwayat Perubahan User',
            'snapshots' => $snapshots,
            'filters' => $filters
        ];
        
        $this->view('user/snapshots', $data);
    }
    
    /**
     * Menampilkan detail snapshot
     */
    public function detail($id) {
        if (!isAdmin()) {
            $this->redirect('dashboard');
        }
        
        $snapshot = $this->userSnapshotModel->findById($id);
        if (!$snapshot) {
            $this->redirect('user/snapshots');
        }
        
        // Ambil data user aktif jika masih ada
        $activeUser = null;
        if ($snapshot['original_user_id']) {
            $activeUser = $this->userModel->findById($snapshot['original_user_id']);
        }
        
        // Ambil riwayat cuti user ini
        $leaveModel = $this->model('Leave');
        $leaveHistory = $leaveModel->getHistory(['user_id' => $snapshot['original_user_id']]);
        
        $data = [
            'title' => 'Detail Snapshot User',
            'page_title' => 'Detail Snapshot User',
            'snapshot' => $snapshot,
            'activeUser' => $activeUser,
            'leaveHistory' => $leaveHistory
        ];
        
        $this->view('user/snapshot_detail', $data);
    }
    
    /**
     * Mencari snapshot
     */
    public function search() {
        if (!isAdmin()) {
            $this->redirect('dashboard');
        }
        
        $keyword = $_GET['keyword'] ?? '';
        $snapshots = $this->userSnapshotModel->searchSnapshots($keyword);
        
        $data = [
            'title' => 'Hasil Pencarian Snapshot',
            'page_title' => 'Hasil Pencarian Snapshot',
            'snapshots' => $snapshots,
            'keyword' => $keyword
        ];
        
        $this->view('user/snapshots', $data);
    }
    
    /**
     * Menampilkan statistik snapshot
     */
    public function stats() {
        if (!isAdmin()) {
            $this->redirect('dashboard');
        }
        
        $stats = $this->userSnapshotModel->getSnapshotStats();
        
        $data = [
            'title' => 'Statistik Perubahan User',
            'page_title' => 'Statistik Perubahan User',
            'stats' => $stats
        ];
        
        $this->view('user/snapshot_stats', $data);
    }
    
    /**
     * Membuat snapshot manual (untuk testing)
     */
    public function createSnapshot($userId) {
        if (!isAdmin()) {
            $this->redirect('dashboard');
        }
        
        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->redirect('user/snapshots');
        }
        
        $snapshotType = $_POST['snapshot_type'] ?? 'modified';
        $reason = $_POST['reason'] ?? 'Manual snapshot creation';
        
        $result = $this->userSnapshotModel->createSnapshot($user, $snapshotType, $reason);
        
        if ($result) {
            setFlashMessage('success', 'Snapshot berhasil dibuat');
        } else {
            setFlashMessage('error', 'Gagal membuat snapshot');
        }
        
        $this->redirect('user/snapshots');
    }
} 