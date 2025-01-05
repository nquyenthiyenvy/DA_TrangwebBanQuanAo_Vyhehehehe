<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config.php';

try {
    if (!isset($_GET['code'])) {
        die('Không nhận được code từ Google');
    } 
    echo "Received code from Google: " . $_GET['code']; 
    $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if(!isset($token['error'])) { 
        $google_client->setAccessToken($token['access_token']);
        $google_service = new Google_Service_Oauth2($google_client);
        $data = $google_service->userinfo->get(); 
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch();
        
        if(!$user) { 
            $stmt = $pdo->prepare("INSERT INTO users (username, email, oauth_provider) VALUES (?, ?, 'google')");
            $stmt->execute([$data['name'], $data['email']]);
            $user_id = $pdo->lastInsertId();
        } else {
            $user_id = $user['user_id'];
        }
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $data['name'];
        
        header('Location: index.php');
        exit;
    }
} catch(Exception $e) {
    echo 'Lỗi: ' . $e->getMessage();
    echo '<pre>';
    print_r($e->getTrace());
    echo '</pre>';
}
?> 