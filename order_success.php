<?php
error_reporting(0);  
error_reporting(E_ALL & ~E_WARNING); 

session_start();

if (!isset($_SESSION['last_order'])) {
    header('Location: index.php');
    exit();
}

$order = $_SESSION['last_order'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công</title>
    <link rel="stylesheet" href="css/style.css"> 
</head>
<body>
    <div class="success-container">
        <div class="success-message">
            <i class="fa fa-check-circle"></i>
            <h1>Đặt Hàng Thành Công!</h1>
            <p>Cảm ơn bạn đã đặt hàng. Mã đơn hàng của bạn là: #<?php echo $order['order_id']; ?></p>
        </div>

        <div class="order-details">
            <h2>Thông tin đơn hàng</h2>
            <p><strong>Họ và tên:</strong> <?php echo htmlspecialchars($order['fullname']); ?></p>
            <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
            <p><strong>Địa chỉ giao hàng:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
            <p><strong>Thành phố:</strong> <?php echo htmlspecialchars($order['city']); ?></p>
            <p><strong>Quận/Huyện:</strong> <?php echo htmlspecialchars($order['district']); ?></p>
            <p><strong>Phường/Xã:</strong> <?php echo htmlspecialchars($order['ward']); ?></p>
            <p><strong>Ghi chú:</strong> <?php echo htmlspecialchars($order['note']); ?></p>
            <p><strong>Tổng tiền:</strong> <?php echo number_format($order['total'], 0, ',', '.'); ?>đ</p>
            <p><strong>Hình thức thanh toán:</strong> <?php echo $order['payment_method'] === 'cod' ? 'Thanh toán khi nhận hàng' : 'Thanh toán qua VNPAY'; ?></p>
        </div>

        <div class="success-actions">
            <a href="index.php" class="btn btn-primary">Tiếp tục mua sắm</a>
            <a href="order-history.php" class="btn btn-secondary">Xem lịch sử đơn hàng</a>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

<?php 
unset($_SESSION['last_order']);
?> 