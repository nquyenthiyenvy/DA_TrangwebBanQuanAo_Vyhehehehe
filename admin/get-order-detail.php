<?php
require_once '../init.php';

if(isset($_GET['id'])) {
    $orderId = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("
            SELECT o.*, od.*, p.product_name, p.image_url
            FROM orders o
            JOIN order_details od ON o.order_id = od.order_id
            JOIN products p ON od.product_id = p.product_id
            WHERE o.order_id = ?
        ");
        
        $stmt->execute([$orderId]);
        $orderDetails = $stmt->fetchAll();
        
        if($orderDetails) {
            $order = $orderDetails[0]; // Thông tin chung của đơn hàng
            ?>
            <div class="order-info">
                <h5>Thông tin đơn hàng</h5>
                <p>Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></p>
                <p>Trạng thái: <?php echo getStatusLabel($order['status']); ?></p>
                <p>Phương thức thanh toán: <?php echo getPaymentMethodLabel($order['payment_method']); ?></p>
            </div>
            
            <div class="customer-info">
                <h5>Thông tin khách hàng</h5>
                <p>Họ tên: <?php echo htmlspecialchars($order['fullname']); ?></p>
                <p>Email: <?php echo htmlspecialchars($order['email']); ?></p>
                <p>SĐT: <?php echo htmlspecialchars($order['phone']); ?></p>
                <p>Địa chỉ: <?php echo htmlspecialchars($order['shipping_address']); ?></p>
            </div>
            
            <div class="products-info">
                <h5>Sản phẩm</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orderDetails as $item): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                     style="width: 50px; height: 50px; object-fit: cover;">
                                <?php echo htmlspecialchars($item['product_name']); ?>
                            </td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                            <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Tổng cộng:</strong></td>
                            <td><strong><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php
        }
    } catch(PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
    }
}
?> 