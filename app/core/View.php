<?php
class View {
    private $layout = 'main';
    private static $instance = null;
    
    public function render($view, $data = [], $useLayout = true) {
        extract($data);
        
        // Special case for login - no layout
        if ($view === 'auth/login') {
            $useLayout = false;
        }
        
        // Check if layout should be used
        if (!$useLayout || $this->layout === null || $this->layout === '') {
            require_once 'app/views/' . $view . '.php';
            return;
        }
        
        // Capture view content
        ob_start();
        require_once 'app/views/' . $view . '.php';
        $content = ob_get_clean();
        
        // Render with layout
        require_once 'app/views/layouts/' . $this->layout . '.php';
    }
    
    public function setLayout($layout) {
        $this->layout = $layout;
    }
    
    public function partial($partial, $data = []) {
        extract($data);
        require_once 'app/views/' . $partial . '.php';
    }
    
    public function isActive($path) {
        $currentUrl = $_GET['url'] ?? '';
        return trim($currentUrl, '/') === trim($path, '/') ? 'active' : '';
    }
}