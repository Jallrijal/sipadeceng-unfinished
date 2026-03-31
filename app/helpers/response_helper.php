<?php
// Helper untuk memastikan clean JSON response
function jsonResponse($data) {
    // Clear any previous output
    if (ob_get_length()) {
        ob_clean();
    }
    
    // Set proper headers
    header('Content-Type: application/json; charset=utf-8');
    
    // Disable error reporting untuk production
    if (!isset($data['debug'])) {
        error_reporting(0);
        ini_set('display_errors', 0);
    }
    
    // Output JSON
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Helper untuk handle database errors
function handleDatabaseError($error) {
    error_log("Database Error: " . $error);
    jsonResponse([
        'success' => false,
        'message' => 'Terjadi kesalahan database',
        'debug' => $error // Remove this in production
    ]);
}