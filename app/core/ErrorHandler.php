<?php
// Custom error handler untuk development
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Custom error handler untuk AJAX requests
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (error_reporting() === 0) {
        return false;
    }
    
    // Check if this is an AJAX request
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    if ($isAjax) {
        // For AJAX, return JSON error
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => true,
            'message' => "Error: $errstr in $errfile on line $errline"
        ]);
        exit;
    }
    
    // For normal requests, display error
    echo "<b>Error:</b> [$errno] $errstr<br>";
    echo "File: $errfile<br>";
    echo "Line: $errline<br>";
    
    return true;
});

// Custom exception handler
set_exception_handler(function($exception) {
    // Check if this is an AJAX request
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    if ($isAjax) {
        // For AJAX, return JSON error
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => true,
            'message' => "Exception: " . $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ]);
        exit;
    }
    
    // For normal requests, display error
    echo "<b>Exception:</b> " . $exception->getMessage() . "<br>";
    echo "File: " . $exception->getFile() . "<br>";
    echo "Line: " . $exception->getLine() . "<br>";
    echo "<pre>" . $exception->getTraceAsString() . "</pre>";
});