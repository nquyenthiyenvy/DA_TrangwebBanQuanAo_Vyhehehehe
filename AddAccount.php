<?php
session_start();
require "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $email = $_POST['email'];

    if ($password !== $password_confirm) {
        $_SESSION['register_error'] = "Mật khẩu và xác nhận mật khẩu không khớp!";
        $_SESSION['post_data'] = $_POST; 
        header("Location: account.php");
        exit();
    }
    $stmt = $pdo->prepare('SELECT * FROM account WHERE Email = :Email');
    $stmt->execute([':Email' => $email]);

    if ($stmt->rowCount() > 0) { 
        $_SESSION['register_error'] = "Email đã được sử dụng!";
        $_SESSION['post_data'] = $_POST; 
        header("Location: account.php");
        exit();
    }
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); 
    $sql = $pdo->prepare('INSERT INTO account (Username, Password, Email) VALUES (:Username, :Password, :Email)');
    $sql->execute([':Username' => $username, ':Password' => $hashed_password, ':Email' => $email]); 
    $_SESSION['register_success'] = "Đăng ký thành công!";
    header("Location: dangnhap.php");
    exit();
}
?>
