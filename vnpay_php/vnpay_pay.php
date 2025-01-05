<?php
session_start();
require_once("./config.php");

// Lấy tổng tiền từ session shipping
$total_amount = 0;
if (isset($_SESSION['shipping']) && isset($_SESSION['shipping']['total_amount'])) {
    $total_amount = $_SESSION['shipping']['total_amount'];
} else {
    // Nếu không có thông tin thanh toán, chuyển về trang checkout
    header('Location: ../checkout.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tạo mới đơn hàng</title>
        <!-- Bootstrap core CSS -->
        <link href="assets/bootstrap.min.css" rel="stylesheet"/>
        <!-- Custom styles for this template -->
        <link href="assets/jumbotron-narrow.css" rel="stylesheet">  
        <script src="assets/jquery-1.11.3.min.js"></script>
    </head>

    <body>
        <div class="container">
            <h3>Tạo mới đơn hàng</h3>
            <div class="table-responsive">
                <form action="vnpay_create_payment.php" id="frmCreateOrder" method="post">        
                    <div class="form-group">
                        <label for="amount">Số tiền thanh toán</label>
                        <input class="form-control" 
                               id="amount" 
                               name="amount" 
                               type="number" 
                               value="<?php echo $total_amount; ?>" 
                               readonly />
                    </div>
                    <h4>Chọn phương thức thanh toán</h4>
                    <div class="form-group">
                        <h5>Cách 1: Chuyển hướng sang Cổng VNPAY chọn phương thức thanh toán</h5>
                        <input type="radio" checked="true" id="bankCode" name="bankCode" value="">
                        <label for="bankCode">Cổng thanh toán VNPAYQR</label><br>
                        
                        <h5>Cách 2: Tách phương thức tại site của đơn vị kết nối</h5>
                        <input type="radio" id="bankCode" name="bankCode" value="VNPAYQR">
                        <label for="bankCode">Thanh toán bằng ứng dụng hỗ trợ VNPAYQR</label><br>
                        
                        <input type="radio" id="bankCode" name="bankCode" value="VNBANK">
                        <label for="bankCode">Thanh toán qua thẻ ATM/Tài khoản nội địa</label><br>
                        
                        <input type="radio" id="bankCode" name="bankCode" value="INTCARD">
                        <label for="bankCode">Thanh toán qua thẻ quốc tế</label><br>
                    </div>
                    <div class="form-group">
                        <h5>Chọn ngôn ngữ giao diện thanh toán:</h5>
                        <input type="radio" id="language" checked="true" name="language" value="vn">
                        <label for="language">Tiếng việt</label><br>
                        <input type="radio" id="language" name="language" value="en">
                        <label for="language">Tiếng anh</label><br>
                    </div>
                    <button type="submit" class="btn btn-default">Thanh toán</button>
                </form>
            </div>
            <p>&nbsp;</p>
            <footer class="footer">
                <p>&copy; VNPAY <?php echo date('Y'); ?></p>
            </footer>
        </div>  
    </body>
</html>
