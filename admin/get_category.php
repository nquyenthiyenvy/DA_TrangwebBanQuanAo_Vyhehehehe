<?php
require_once 'config.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE category_id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode($category);
} 