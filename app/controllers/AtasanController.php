<?php
class AtasanController extends Controller {
    private $atasanModel;
    
    public function __construct() {
        $this->atasanModel = $this->model('Atasan');
    }
    
    public function index() {
        requireAdmin();
        
        $data = [
            'title' => 'Kelola Atasan',
            'page_title' => 'Kelola Atasan'
        ];
        
        $this->view('atasan/index', $data);
    }
    
    public function getAtasanList() {
        requireAdmin();
        
        $atasanList = $this->atasanModel->getAtasanWithUserCount();
        
        $this->jsonResponse([
            'success' => true,
            'data' => $atasanList
        ]);
    }
    
    public function add() {
        requireAdmin();
        
        $data = [
            'title' => 'Tambah Atasan',
            'page_title' => 'Tambah Atasan',
            'atasan' => null,
            'action' => 'add'
        ];
        
        $this->view('atasan/form', $data);
    }
    
    public function edit($id) {
        requireAdmin();
        
        $atasan = $this->atasanModel->find($id);
        
        if (!$atasan) {
            redirect('atasan/index');
        }
        
        $data = [
            'title' => 'Edit Atasan',
            'page_title' => 'Edit Atasan',
            'atasan' => $atasan,
            'action' => 'edit'
        ];
        
        $this->view('atasan/form', $data);
    }
    
    public function save() {
        requireAdmin();
        
        $action = $_POST['action'];
        $data = [
            'nama_atasan' => cleanInput($_POST['nama_atasan']),
            'NIP' => cleanInput($_POST['NIP']),
            'jabatan' => isset($_POST['jabatan']) ? cleanInput($_POST['jabatan']) : ''
        ];
        // handle optional role value
        $role = isset($_POST['role']) ? cleanInput($_POST['role']) : null;
        $validRoles = ['kasubbag','kabag','sekretaris','ketua'];
        $data['role'] = in_array($role, $validRoles) ? $role : null;
        
        // Validasi NIP
        if (strlen($data['NIP']) < 14) {
            $this->jsonResponse(['success' => false, 'message' => 'NIP harus minimal 14 digit']);
            return;
        }
        
        // Definisikan atasanId untuk action edit
        $atasanId = null;
        if ($action == 'edit') {
            $atasanId = cleanInput($_POST['id']);
        }
        
        // Validasi duplikasi NIP
        if ($this->atasanModel->isNipExists($data['NIP'], $atasanId)) {
            $this->jsonResponse(['success' => false, 'message' => 'NIP sudah terdaftar']);
            return;
        }
        
        if ($action == 'add') {
            $result = $this->atasanModel->create($data);
            $message = 'Atasan berhasil ditambahkan';
        } else {
            $result = $this->atasanModel->update($atasanId, $data);
            $message = 'Atasan berhasil diupdate';
        }
        
        if ($result) {
            $this->jsonResponse(['success' => true, 'message' => $message]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Gagal menyimpan data atasan']);
        }
    }
    
    public function delete($id) {
        requireAdmin();
        
        // Cek apakah ada user yang menggunakan atasan ini
        $userModel = $this->model('User');
        $users = $userModel->getUsersByAtasan($id);
        
        if (!empty($users)) {
            $this->jsonResponse([
                'success' => false, 
                'message' => 'Tidak dapat menghapus atasan karena masih ada user yang menggunakan atasan ini'
            ]);
            return;
        }
        
        $result = $this->atasanModel->delete($id);
        
        if ($result) {
            $this->jsonResponse(['success' => true, 'message' => 'Atasan berhasil dihapus']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Gagal menghapus atasan']);
        }
    }
    
    public function detail($id) {
        requireAdmin();
        
        $atasan = $this->atasanModel->find($id);
    $users = $this->atasanModel->getUsersUnderAtasan($id); // Sudah urut id di model
        
        if (!$atasan) {
            redirect('atasan/index');
        }
        
        $data = [
            'title' => 'Detail Atasan',
            'page_title' => 'Detail Atasan',
            'atasan' => $atasan,
            'users' => $users
        ];
        
        $this->view('atasan/detail', $data);
    }
}
