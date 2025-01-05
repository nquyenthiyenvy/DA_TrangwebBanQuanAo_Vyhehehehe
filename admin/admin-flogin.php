<?php
session_start();
require "../config.php";

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM account WHERE username = :username AND role = 'admin'";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: admin-dashboard.php");
            exit();
        } else {
            echo "<script>
                alert('Thông tin đăng nhập không chính xác!');
                window.location.href='admin-login.php';
            </script>";
        }
    } else {
        echo "<script>
            alert('Tài khoản không có quyền admin!');
            if(confirm('Bạn có muốn quay về trang chủ?')) {
                window.location.href='../index.php';
            } else {
                window.location.href='admin-login.php';
            }
        </script>";
    }
}
?> 