<?php
require_once '../init.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'Không có quyền truy cập'
    ]);
    exit;
}

try {
    // Debug - Log received data
    error_log("Received POST data: " . print_r($_POST, true));

    if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
        throw new Exception('Thiếu thông tin cần thiết');
    }

    $order_id = (int)$_POST['order_id'];
    $new_status = trim($_POST['status']);

    // Debug - Log processed data
    error_log("Processing order_id: $order_id, new_status: $new_status");

    // Kiểm tra trạng thái hợp lệ
    $valid_statuses = ['pending', 'processing', 'shipping', 'completed', 'cancelled'];
    if (!in_array($new_status, $valid_statuses)) {
        throw new Exception('Trạng thái không hợp lệ');
    }

    // Debug - Log SQL query
    $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    error_log("Executing SQL: $sql with params: [$new_status, $order_id]");

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$new_status, $order_id]);

    if (!$result) {
        // Debug - Log SQL error
        error_log("SQL Error: " . print_r($stmt->errorInfo(), true));
        throw new Exception('Không thể cập nhật trạng thái đơn hàng: ' . implode(', ', $stmt->errorInfo()));
    }

    // Debug - Log success
    error_log("Update successful. Rows affected: " . $stmt->rowCount());

    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật trạng thái thành công',
        'affected_rows' => $stmt->rowCount()
    ]);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi database: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("General Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 