<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $provider_id = $_POST['provider_id'] ?? '';

        if (empty($email)) {
            throw new Exception('Email is required');
        }
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) { 
            $stmt = $pdo->prepare("INSERT INTO users (username, email, oauth_provider, oauth_id) 
                                 VALUES (?, ?, 'google', ?)");
            $stmt->execute([$name, $email, $provider_id]);
            $user_id = $pdo->lastInsertId();
        } else {
            $user_id = $user['user_id'];
        } 
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $name;

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?> 