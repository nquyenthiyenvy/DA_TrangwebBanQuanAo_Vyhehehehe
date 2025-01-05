<?php
// Kết nối database với mysqli
$mysqli = new mysqli('localhost', 'root', '', 'shop_db');

// Kiểm tra kết nối
if ($mysqli->connect_error) {
    die('Lỗi kết nối: ' . $mysqli->connect_error);
}

// Đặt charset là utf8 để hỗ trợ tiếng Việt
$mysqli->set_charset("utf8");

// Trả về kết nối
return $mysqli;
?> 
