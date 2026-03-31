<?php
class App {
    protected $controller = 'AuthController';
    protected $method = 'welcome';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // Server-side session timeout enforcement ✅
        if (isset($_SESSION['user_id'])) {
            $now = time();
            if (!isset($_SESSION['last_activity'])) {
                $_SESSION['last_activity'] = $now;
            } else {
                $elapsed = $now - $_SESSION['last_activity'];
                if ($elapsed > SESSION_TIMEOUT_SECONDS) {
                    // Expire session
                    session_unset();
                    session_destroy();

                    // AJAX clients get JSON response, normal requests redirect to login
                    if ($this->isAjaxRequest()) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => 'session_expired']);
                        exit;
                    } else {
                        header('Location: ' . baseUrl('auth/login?expired=1'));
                        exit;
                    }
                } else {
                    // Update last activity on each request
                    $_SESSION['last_activity'] = $now;
                }
            }
        }

        // Special handling for AJAX requests
    if ($this->isAjaxRequest()) {
        // Skip login check for AJAX if accessing public methods
        $publicMethods = ['login', 'logout'];
        $currentMethod = isset($url[1]) ? $url[1] : 'index';
        
        if (!isset($_SESSION['user_id']) && !in_array($currentMethod, $publicMethods)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
    }
        
        // Jika belum login, redirect ke halaman Welcome (kecuali akses auth)
        if (!isset($_SESSION['user_id']) && !(isset($url[0]) && $url[0] == 'auth')) {
            $this->controller = 'AuthController';
            $this->method = 'welcome';
        } else {
            // Controller
            if (isset($url[0])) {
                $controllerName = ucfirst($url[0]) . 'Controller';
                if (file_exists('app/controllers/' . $controllerName . '.php')) {
                    $this->controller = $controllerName;
                } else {
                    // Jika controller tidak ditemukan, fallback ke AuthController
                    $this->controller = 'AuthController';
                    $this->method = 'login';
                }
                unset($url[0]);
                // Jika user meminta controller secara eksplisit tetapi tidak menyertakan method,
                // set default method ke 'index' agar route seperti /dashboard memanggil index().
                // Method ini dapat dioverride oleh nilai di $url[1] jika disediakan.
                if (!isset($url[1])) {
                    $this->method = 'index';
                }
            }
            // Method
            if (isset($url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }
        
        require_once 'app/controllers/' . $this->controller . '.php';
            $this->controller = new $this->controller;

            if (method_exists($this->controller, $this->method)) {
                // Params
                $this->params = $url ? array_values($url) : [];
                
                // Check if method expects parameters
                $reflection = new ReflectionMethod($this->controller, $this->method);
                $paramCount = $reflection->getNumberOfParameters();
                
                if ($paramCount > 0 && count($this->params) > 0) {
                    // Pass first parameter directly for methods like draft($id)
                    call_user_func_array([$this->controller, $this->method], $this->params);
                } else {
                    // Call without parameters
                    call_user_func([$this->controller, $this->method]);
                }
            } else {
                $this->show404();
            }
    }
    
    public function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
    
    private function show404() {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Page Not Found</h1>";
        exit;
    }

    private function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }       
}

