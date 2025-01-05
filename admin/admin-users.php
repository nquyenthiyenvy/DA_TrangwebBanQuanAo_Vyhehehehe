<?php
session_start();
include('config.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_name'])) {
    header("Location: ../dangnhap.php");
    exit();
}

// Kiểm tra quyền truy cập
if ($_SESSION['role_name'] !== 'admin' && $_SESSION['role_name'] !== 'manager-user') {
    header("Location: admin-403.php");
    exit();
}

// Phân quyền
$canManageUsers = ($_SESSION['role_name'] === 'admin' || $_SESSION['role_name'] === 'manager-user');

if (isset($_POST['addUser'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); 
    $role = $_POST['role'];
    $status = $_POST['status'];
    $sql = "INSERT INTO account (username, email, password, role, status) 
            VALUES (:username, :email, :password, :role, :status)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':password' => $password,
        ':role' => $role,
        ':status' => $status
    ]);
    header("Location: admin-users.php");
    exit();
}
if (isset($_POST['deleteUserId'])) {
    $userId = $_POST['deleteUserId'];
    
    try {
        // Kiểm tra xem có phải user đang đăng nhập không
        if ($userId == $_SESSION['user_id']) {
            throw new Exception("Không thể xóa tài khoản đang đăng nhập!");
        }

        // Kiểm tra role của user cần xóa
        $stmt = $pdo->prepare("SELECT role FROM account WHERE account_id = ?");
        $stmt->execute([$userId]);
        $userRole = $stmt->fetchColumn();

        // Không cho phép xóa admin
        if ($userRole === 'admin') {
            throw new Exception("Không thể xóa tài khoản admin!");
        }

        // Nếu qua được các điều kiện trên thì thực hiện xóa
        $sql = "DELETE FROM account WHERE account_id = :account_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':account_id' => $userId]);

        $_SESSION['success_message'] = "Xóa người dùng thành công!";
        header("Location: admin-users.php");
        exit();

    } catch(Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: admin-users.php");
        exit();
    }
} 
$sql = "SELECT a.account_id, a.username, a.email, a.role, a.status 
        FROM account a"; 
$result = $pdo->query($sql);  
if(isset($_POST['add_user'])) {
    try { 
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $role_name = trim($_POST['role_name']);
        $status = trim($_POST['status']); 
        if(empty($username) || empty($email) || empty($password) || empty($role_name)) {
            throw new Exception("Vui lòng điền đầy đủ thông tin bắt buộc!");
        } 
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email không hợp lệ!");
        } 
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->fetchColumn() > 0) {
            throw new Exception("Email đã được sử dụng!");
        } 
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); 
        $sql = "INSERT INTO users (username, email, password, role_name, status) 
                VALUES (:username, :email, :password, :role_name, :status)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashed_password,
            ':role_name' => $role_name,
            ':status' => $status
        ]);

        if($result) {
            echo "<script>
                alert('Thêm người dùng thành công!');
                window.location.href='admin-users.php';
            </script>";
            exit();
        } else {
            throw new Exception("Có lỗi xảy ra khi thêm người dùng!");
        }

    } catch(Exception $e) {
        echo "<script>alert('Lỗi: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <title>Quản lý người dùng - Admin</title>
    <link href="../css/font-awesome.css" rel="stylesheet">
    <link href="../css/bootstrap.css" rel="stylesheet"> 
    <link href="../css/admin.css" rel="stylesheet">    
</head>
<body class="admin-page">
    <div class="admin-sidebar" id="adminSidebar">
        <button id="sidebarToggle" class="sidebar-toggle">
            <i class="fa fa-times"></i>
        </button>
        <div class="admin-logo">
            <h2>H&V Admin</h2>
        </div>
        <ul class="admin-menu">
            <li class="active"><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="admin-products.php"><i class="fa fa-shopping-bag"></i> Sản phẩm</a></li>
            <li><a href="admin-categories.php"><i class="fa fa-list"></i> Danh mục</a></li>
            <li><a href="admin-orders.php"><i class="fa fa-shopping-cart"></i> Đơn hàng</a></li>
            <li><a href="admin-users.php"><i class="fa fa-users"></i> Người dùng</a></li>
            <li><a href="admin-logout.php"><i class="fa fa-sign-out"></i> Đăng xuất</a></li>
        </ul>
    </div> 
    <div class="admin-main">
        <div class="admin-header">
            <h1>Quản lý người dùng</h1>
            <?php if ($canManageUsers): ?>
            <button class="btn btn-add-user" data-toggle="modal" data-target="#addUserModal">
                <i class="fa fa-plus"></i> Thêm người dùng
            </button>
            <?php endif; ?>
        </div> 
        <div class="admin-filters">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-control" id="roleFilter">
                        <option value="">Tất cả vai trò</option>
                        <option value="admin">Admin</option>
                        <option value="user">Người dùng</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control" id="statusFilter">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active">Đang hoạt động</option>
                        <option value="blocked">Đã khóa</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="admin-messages">
            <?php if(isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?php 
                        echo $_SESSION['error_message'];
                        unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="admin-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                     if ($result->rowCount() > 0) {
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) { 
                            echo "<tr>";
                            echo "<td>" . $row['account_id'] . "</td>";
                            echo "<td>" . $row['username'] . "</td>";
                            echo "<td>" . $row['email'] . "</td>";
                            echo "<td><span class='role-badge'>" . $row['role'] . "</span></td>";
                            echo "<td><span class='user-status " . $row['status'] . "'>" . ucfirst($row['status']) . "</span></td>";
                            echo "<td>";
                            echo "<button class='btn btn-view' data-toggle='modal' data-target='#viewUserModal'>
                                    <i class='fa fa-eye'></i>
                                  </button>";
                            if ($canManageUsers) {
                                echo "<button class='btn btn-edit' data-toggle='modal' data-target='#editUserModal'>
                                        <i class='fa fa-edit'></i>
                                      </button>";
                            }
                            if ($row['role'] !== 'admin' && $row['account_id'] != $_SESSION['user_id']) {
                                echo "<form action='admin-users.php' method='POST' style='display:inline-block;'>
                                        <input type='hidden' name='deleteUserId' value='" . $row['account_id'] . "'>
                                        <button type='submit' class='btn btn-danger' onclick='return confirm(\"Bạn có chắc chắn muốn xóa người dùng này?\")'>
                                            <i class='fa fa-trash'></i>
                                        </button>
                                      </form>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>Không có người dùng nào.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="admin-pagination">
            <ul class="pagination">
                <li><a href="#">&laquo;</a></li>
                <li class="active"><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">&raquo;</a></li>
            </ul>
        </div>
    </div>
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Thêm người dùng</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="admin-users.php" method="POST">
                        <div class="form-group">
                            <label for="username">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Vai trò</label>
                            <select class="form-control" id="role" name="role">
                                <option value="admin">Admin</option>
                                <option value="user">Người dùng</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Trạng thái</label>
                            <select class="form-control" id="status" name="status">
                                <option value="active">Đang hoạt động</option>
                                <option value="blocked">Đã khóa</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" name="addUser">Thêm người dùng</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        const sidebar = document.getElementById('adminSidebar');
        const mainContent = document.querySelector('.admin-main');
        const icon = this.querySelector('i');
        
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        
        if(sidebar.classList.contains('collapsed')) {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        } else {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        }
        
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });

    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('adminSidebar');
        const mainContent = document.querySelector('.admin-main');
        const icon = document.querySelector('#sidebarToggle i');
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        
        if(isCollapsed) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });
    </script>
</body>
</html>
