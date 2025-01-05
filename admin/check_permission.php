<?php
function checkPermission($allowed_roles) {
    if (!isset($_SESSION['role'])) {
        header("Location: admin-dashboard.php");
        exit();
    }

    $role = $_SESSION['role']; 
    if ($role === 'admin') {
        return true;
    }
    if (!in_array($role, $allowed_roles)) {
        switch($role) {
            case 'manager-product':
                header("Location: admin-products.php");
                break;
            case 'manager-dashboard':
                header("Location: admin-dashboard.php");
                break;
            case 'manager-order':
                header("Location: admin-orders.php");
                break;
            case 'manager-user':
                header("Location: admin-users.php");
                break;
            default:
                header("Location: ../dangnhap.php");
        }
        exit();
    }

    return true;
}
?> 