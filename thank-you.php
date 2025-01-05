<?php
session_start();
require_once 'init.php'; 
if (!isset($_SESSION['last_order_id'])) {
    header('Location: index.php');
    exit;
}

try { 
    $stmt = $pdo->prepare("SELECT * FROM oders WHERE order_id = ?");
    $stmt->execute([$_SESSION['last_order_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception("Không tìm thấy đơn hàng");
    }
    
} catch(Exception $e) {
    error_log($e->getMessage());
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Đặt hàng thành công - H&V Shop</title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section id="aa-error">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="aa-error-area">
                        <h2>Đặt hàng thành công!</h2>
                        <div class="order-info">
                            <p>Cảm ơn <strong><?php echo htmlspecialchars($order['fullname']); ?></strong> đã đặt hàng.</p>
                            <p>Mã đơn hàng của bạn: <strong>#<?php echo $order['order_id']; ?></strong></p>
                            <p>Tổng giá trị đơn hàng: <strong><?php echo number_format($order['total'], 0, ',', '.'); ?>đ</strong></p>
                            <p>Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất.</p>
                        </div>
                        <a href="index.php" class="aa-primary-btn">Về trang chủ</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 