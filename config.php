<?php
$host="localhost";
$dbname="db_web";
$username="root";
$password="";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Lỗi kết nối!".$e->getMessage();
}
?>
