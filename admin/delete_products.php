<?php
require_once '../init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $ids = $data['ids'] ?? [];
    
    if (!empty($ids)) {
        try { 
            $idList = implode(',', array_map('intval', $ids));
            $sql = "DELETE FROM products WHERE product_id IN ($idList)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute();
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa sản phẩm']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
} 