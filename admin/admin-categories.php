<?php
require_once '../init.php';
if(isset($_POST['add_category'])) {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        if (!isset($_POST['category_name']) || empty(trim($_POST['category_name']))) {
            throw new Exception("Tên danh mục không được để trống");
        }

        $category_name = trim($_POST['category_name']);
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE category_name = ?");
        $check_stmt->execute([$category_name]);
        
        if($check_stmt->fetchColumn() > 0) {
            throw new Exception("Danh mục này đã tồn tại");
        }
        $stmt = $pdo->prepare("INSERT INTO categories (category_name, description, status) VALUES (?, ?, ?)");
        $result = $stmt->execute([$category_name, $description, $status]);

        if($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Thêm danh mục thành công'
            ]);
        } else {
            throw new Exception("Không thể thêm danh mục");
        }

    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// Nếu không phải request AJAX, load trang bình thường
require_once 'admin-header.php';

if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== 'admin') {
    header('Location: admin-403.php');
    exit;
}

// Xử lý xóa danh mục
if(isset($_GET['delete_id'])) {
    try {
        $check_sql = "SELECT COUNT(*) FROM products WHERE category_id = ?";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$_GET['delete_id']]);
        
        if($check_stmt->fetchColumn() > 0) {
            echo "<script>alert('Không thể xóa danh mục này vì đang có sản phẩm thuộc danh mục!');</script>";
        } else {
            $sql = "DELETE FROM categories WHERE category_id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$_GET['delete_id']]);

            if($result) {
                echo "<script>
                    alert('Xóa danh mục thành công!');
                    window.location.href = 'admin-categories.php';
                </script>";
                exit();
            }
        }
    } catch(Exception $e) {
        echo "<script>alert('Có lỗi xảy ra: " . $e->getMessage() . "');</script>";
    }
}

$query = "SELECT 
    COALESCE(c.category_id, '') as category_id,
    c.category_name, 
    c.description,
    COUNT(p.product_id) as product_count
FROM categories c
LEFT JOIN products p ON c.category_id = p.category_id
GROUP BY c.category_id, c.category_name, c.description";

try {
    $categories = $pdo->query($query)->fetchAll();
} catch(PDOException $e) {
    echo "Lỗi truy vấn: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <title>Quản lý danh mục - Admin</title>
    
    <link href="../css/font-awesome.css" rel="stylesheet">
    <link href="../css/bootstrap.css" rel="stylesheet">   
    <link href="../css/admin.css" rel="stylesheet">    
</head>
<body class="admin-page">
    <div class="admin-sidebar" id="adminSidebar">
        <button id="sidebarToggle" class="sidebar-toggle">
            <i class="fa fa-times"></i>
        </button>
        <div class="admin-logo">
            <h2>H&V Admin</h2>
        </div>
        <ul class="admin-menu">
            <li><a href="admin-dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="admin-products.php"><i class="fa fa-shopping-bag"></i> Sản phẩm</a></li>
            <li><a href="admin-categories.php"><i class="fa fa-list"></i> Danh mục</a></li>
            <li><a href="admin-orders.php"><i class="fa fa-shopping-cart"></i> Đơn hàng</a></li>
            <li class="active"><a href="admin-users.php"><i class="fa fa-users"></i> Người dùng</a></li>
            <li><a href="admin-logout.phpphp"><i class="fa fa-sign-out"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <div class="admin-main">
        <div class="admin-header">
            <h1>Quản lý danh mục</h1>
            <button class="btn btn-add-category" data-toggle="modal" data-target="#addCategoryModal">
                <i class="fa fa-plus"></i> Thêm danh mục
            </button>
        </div>

        <div class="admin-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên danh mục</th>
                        <th>Mô tả</th>
                        <th>Số sản phẩm</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($categories as $category): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['category_id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($category['category_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($category['description'] ?? ''); ?></td>
                            <td><?php echo (int)$category['product_count']; ?></td>
                            <td>
                                <button type='button' class='btn btn-primary btn-edit' onclick='editCategory(<?php echo $category['category_id']; ?>)'>
                                    <i class='fa fa-edit'></i> Sửa
                                </button>
                                <a href='admin-categories.php?delete_id=<?php echo $category['category_id']; ?>' 
                                   class='btn btn-danger' 
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                    <i class='fa fa-trash'></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addCategoryModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Thêm danh mục mới</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="addCategoryForm">
                    <div class="modal-body">
                        <input type="hidden" name="add_category" value="1">
                        <div class="form-group">
                            <label>Tên danh mục</label>
                            <input type="text" name="category_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Mô tả</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Trạng thái</label>
                            <select name="status" class="form-control">
                                <option value="1">Hoạt động</option>
                                <option value="0">Không hoạt động</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm danh mục</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCategoryModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Sửa danh mục</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        <div class="form-group">
                            <label>Tên danh mục</label>
                            <input type="text" class="form-control" value="Áo nam" required>
                        </div>
                        <div class="form-group">
                            <label>Mô tả</label>
                            <textarea class="form-control" rows="3">Các loại áo dành cho nam giới</textarea>
                        </div>
                        <div class="form-group">
                            <label>Trạng thái</label>
                            <select class="form-control">
                                <option value="1" selected>Hoạt động</option>
                                <option value="0">Không hoạt động</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="../js/bootstrap.js"></script>  
    <script>
    $(document).ready(function() {
        $('#addCategoryForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                type: 'POST',
                url: 'admin-categories.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        alert(response.message);
                        $('#addCategoryModal').modal('hide');
                        location.reload();
                    } else {
                        alert(response.message || 'Có lỗi xảy ra!');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Có lỗi khi gửi yêu cầu: ' + error);
                }
            });
        });
    });

    function deleteCategory(id) {
        if (confirm('Bạn có chắc muốn xóa danh mục này?')) {
            window.location.href = 'admin-categories.php?delete_id=' + id;
        }
    }

    function editCategory(id) {
        $.ajax({
            url: 'get_category.php',
            type: 'GET',
            data: { id: id },
            success: function(response) {
                var category = JSON.parse(response);
                $('#edit_category_id').val(category.category_id);
                $('#edit_category_name').val(category.category_name);
                $('#edit_description').val(category.description);
                $('#edit_status').val(category.status);
                $('#editCategoryModal').modal('show');
            }
        });
    }

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