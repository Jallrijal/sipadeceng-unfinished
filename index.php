<?php
require_once __DIR__ . '/vendor/autoload.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
date_default_timezone_set('Asia/Makassar');

// Manual require core classes untuk memastikan tersedia
require_once 'app/core/Database.php';
require_once 'app/core/Model.php';
require_once 'app/core/Controller.php';
require_once 'app/core/View.php';
require_once 'app/core/ErrorHandler.php';
require_once 'app/core/App.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = ['app/core/', 'app/controllers/', 'app/models/'];
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Load helpers
require_once 'app/helpers/auth_helper.php';
require_once 'app/helpers/date_helper.php';
require_once 'app/helpers/general_helper.php';
require_once 'app/helpers/direct_superior_helper.php';

// Load config
require_once 'config/database.php';
require_once 'config/session.php';

// Initialize App
$app = new App();