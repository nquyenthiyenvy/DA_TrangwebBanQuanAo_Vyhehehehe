<?php
function checkPermission($permission) {
    if (!isset($_SESSION['user_id'])) {
        error_log("No user_id in session");
        header('Location: ' . ROOT_PATH . '/dangnhap.php');
        exit;
    }

    // Debug chi tiết
    error_log("=== Permission Check ===");
    error_log("Checking permission: " . $permission);
    error_log("User ID: " . $_SESSION['user_id']);
    error_log("Username: " . $_SESSION['username']);
    error_log("Role name: " . $_SESSION['role_name']);

    // Admin có tất cả quyền
    if ($_SESSION['role_name'] === 'admin') {
        error_log("User is admin - Access granted");
        return true;
    }

    // Kiểm tra quyền theo role
    $hasPermission = false;
    switch($permission) {
        case 'products':
            $hasPermission = in_array($_SESSION['role_name'], ['manager-product']);
            break;
        case 'categories':
            $hasPermission = in_array($_SESSION['role_name'], ['manager-category', 'manager-product']);
            break;
        case 'orders':
            $hasPermission = in_array($_SESSION['role_name'], ['manager-order']);
            break;
        case 'users':
            $hasPermission = in_array($_SESSION['role_name'], ['manager-user']);
            break;
    }

    error_log("Permission check result: " . ($hasPermission ? 'granted' : 'denied'));
    return $hasPermission;
}

function getManagerRedirectPage($role) {
    switch($role) {
        case 'manager-product':
            return 'admin-products.php';
        case 'manager-category':
            return 'admin-categories.php';
        case 'manager-order':
            return 'admin-orders.php';
        case 'manager-user':
            return 'admin-users.php';
        default:
            return 'admin-403.php';
    }
}
?> 