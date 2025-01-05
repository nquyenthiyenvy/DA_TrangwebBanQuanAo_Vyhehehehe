<div class="admin-sidebar">
    <div class="admin-logo">
        <h2>H&V Admin</h2>
    </div>
    <ul class="admin-menu">
        <?php if(in_array($_SESSION['role'], ['admin', 'manager-dashboard'])): ?>
            <li><a href="admin-dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <?php endif; ?>

        <?php if(in_array($_SESSION['role'], ['admin', 'manager-product'])): ?>
            <li><a href="admin-products.php"><i class="fa fa-shopping-bag"></i> Sản phẩm</a></li>
        <?php endif; ?>

        <?php if(in_array($_SESSION['role'], ['admin', 'manager-order'])): ?>
            <li><a href="admin-orders.php"><i class="fa fa-shopping-cart"></i> Đơn hàng</a></li>
        <?php endif; ?>

        <?php if(in_array($_SESSION['role'], ['admin', 'manager-user'])): ?>
            <li><a href="admin-users.php"><i class="fa fa-users"></i> Người dùng</a></li>
        <?php endif; ?>

        <li><a href="admin-logout.php"><i class="fa fa-sign-out"></i> Đăng xuất</a></li>
    </ul>
</div> 