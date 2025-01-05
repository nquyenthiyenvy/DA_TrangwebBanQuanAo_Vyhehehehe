<?php
require_once '../init.php'; 
$current_page = basename($_SERVER['PHP_SELF']); 
if ($_SESSION['role_name'] !== 'admin' && $current_page !== 'admin-403.php') {
    $allowedPage = getManagerRedirectPage($_SESSION['role_name']); 
    if ($current_page !== basename($allowedPage)) {
        header('Location: admin-403.php');
        exit;
    }
}
?>

<div class="admin-sidebar">
    <div class="admin-logo">
        <h2>H&V Admin</h2>
        <div class="admin-user-info">
            <i class="fa fa-user"></i>
            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <small><?php echo ucfirst($_SESSION['role_name']); ?></small>
        </div>
    </div>
    <ul class="admin-menu">
        <?php if ($_SESSION['role_name'] === 'admin'): ?>
            <li><a href="admin-dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="admin-products.php"><i class="fa fa-shopping-bag"></i> Sản phẩm</a></li>
            <li><a href="admin-categories.php"><i class="fa fa-list"></i> Danh mục</a></li>
            <li><a href="admin-orders.php"><i class="fa fa-shopping-cart"></i> Đơn hàng</a></li>
            <li><a href="admin-users.php"><i class="fa fa-users"></i> Người dùng</a></li>
        <?php else: ?>
            <?php switch($_SESSION['role_name']): 
                  case 'manager-product': ?>
                    <li><a href="admin-products.php"><i class="fa fa-shopping-bag"></i> Sản phẩm</a></li>
                    <?php break;
                  case 'manager-category': ?>
                    <li><a href="admin-categories.php"><i class="fa fa-list"></i> Danh mục</a></li>
                    <?php break;
                  case 'manager-order': ?>
                    <li><a href="admin-orders.php"><i class="fa fa-shopping-cart"></i> Đơn hàng</a></li>
                    <?php break;
                  case 'manager-user': ?>
                    <li><a href="admin-users.php"><i class="fa fa-users"></i> Người dùng</a></li>
                    <?php break;
            endswitch; ?>
        <?php endif; ?>
        
        <li><a href="admin-logout.php"><i class="fa fa-sign-out"></i> Đăng xuất</a></li>
    </ul>
</div>
</rewritten_file> 