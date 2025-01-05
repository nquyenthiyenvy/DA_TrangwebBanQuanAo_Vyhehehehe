<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require "config.php"; 
session_unset();
session_destroy();
session_start();

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    error_log("Login attempt - Email: " . $email);

    $query = "SELECT * FROM account WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("User found - Username: " . $user['username'] . ", Role: " . $user['role']);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['account_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_name'] = $user['role'];
            error_log("Login successful - Session data:");
            error_log("user_id: " . $_SESSION['user_id']);
            error_log("email: " . $_SESSION['email']);
            error_log("username: " . $_SESSION['username']);
            error_log("role_name: " . $_SESSION['role_name']);

            switch($user['role']) {
                case 'admin':
                    header("Location: admin/admin-dashboard.php");
                    break;
                case 'manager-product':
                    header("Location: admin/admin-products.php");
                    break;
                case 'manager-category':
                    header("Location: admin/admin-categories.php");
                    break;
                case 'manager-order':
                    header("Location: admin/admin-orders.php");
                    break;
                case 'manager-user':
                    header("Location: admin/admin-users.php");
                    break;
                case 'user':
                default:
                    header("Location: index.php");
                    break;
            }
            exit();
        } else {
            error_log("Password verification failed");
        }
    } else {
        error_log("No user found with email: " . $email);
    }
    echo "<script>alert('Thông tin đăng nhập không chính xác!'); window.location.href='dangnhap.php';</script>";
}
?>
