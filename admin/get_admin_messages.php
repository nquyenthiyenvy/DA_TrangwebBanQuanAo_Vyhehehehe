<?php
require_once '../init.php';

if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] != 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_GET['user_id'];
$last_id = isset($_GET['last_id']) ? $_GET['last_id'] : 0;

try {
    $sql = "SELECT m.*, a.username as sender_name 
            FROM messages m 
            JOIN account a ON m.sender_id = a.account_id
            WHERE (m.sender_id = :user_id AND m.receiver_id = :admin_id)
            OR (m.sender_id = :admin_id AND m.receiver_id = :user_id)
            AND m.message_id > :last_id
            ORDER BY m.timestamp ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':admin_id' => $_SESSION['account_id'],
        ':last_id' => $last_id
    ]);

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($messages);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
} 