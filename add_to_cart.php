<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=db_web", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = array(
                    'id' => $product_id,
                    'name' => $product['product_name'],
                    'price' => $product['price'],
                    'image' => $product['image_url'],
                    'quantity' => $quantity
                );
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Thêm vào giỏ hàng thành công',
                'cart_count' => count($_SESSION['cart'])
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm'
            ]);
        }
        
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu thông tin sản phẩm'
    ]);
}
?>
