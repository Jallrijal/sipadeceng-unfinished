<?php
abstract class BaseController extends Controller {
    protected $db;
    protected $view;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->view = new View();
    }
    
    protected function requireLogin() {
        if (!isLoggedIn()) {
            if ($this->isAjax()) {
                $this->jsonResponse(['success' => false, 'message' => 'Unauthorized']);
            } else {
                $this->redirect('auth/login');
            }
        }
    }
    
    protected function requireAdmin() {
        $this->requireLogin();
        if (!isAdmin()) {
            if ($this->isAjax()) {
                $this->jsonResponse(['success' => false, 'message' => 'Admin access required']);
            } else {
                $this->redirect('dashboard');
            }
        }
    }
    
    protected function requireUser() {
        $this->requireLogin();
        if (!isUser()) {
            if ($this->isAjax()) {
                $this->jsonResponse(['success' => false, 'message' => 'User access required']);
            } else {
                $this->redirect('dashboard');
            }
        }
    }
}