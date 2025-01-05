<?php
require_once 'config.php';

if(isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $sql = "SELECT o.*
            FROM orders o
            WHERE o.order_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    $sql = "SELECT od.*, p.product_name
            FROM order_details od
            LEFT JOIN products p ON od.product_id = p.product_id
            WHERE od.order_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$order_id]);
    $order_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response = [
        'order' => $order,
        'details' => $order_details
    ];
    
    echo json_encode($response);
} 