<?php
session_start();

if (isset($_GET['id']) && isset($_SESSION['cart'][$_GET['id']])) {
    unset($_SESSION['cart'][$_GET['id']]);
}
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?>
