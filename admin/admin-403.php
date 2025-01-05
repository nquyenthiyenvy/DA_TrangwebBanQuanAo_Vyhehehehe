<?php
require_once '../init.php'; 
if (!isset($_SESSION['role_name'])) {
    header('Location: ' . ROOT_PATH . '/dangnhap.php');
    exit;
}
$redirectPage = getManagerRedirectPage($_SESSION['role_name']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Không có quyền truy cập</title>
    <link href="../css/admin.css" rel="stylesheet">
    <link href="../css/font-awesome.css" rel="stylesheet">
</head>
<body>
    <div class="error-page">
        <h1>403</h1>
        <h2>Không có quyền truy cập</h2>
        <p>Bạn không có quyền truy cập trang này.</p>
        <a href="<?php echo $redirectPage; ?>" class="btn btn-primary">
            <i class="fa fa-arrow-left"></i> Quay lại trang của bạn
        </a>
    </div>
</body>
</html> 