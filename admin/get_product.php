<?php
require_once '../init.php';

if(isset($_GET['id'])) {
    $product_id = $_GET['id'];
    
    try {
        $sql = "SELECT * FROM products WHERE product_id = :product_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($product) {
            echo json_encode($product);
        } else {
            echo json_encode(['error' => 'Không tìm thấy sản phẩm']);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Thiếu ID sản phẩm']);
} 