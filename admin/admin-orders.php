<?php
require_once '../init.php'; 
if (!checkPermission('orders')) {
    header('Location: admin-403.php');
    exit;
}

function getStatusButtonClass($status) {
    switch($status) {
        case 'pending':
            return 'warning';
        case 'processing':
            return 'info';
        case 'shipping':
            return 'primary';
        case 'completed':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}

function getStatusText($status) {
    switch($status) {
        case 'pending':
            return 'Chờ xử lý';
        case 'processing':
            return 'Đang xử lý';
        case 'shipping':
            return 'Đang giao';
        case 'completed':
            return 'Hoàn thành';
        case 'cancelled':
            return 'Đã hủy';
        default:
            return 'Không xác định';
    }
}

try {
    $query = "SELECT o.order_id, o.order_date, o.total_amount, 
                     o.order_status, o.fullname, o.email, o.phone,
                     CONCAT(o.address, ', ', o.ward, ', ', o.district, ', ', o.city) as shipping_address,
                     o.payment_method, o.note
              FROM orders o
              WHERE 1=1";
    $params = array();

    if (isset($_GET['status']) && !empty($_GET['status'])) {
        $query .= " AND o.order_status = :status";
        $params[':status'] = $_GET['status'];
    }

    if (isset($_GET['date']) && !empty($_GET['date'])) {
        $query .= " AND DATE(o.order_date) = :date";
        $params[':date'] = $_GET['date'];
    }

    $query .= " ORDER BY o.order_date DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();

} catch(PDOException $e) {
    error_log("Orders query error: " . $e->getMessage());
    echo "Lỗi truy vấn dữ liệu";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <title>Quản lý đơn hàng - Admin</title>
    <link href="../css/font-awesome.css" rel="stylesheet">
    <link href="../css/bootstrap.css" rel="stylesheet">   
    <link href="../css/admin.css" rel="stylesheet">
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
        .page-header {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 0 20px rgba(220, 53, 69, 0.3);
        }
        .filter-section {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .orders-table {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: normal;
        }
        .btn-update {
            padding: 5px 10px;
            font-size: 14px;
        }
        .modal-header {
            background: #dc3545;
            color: #fff;
        }
        .modal-header .close {
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
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
                    <a href="admin-dashboard.php" class="nav-link">
                        <i class="fa fa-dashboard"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-categories.php" class="nav-link">
                        <i class="fa fa-dashboard"></i> Danh mục
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-products.php" class="nav-link">
                        <i class="fa fa-shopping-cart"></i> Sản phẩm
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-orders.php" class="nav-link active">
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

        <!-- Main content -->
        <div class="admin-content">
            <div class="page-header">
                <h2>Quản lý đơn hàng</h2>
                <p>Xem và cập nhật trạng thái đơn hàng</p>
            </div>

            <div class="filter-section">
                <div class="row">
                    <div class="col-md-6">
                        <select class="form-control" id="statusFilter">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending">Chờ xử lý</option>
                            <option value="processing">Đang xử lý</option>
                            <option value="shipping">Đang giao</option>
                            <option value="completed">Hoàn thành</option>
                            <option value="cancelled">Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="date" class="form-control" id="dateFilter">
                    </div>
                </div>
            </div>

            <div class="orders-table">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Thông tin liên hệ</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $order): ?>
                            <tr>
                                <td><strong>#<?php echo $order['order_id']; ?></strong></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($order['fullname']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($order['shipping_address']); ?></small>
                                </td>
                                <td>
                                    <i class="fa fa-envelope"></i> <?php echo htmlspecialchars($order['email']); ?><br>
                                    <i class="fa fa-phone"></i> <?php echo htmlspecialchars($order['phone']); ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                <td><strong><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</strong></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-<?php echo getStatusButtonClass($order['order_status']); ?> dropdown-toggle btn-sm" 
                                                type="button" 
                                                id="dropdownStatus_<?php echo $order['order_id']; ?>"
                                                data-toggle="dropdown">
                                            <?php echo getStatusText($order['order_status']); ?>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item <?php echo $order['order_status'] == 'pending' ? 'active' : ''; ?>" 
                                               href="#" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'pending')">
                                                <i class="fa fa-clock-o text-warning"></i> Chờ xử lý
                                            </a>
                                            <a class="dropdown-item <?php echo $order['order_status'] == 'processing' ? 'active' : ''; ?>" 
                                               href="#" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'processing')">
                                                <i class="fa fa-refresh text-info"></i> Đang xử lý
                                            </a>
                                            <a class="dropdown-item <?php echo $order['order_status'] == 'shipping' ? 'active' : ''; ?>" 
                                               href="#" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'shipping')">
                                                <i class="fa fa-truck text-primary"></i> Đang giao
                                            </a>
                                            <a class="dropdown-item <?php echo $order['order_status'] == 'completed' ? 'active' : ''; ?>" 
                                               href="#" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'completed')">
                                                <i class="fa fa-check text-success"></i> Hoàn thành
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item <?php echo $order['order_status'] == 'cancelled' ? 'active' : ''; ?>" 
                                               href="#" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'cancelled')">
                                                <i class="fa fa-times text-danger"></i> Hủy đơn
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle btn-sm" type="button" data-toggle="dropdown">
                                            <i class="fa fa-cog"></i> Thao tác
                                        </button>
                                        <div class="dropdown-menu">
                                            <h6 class="dropdown-header">Cập nhật trạng thái</h6>
                                            <a class="dropdown-item <?php echo $order['order_status'] == 'pending' ? 'active' : ''; ?>" 
                                               href="#" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'pending')">
                                                <i class="fa fa-clock-o text-warning"></i> Chờ xử lý
                                            </a>
                                            <a class="dropdown-item <?php echo $order['order_status'] == 'processing' ? 'active' : ''; ?>" 
                                               href="#" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'processing')">
                                                <i class="fa fa-refresh text-info"></i> Đang xử lý
                                            </a>
                                            <a class="dropdown-item <?php echo $order['order_status'] == 'shipping' ? 'active' : ''; ?>" 
                                               href="#" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'shipping')">
                                                <i class="fa fa-truck text-primary"></i> Đang giao
                                            </a>
                                            <a class="dropdown-item <?php echo $order['order_status'] == 'completed' ? 'active' : ''; ?>" 
                                               href="#" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'completed')">
                                                <i class="fa fa-check text-success"></i> Hoàn thành
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item <?php echo $order['order_status'] == 'cancelled' ? 'active' : ''; ?>" 
                                               href="#" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'cancelled')">
                                                <i class="fa fa-times text-danger"></i> Hủy đơn
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateStatusModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Cập nhật trạng thái đơn hàng</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="updateStatusForm">
                        <input type="hidden" id="orderId">
                        <div class="form-group">
                            <label>Trạng thái mới</label>
                            <select class="form-control" id="newStatus">
                                <option value="pending">Chờ xử lý</option>
                                <option value="processing">Đang xử lý</option>
                                <option value="shipping">Đang giao</option>
                                <option value="completed">Hoàn thành</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" onclick="saveStatus()">
                        <i class="fa fa-save"></i> Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script>
    function updateOrderStatus(orderId, newStatus) {
        if (!confirm('Bạn có chắc muốn thay đổi trạng thái đơn hàng?')) {
            return;
        }

        $.ajax({
            url: 'update-order-status.php',
            method: 'POST',
            data: {
                order_id: orderId,
                status: newStatus
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload(); // Tải lại trang sau khi cập nhật thành công
                } else {
                    alert('Lỗi: ' + response.message);
                }
            },
            error: function() {
                alert('Có lỗi xảy ra khi cập nhật trạng thái');
            }
        });
    }

    // Xử lý lọc
    $('#statusFilter').change(function() {
        var status = $(this).val();
        var currentUrl = new URL(window.location.href);
        if(status) {
            currentUrl.searchParams.set('status', status);
        } else {
            currentUrl.searchParams.delete('status');
        }
        window.location.href = currentUrl.toString();
    });

    $('#dateFilter').change(function() {
        var date = $(this).val();
        var currentUrl = new URL(window.location.href);
        if(date) {
            currentUrl.searchParams.set('date', date);
        } else {
            currentUrl.searchParams.delete('date');
        }
        window.location.href = currentUrl.toString();
    });
    $(document).ready(function() {
        var urlParams = new URLSearchParams(window.location.search);
        $('#statusFilter').val(urlParams.get('status') || '');
        $('#dateFilter').val(urlParams.get('date') || '');
    });
    </script>
</body>
</html> 