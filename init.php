<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
define('ROOT_PATH', __DIR__); 
error_log("=== Session Data in init.php ===");
error_log("Current path: " . $_SERVER['PHP_SELF']);
error_log("Session ID: " . session_id()); 
foreach ($_SESSION as $key => $value) {
    if (is_string($value) || is_numeric($value)) {
        error_log("$key: $value");
    } else {
        error_log("$key: " . print_r($value, true));
    }
} 
$current_path = $_SERVER['PHP_SELF'];
if (strpos($current_path, '/admin/') !== false) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_name'])) {
        error_log("No valid session - redirecting to login");
        header('Location: ' . ROOT_PATH . '/dangnhap.php');
        exit;
    } 
    error_log("User authenticated - Role: " . $_SESSION['role_name']);
    error_log("Current page: " . basename($current_path));
}

require_once ROOT_PATH . '/config.php';
require_once ROOT_PATH . '/includes/functions.php';
?> 