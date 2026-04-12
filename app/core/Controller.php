<?php
require_once 'app/core/DatabaseHelper.php';

class Controller {
    use DatabaseHelper;
    
    protected function model($model) {
        require_once 'app/models/' . $model . '.php';
        return new $model();
    }
    
    protected function view($view, $data = [], $useLayout = true) {
        $viewObj = new View();
        $viewObj->render($view, $data, $useLayout);
    }
    
    protected function redirect($url) {
        header('Location: ' . $this->baseUrl($url));
        exit;
    }
    
    protected function baseUrl($url = '') {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $base = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
        return rtrim($base, '\\/') . '/' . ltrim($url, '/');
    }
    
    protected function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    protected function jsonResponse($data) {
        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}