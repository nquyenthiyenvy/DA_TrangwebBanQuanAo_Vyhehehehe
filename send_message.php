<?php
session_start();
require_once 'init.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    if (!isset($_SESSION['account_id'])) {
        $response['message'] = 'Vui lòng đăng nhập để gửi tin nhắn';
        echo json_encode($response);
        exit;
    }

    $message = $_POST['message'];
    $sender_id = $_SESSION['account_id'];
    $receiver_id = 1; 

    try {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (:sender_id, :receiver_id, :message)";
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