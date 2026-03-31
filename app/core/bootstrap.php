<?php
// Bootstrap file to ensure all necessary files are loaded

// Core files that must be loaded first
$coreFiles = [
    'app/core/Database.php',
    'app/core/Model.php',
    'app/core/Controller.php',
    'app/core/View.php',
    'app/core/ErrorHandler.php'
];

foreach ($coreFiles as $file) {
    if (file_exists($file)) {
        require_once $file;
    } else {
        die("Core file missing: $file");
    }
}

// Ensure Database class is globally available
if (!class_exists('Database')) {
    die("Database class not loaded!");
}