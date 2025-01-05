<?php
session_start();

if (!isset($_SESSION['username'])) {
    $_SESSION['redirect_url'] = 'order.php';
    header('Location: dangnhap.php');
    exit();
}

if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    if (empty($_POST['fullname'])) $errors[] = "Vui lòng nhập họ tên";
    if (empty($_POST['phone'])) $errors[] = "Vui lòng nhập số điện thoại";
    if (empty($_POST['city'])) $errors[] = "Vui lòng nhập tỉnh/thành phố";
    if (empty($_POST['district'])) $errors[] = "Vui lòng nhập quận/huyện";
    if (empty($_POST['ward'])) $errors[] = "Vui lòng nhập phường/xã";
    if (empty($_POST['address'])) $errors[] = "Vui lòng nhập địa chỉ chi tiết";
    
    if (empty($errors)) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=db_web", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            $pdo->beginTransaction(); 
            $sql = "INSERT INTO oders (
                account_id, fullname, phone, email, 
                address, city, district, ward,
                total, payment_method, status
            ) VALUES (
                :account_id, :fullname, :phone, :email,
                :address, :city, :district, :ward,
                :total, :payment_method, 'pending'
            )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':account_id' => $_SESSION['account_id'],
                ':fullname' => $_POST['fullname'],
                ':phone' => $_POST['phone'],
                ':email' => $_POST['email'],
                ':address' => $_POST['address'],
                ':city' => $_POST['city'],
                ':district' => $_POST['district'],
                ':ward' => $_POST['ward'],
                ':total' => $total,
                ':payment_method' => $_POST['payment_method']
            ]);
            
            $order_id = $pdo->lastInsertId(); 



            $stmt = $pdo->prepare("INSERT INTO orderdetails (order_id, product_id, quantity, price) 
                                VALUES (:order_id, :product_id, :quantity, :price)");
                                
            foreach ($_SESSION['cart'] as $item) {
                $stmt->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $item['id'],
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price']
                ]);
            }
            
            $_SESSION['last_order_id'] = $order_id;

            // Xóa giỏ hàng
            unset($_SESSION['cart']); 
            $pdo->commit();
            
            //
            header('Location: thank-you.php');
            exit();
            
        } catch(PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Order error: " . $e->getMessage());
            $errors[] = "Có lỗi xảy ra khi đặt hàng: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <title>H&V Shop | Đặt hàng</title> 
    <link href="css/font-awesome.css" rel="stylesheet"> 
    <link href="css/bootstrap.css" rel="stylesheet">
    <link id="switcher" href="css/theme-color/default-theme.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">    
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section id="checkout">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="checkout-area">
                        <form id="checkout-form" method="POST">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="checkout-left">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">Thông tin đặt hàng</h3>
                                            </div>
                                            <div class="panel-body">
                                                <?php if (!empty($errors)): ?>
                                                    <div class="alert alert-danger">
                                                        <ul>
                                                            <?php foreach ($errors as $error): ?>
                                                                <li><?php echo $error; ?></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="form-group">
                                                    <label>Họ và tên <span class="required">*</span></label>
                                                    <input type="text" name="fullname" class="form-control" required>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="email" name="email" class="form-control">
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>Số điện thoại <span class="required">*</span></label>
                                                    <input type="tel" name="phone" class="form-control" required>
                                                </div>

                                                <div class="form-group">
                                                    <label>Tỉnh/Thành phố <span class="required">*</span></label>
                                                    <input type="text" name="city" class="form-control" required>
                                                </div>

                                                <div class="form-group">
                                                    <label>Quận/Huyện <span class="required">*</span></label>
                                                    <input type="text" name="district" class="form-control" required>
                                                </div>

                                                <div class="form-group">
                                                    <label>Phường/Xã <span class="required">*</span></label>
                                                    <input type="text" name="ward" class="form-control" required>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>Địa chỉ chi tiết <span class="required">*</span></label>
                                                    <input type="text" name="address" class="form-control" required 
                                                           placeholder="Số nhà, tên đường...">
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>Ghi chú</label>
                                                    <textarea name="note" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="checkout-right">
                                        <h4>Đơn hàng của bạn</h4>
                                        <div class="aa-order-summary-area">
                                            <table class="table table-responsive">
                                                <thead>
                                                    <tr>
                                                        <th>Sản phẩm</th>
                                                        <th>Tổng</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($_SESSION['cart'] as $item): ?>
                                                        <tr>
                                                            <td><?php echo $item['name']; ?> <strong> × <?php echo $item['quantity']; ?></strong></td>
                                                            <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ</td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Tổng cộng</th>
                                                        <td><?php echo number_format($total, 0, ',', '.'); ?>đ</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div class="aa-payment-method">
                                            <label>
                                                <input type="radio" name="payment_method" value="cod" checked> Thanh toán khi nhận hàng
                                            </label>
                                            <label>
                                                <input type="radio" name="payment_method" value="vnpay"> Thanh toán VNPay
                                            </label>
                                            <input type="submit" value="Đặt hàng" class="aa-browse-btn">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <?php include 'includes/footer.php'; ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>  
</body>
</html> 