<?php
session_start();
require_once("./config.php");

try {
    // Kết nối database bằng PDO
    $pdo = new PDO('mysql:host=localhost;dbname=ten_cua_so_du_lieu;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Lấy dữ liệu từ URL
    $vnp_SecureHash = $_GET['vnp_SecureHash'];
    $inputData = array();
    foreach ($_GET as $key => $value) {
        if (substr($key, 0, 4) == "vnp_") {
            $inputData[$key] = $value;
        }
    }

    unset($inputData['vnp_SecureHash']);
    ksort($inputData);
    $i = 0;
    $hashData = "";
    foreach ($inputData as $key => $value) {
        if ($i == 1) {
            $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashData .= urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }
    }

    $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

    // Kiểm tra chữ ký giao dịch
    if ($secureHash == $vnp_SecureHash) {
        if ($_GET['vnp_ResponseCode'] == '00') {
            // Lấy thông tin giao dịch
            $order_id = $_GET['vnp_TxnRef'];
            $total_price = $_GET['vnp_Amount'] / 100; // Chuyển đổi lại số tiền thành đồng
            $order_date = date('Y-m-d H:i:s');
            $order_status = 'Thành công';
            $shipping_address = 'Dịch vụ giao hàng'; // Thay bằng dữ liệu thực tế
            $user_id = $_SESSION['user_id'] ?? 1; // User ID tạm thời (cần session đúng khi login)

            // Chèn dữ liệu đầy đủ vào bảng orders
            $stmt = $pdo->prepare("INSERT INTO orders (order_id, user_id, order_date, order_status, shipping_address, total_price)
                                   VALUES (:order_id, :user_id, :order_date, :order_status, :shipping_address, :total_price)");
            $stmt->bindParam(':order_id', $order_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':order_date', $order_date);
            $stmt->bindParam(':order_status', $order_status);
            $stmt->bindParam(':shipping_address', $shipping_address);
            $stmt->bindParam(':total_price', $total_price);
            $stmt->execute();

            $result_message = "<span style='color:blue'>Giao dịch thành công và đã được lưu vào cơ sở dữ liệu.</span>";
        } else {
            $result_message = "<span style='color:red'>Giao dịch không thành công.</span>";
        }
    } else {
        $result_message = "<span style='color:red'>Chữ ký không hợp lệ.</span>";
    }
} catch (PDOException $e) {
    $result_message = "<span style='color:red'>Lỗi cơ sở dữ liệu: " . $e->getMessage() . "</span>";
} catch (Exception $e) {
    $result_message = "<span style='color:red'>Lỗi: " . $e->getMessage() . "</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>VNPAY RESPONSE</title>
        <link href="/vnpay_php/assets/bootstrap.min.css" rel="stylesheet"/>
        <link href="/vnpay_php/assets/jumbotron-narrow.css" rel="stylesheet">         
        <script src="/vnpay_php/assets/jquery-1.11.3.min.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="header clearfix">
                <h3 class="text-muted">VNPAY RESPONSE</h3>
            </div>
            <div class="table-responsive">
                <div class="form-group">
                    <label>Mã đơn hàng:</label>
                    <label><?php echo $_GET['vnp_TxnRef'] ?? ''; ?></label>
                </div>    
                <div class="form-group">
                    <label>Số tiền:</label>
                    <label><?php echo $_GET['vnp_Amount'] ?? ''; ?></label>
                </div>  
                <div class="form-group">
                    <label>Nội dung thanh toán:</label>
                    <label><?php echo $_GET['vnp_OrderInfo'] ?? ''; ?></label>
                </div> 
                <div class="form-group">
                    <label>Mã phản hồi (vnp_ResponseCode):</label>
                    <label><?php echo $_GET['vnp_ResponseCode'] ?? ''; ?></label>
                </div> 
                <div class="form-group">
                    <label>Mã giao dịch Tại VNPAY:</label>
                    <label><?php echo $_GET['vnp_TransactionNo'] ?? ''; ?></label>
                </div> 
                <div class="form-group">
                    <label>Mã Ngân hàng:</label>
                    <label><?php echo $_GET['vnp_BankCode'] ?? ''; ?></label>
                </div> 
                <div class="form-group">
                    <label>Thời gian thanh toán:</label>
                    <label><?php echo $_GET['vnp_PayDate'] ?? ''; ?></label>
                </div> 
                <div class="form-group">
                    <label>Kết quả:</label>
                    <label><?php echo $result_message; ?></label>
                </div> 
            </div>
            <footer class="footer">
                   <p>&copy; VNPAY <?php echo date('Y')?></p>
            </footer>
        </div>  
    </body>
</html>
