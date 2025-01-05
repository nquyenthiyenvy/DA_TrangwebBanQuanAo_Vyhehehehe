<?php
require_once '../init.php';
require_once 'admin-header.php';

if (!checkPermission('dashboard')) {
    header('Location: admin-403.php');
    exit;
}

if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== 'admin') {
    header('Location: admin-403.php');
    exit;
}

try { 
    $orderStats = $pdo->query("SELECT COALESCE(order_status, 'pending') as status, COUNT(*) as count 
                              FROM orders 
                              GROUP BY order_status")->fetchAll(PDO::FETCH_ASSOC); 
    $monthlyRevenue = $pdo->query("SELECT 
                                    DATE_FORMAT(order_date, '%m/%Y') as month,
                                    COALESCE(SUM(total_amount), 0) as revenue,
                                    COUNT(*) as order_count
                                  FROM orders 
                                  WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                                  GROUP BY DATE_FORMAT(order_date, '%m/%Y')
                                  ORDER BY order_date")->fetchAll(PDO::FETCH_ASSOC); 
    $topOrders = $pdo->query("SELECT 
                                fullname as customer_name,
                                total_amount as revenue,
                                DATE_FORMAT(order_date, '%d/%m/%Y') as order_date
                             FROM orders
                             WHERE order_status != 'cancelled'
                             ORDER BY total_amount DESC
                             LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    if (empty($orderStats)) {
        $orderStats = [
            ['status' => 'pending', 'count' => 0],
            ['status' => 'processing', 'count' => 0],
            ['status' => 'shipping', 'count' => 0],
            ['status' => 'completed', 'count' => 0],
            ['status' => 'cancelled', 'count' => 0]
        ];
    }

    if (empty($monthlyRevenue)) {
        $monthlyRevenue = [
            ['month' => date('m/Y'), 'revenue' => 0, 'order_count' => 0]
        ];
    }

} catch(PDOException $e) {
    error_log("Dashboard query error: " . $e->getMessage());
    echo "<div class='alert alert-danger'>Lỗi truy vấn dữ liệu: " . $e->getMessage() . "</div>";
    $orderStats = [];
    $monthlyRevenue = [];
    $topOrders = [];
} 
function formatMoney($amount) {
    $amount = $amount ?: 0;
    return number_format($amount, 0, ',', '.') . 'đ';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <title>Dashboard - Admin</title>
    <link href="../css/font-awesome.css" rel="stylesheet">
    <link href="../css/bootstrap.css" rel="stylesheet">   
    <link href="../css/admin.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        .admin-sidebar {
            width: 250px;
            background: #dc3545;
            color: #fff;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
        }
        .admin-sidebar .brand {
            text-align: center;
            padding: 20px 15px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .admin-sidebar .brand h2 {
            color: #fff;
            margin: 0;
            font-size: 24px;
        }
        .admin-sidebar .brand p {
            color: rgba(255,255,255,0.7);
            margin: 10px 0 0;
            font-size: 14px;
        }
        .admin-sidebar .user-info {
            text-align: center;
            padding: 20px 15px;
        }
        .admin-sidebar .user-info i {
            font-size: 48px;
            color: #fff;
        }
        .admin-sidebar .nav-item a {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            display: block;
            transition: all 0.3s;
        }
        .admin-sidebar .nav-item a:hover,
        .admin-sidebar .nav-item a.active {
            background: #c82333;
            color: #fff;
            text-decoration: none;
        }
        .admin-sidebar .nav-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .admin-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            background: #f8f9fa;
        }
        .stats-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
            text-align: center;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-card i {
            font-size: 40px;
            margin-bottom: 15px;
            color: #dc3545;
        }
        .stats-card h4 {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .stats-card h2 {
            color: #343a40;
            margin: 0;
            font-weight: bold;
        }
        .chart-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .welcome-banner {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 0 20px rgba(220, 53, 69, 0.3);
        }
        .welcome-banner h2 {
            margin: 0;
            font-size: 24px;
        }
        .welcome-banner p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .top-products {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .top-products table {
            margin: 0;
        }
        .top-products th {
            background: #f8f9fa;
            border-top: none;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper"> 
        <div class="admin-sidebar" id="adminSidebar">
            <button id="sidebarToggle" class="sidebar-toggle">
                <i class="fa fa-times"></i>
            </button>
            <div class="brand">
                <h2>H&V Admin</h2>
                <p>Chào mừng bạn đến với trang quản trị H&V Shop</p>
            </div>
            <div class="user-info">
                <i class="fa fa-user-circle"></i>
                <h5 class="mt-3 mb-0"><?php echo htmlspecialchars($_SESSION['username']); ?></h5>
                <p class="text-muted">Admin</p>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="admin-dashboard.php" class="nav-link active">
                        <i class="fa fa-dashboard"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-categories.php" class="nav-link">
                        <i class="fa fa-users"></i> Danh mục
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-products.php" class="nav-link">
                        <i class="fa fa-shopping-cart"></i> Sản phẩm
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-orders.php" class="nav-link">
                        <i class="fa fa-file-text"></i> Đơn hàng
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-users.php" class="nav-link">
                        <i class="fa fa-users"></i> Người dùng
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../logout.php" class="nav-link">
                        <i class="fa fa-sign-out"></i> Đăng xuất
                    </a>
                </li>
            </ul>
        </div>
        <div class="admin-content">
            <div class="welcome-banner">
                <h2>Dashboard</h2>
                <p>Xem tổng quan về hoạt động của cửa hàng</p>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fa fa-shopping-cart"></i>
                        <h4>Tổng đơn hàng</h4>
                        <h2>
                            <?php 
                            try {
                                echo $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn() ?: 0;
                            } catch(PDOException $e) {
                                echo 0;
                            }
                            ?>
                        </h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fa fa-cubes"></i>
                        <h4>Tổng sản phẩm</h4>
                        <h2><?php echo $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(); ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fa fa-users"></i>
                        <h4>Tổng người dùng</h4>
                        <h2><?php echo $pdo->query("SELECT COUNT(*) FROM account")->fetchColumn(); ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fa fa-money"></i>
                        <h4>Doanh thu</h4>
                        <h2><?php echo formatMoney($pdo->query("SELECT SUM(total_amount) FROM orders WHERE order_status != 'cancelled'")->fetchColumn()); ?></h2>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="chart-container">
                        <h4>Biểu đồ doanh thu</h4>
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="chart-container">
                        <h4>Trạng thái đơn hàng</h4>
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="top-products">
                        <h4>Top 5 đơn hàng giá trị cao nhất</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Khách hàng</th>
                                        <th>Ngày đặt</th>
                                        <th>Giá trị đơn hàng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topOrders as $order): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                        <td><?php echo $order['order_date']; ?></td>
                                        <td><?php echo formatMoney($order['revenue']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Biểu đồ doanh thu
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($monthlyRevenue, 'month')); ?>,
            datasets: [{
                label: 'Doanh thu',
                data: <?php echo json_encode(array_column($monthlyRevenue, 'revenue')); ?>,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true
            }, {
                label: 'Số đơn hàng',
                data: <?php echo json_encode(array_column($monthlyRevenue, 'order_count')); ?>,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Biểu đồ trạng thái đơn hàng
    new Chart(document.getElementById('orderStatusChart'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_column($orderStats, 'status')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($orderStats, 'count')); ?>,
                backgroundColor: [
                    '#ffc107',  
                    '#17a2b8',  
                    '#007bff',  
                    '#28a745',  
                    '#dc3545'   
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });

    document.getElementById('sidebarToggle').addEventListener('click', function() {
        const sidebar = document.getElementById('adminSidebar');
        const mainContent = document.querySelector('.admin-main');
        const icon = this.querySelector('i');
        
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        
        if(sidebar.classList.contains('collapsed')) {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        } else {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        }
        
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });

    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('adminSidebar');
        const mainContent = document.querySelector('.admin-main');
        const icon = document.querySelector('#sidebarToggle i');
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        
        if(isCollapsed) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });
    </script>
</body>
</html> 