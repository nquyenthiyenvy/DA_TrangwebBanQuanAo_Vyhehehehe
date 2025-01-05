<?php
require_once '../init.php';

if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] != 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $sql = "SELECT DISTINCT a.account_id, a.username 
            FROM account a 
            JOIN messages m ON a.account_id = m.sender_id OR a.account_id = m.receiver_id 
            WHERE a.role != 'admin'
            ORDER BY a.username";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
} 