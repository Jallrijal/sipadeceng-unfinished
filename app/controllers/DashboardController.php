<?php
class DashboardController extends Controller {
    
    public function index() {
        if (!isLoggedIn()) {
            $this->redirect('auth/login');
        }
        // Route to correct dashboard based on role: atasan -> atasan view, pimpinan -> pimpinan view, else pegawai
        if (isAtasan()) {
            $this->atasan();
        } elseif (isAdmin()) {
            $this->pimpinan();
        } else {
            $this->user();
        }
    }
    public function atasan() {
        if (!isAtasan()) {
            $this->redirect('dashboard');
        }

        $data = [
            'title' => 'Dashboard Atasan',
            'page_title' => 'Dashboard',
            // Tanda supaya view admin tidak menampilkan grafik untuk atasan
            'hide_charts' => true
        ];

        $this->view('dashboard/atasan', $data);
    }

    public function pimpinan() {
        if (!isAdmin()) {
            $this->redirect('dashboard/pegawai');
        }

        $data = [
            'title' => 'Dashboard Pimpinan',
            'page_title' => 'Dashboard'
        ];

        $this->view('dashboard/admin', $data);
    }
    
    public function user() {
        if (!isUser()) {
            $this->redirect('dashboard/atasan');
        }
        
        $data = [
            'title' => 'Dashboard User',
            'page_title' => 'Dashboard'
        ];
        
        $this->view('dashboard/pegawai', $data);
    }
}