<?php
function checkPermission() {
    if (!isset($_SESSION['role_name'])) {
        header('Location: ../dangnhap.php');
        exit;
    }

    if ($_SESSION['role_name'] !== 'admin') {
        header('Location: admin-403.php');
        exit;
    }

    return true;
} 