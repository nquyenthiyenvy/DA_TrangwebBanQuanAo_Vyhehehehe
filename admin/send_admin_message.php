<?php
require_once '../init.php';

if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] != 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    $message = $_POST['message'];
    $receiver_id = $_POST['receiver_id'];
    $sender_id = $_SESSION['account_id'];

    try {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message) 
                VALUES (:sender_id, :receiver_id, :message)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':sender_id' => $sender_id,
            ':receiver_id' => $receiver_id,
            ':message' => $message
        ]);

        $response['success'] = true;
        $response['message'] = 'Tin nhắn đã được gửi';
    } catch(PDOException $e) {
        $response['message'] = 'Lỗi: ' . $e->getMessage();
    }

    echo json_encode($response);
} 