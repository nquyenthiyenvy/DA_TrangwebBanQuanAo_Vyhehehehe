<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Vui lòng đăng nhập để đánh giá']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id'];

    try {
        $check = $pdo->prepare("SELECT rating_id FROM ratings WHERE user_id = ? AND product_id = ?");
        $check->execute([$user_id, $product_id]);
        
        if ($check->rowCount() > 0) {
            $sql = "UPDATE ratings SET rating = ?, comment = ? WHERE user_id = ? AND product_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$rating, $comment, $user_id, $product_id]);
        } else {
            $sql = "INSERT INTO ratings (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$product_id, $user_id, $rating, $comment]);
        }
        $avg = $pdo->prepare("SELECT AVG(rating) as avg_rating FROM ratings WHERE product_id = ?");
        $avg->execute([$product_id]);
        $avg_rating = $avg->fetch()['avg_rating'];

        echo json_encode(['success' => true, 'avg_rating' => round($avg_rating, 1)]);
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Lỗi: ' . $e->getMessage()]);
    }
} 