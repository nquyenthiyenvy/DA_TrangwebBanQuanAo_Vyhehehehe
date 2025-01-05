<?php
require_once '../init.php';
error_log("Current user: " . $_SESSION['username']);
error_log("Current role: " . $_SESSION['role_name']);

if (!checkPermission('products')) {
    error_log("Permission denied for: " . $_SESSION['username']);
    header('Location: admin-403.php');
    exit;
}
if (!in_array($_SESSION['role_name'], ['admin', 'manager-product'])) {
    header('Location: admin-403.php');
    exit;
}
if(isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock_quantity = $_POST['stock_quantity'];
    $size = $_POST['size'];
    $color = $_POST['color'];

    if(isset($_FILES['image_url'])) {
        $file = $_FILES['image_url'];
        $file_name = $file['name'];
        
        if(move_uploaded_file($file['tmp_name'], "../uploads/" . $file_name)) {
            $sql = "INSERT INTO products (product_name, category_id, price, description, stock_quantity, image_url, size, color) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $product_name,
                $category_id, 
                $price,
                $description,
                $stock_quantity,
                'uploads/' . $file_name,
                $size,
                $color
            ]);

            if($result) {
                echo "<script>
                    alert('Thêm sản phẩm thành công!'); 
                    window.location.href = 'admin-products.php';
                </script>";
                exit();
            } else {
                echo "<script>alert('Có lỗi khi thêm sản phẩm!');</script>";
            }
        } else {
            echo "<script>alert('Không thể upload ảnh!');</script>";
        }
    } else {
        echo "<script>alert('Vui lòng chọn ảnh sản phẩm!');</script>";
    }
}
if(isset($_SESSION['just_added'])) {
    unset($_SESSION['just_added']);
}
if(isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<script>alert('Thêm sản phẩm thành công!');</script>";
}
if (isset($_GET['delete_product_id'])) {
    $product_id = $_GET['delete_product_id'];

    $sql = "DELETE FROM products WHERE product_id = :product_id";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        echo "<script>alert('Sản phẩm đã được xóa thành công!');</script>";
        header("Location: admin-products.php"); 
        exit();
    } catch (PDOException $e) {
        echo "Lỗi xóa sản phẩm: " . $e->getMessage();
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock_quantity = $_POST['stock_quantity'];
    $size = isset($_POST['size']) ? $_POST['size'] : NULL;
    $color = isset($_POST['color']) ? $_POST['color'] : NULL;
    $status = isset($_POST['status']) ? $_POST['status'] : 0;

    try {
        $sql = "UPDATE products SET 
                product_name = :product_name,
                category_id = :category_id,
                price = :price,
                description = :description,
                stock_quantity = :stock_quantity,
                size = :size,
                color = :color,
                status = :status";
        if ($_FILES['image_url']['error'] == 0) {
            $image_name = time() . '_' . $_FILES['image_url']['name'];
            $upload_dir = '../uploads/';
            $image_path = 'uploads/' . $image_name;
            
            if (move_uploaded_file($_FILES['image_url']['tmp_name'], $upload_dir . $image_name)) {
                $sql .= ", image_url = :image_url";
            }
        }

        $sql .= " WHERE product_id = :product_id";
        
        $stmt = $pdo->prepare($sql);
        $params = [
            ':product_id' => $product_id,
            ':product_name' => $product_name,
            ':category_id' => $category_id,
            ':price' => $price,
            ':description' => $description,
            ':stock_quantity' => $stock_quantity,
            ':size' => $size,
            ':color' => $color,
            ':status' => $status
        ];

        if (isset($image_path)) {
            $params[':image_url'] = $image_path;
        }

        if ($stmt->execute($params)) {
            echo "<script>alert('Cập nhật sản phẩm thành công!');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Lỗi cập nhật sản phẩm: " . $e->getMessage() . "');</script>";
    }
}

$categoryFilter = isset($_GET['category_id']) ? $_GET['category_id'] : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

$orderBy = isset($_GET['sort']) ? $_GET['sort'] : 'id';

$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE 1=1";

$params = array();

//TIM KIEM
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $sql .= " AND (p.product_name LIKE :search 
              OR p.description LIKE :search 
              OR c.category_name LIKE :search)";
    $params[':search'] = "%$search%";
}
if ($categoryFilter) {
    $sql .= " AND p.category_id = :category_id";
    $params[':category_id'] = $categoryFilter;
}

switch($orderBy) {
    case 'id_asc':
        $sql .= " ORDER BY p.product_id ASC";
        break;
    case 'id_desc':
        $sql .= " ORDER BY p.product_id DESC";
        break;
    case 'name_asc':
        $sql .= " ORDER BY p.product_name ASC";
        break;
    case 'name_desc':
        $sql .= " ORDER BY p.product_name DESC";
        break;
    case 'price_asc':
        $sql .= " ORDER BY CAST(p.price AS DECIMAL) ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY CAST(p.price AS DECIMAL) DESC";
        break;
    default:
        $sql .= " ORDER BY p.product_id DESC";
}
error_log("SQL Query: " . $sql);

$stmt = $pdo->prepare($sql);
foreach($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute(); 
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $count = $stmt->rowCount();
    echo '<div class="alert alert-info">
            Tìm thấy ' . $count . ' sản phẩm cho từ khóa "' . htmlspecialchars($_GET['search']) . '"
          </div>';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý sản phẩm - Admin</title>
    
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link href="../css/font-awesome.css" rel="stylesheet">
    <link href="../css/admin.css" rel="stylesheet"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</head>
<body class="admin-page">
    <div class="admin-sidebar" id="adminSidebar">
        <button id="sidebarToggle" class="sidebar-toggle">
            <i class="fa fa-times"></i>
        </button>
        <div class="admin-logo">
            <h2>H&V Admin</h2>
            <div class="admin-user-info">
                <i class="fa fa-user"></i>
                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <small><?php echo ucfirst(str_replace('-', ' ', $_SESSION['role_name'])); ?></small>
            </div>
        </div>
        <ul class="admin-menu">
            <?php if($_SESSION['role_name'] === 'admin'): ?>
                <li><a href="admin-dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <?php endif; ?>

            <li class="active"><a href="admin-products.php"><i class="fa fa-shopping-bag"></i> Sản phẩm</a></li>

            <?php if($_SESSION['role_name'] === 'admin'): ?>
                <li><a href="admin-categories.php"><i class="fa fa-list"></i> Danh mục</a></li>
                <li><a href="admin-orders.php"><i class="fa fa-shopping-cart"></i> Đơn hàng</a></li>
                <li><a href="admin-users.php"><i class="fa fa-users"></i> Người dùng</a></li>
            <?php endif; ?>

            <li><a href="admin-logout.php"><i class="fa fa-sign-out"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <div class="admin-main">
        <div class="admin-header">
            <h1>Quản lý sản phẩm</h1>
            <div class="action-buttons">
                <button type="button" class="btn btn-danger" onclick="deleteSelected()" id="deleteMultiple" style="display: none;">
                    <i class="fa fa-trash"></i> Xóa đã chọn
                </button>
                <button type="button" class="btn btn-add-product" onclick="showAddProductForm()">
                    <i class="fa fa-plus"></i> Thêm sản phẩm
                </button>
            </div>
        </div>

        <div class="admin-filters">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-control" id="categoryFilter" name="category_id" onchange="window.location='admin-products.php?category_id='+this.value">
                        <option value="">Tất cả danh mục</option>
                        <option value="1" <?php echo ($categoryFilter == '1') ? 'selected' : ''; ?>>Áo nam</option>
                        <option value="2" <?php echo ($categoryFilter == '2') ? 'selected' : ''; ?>>Áo nữ</option>
                        <option value="3" <?php echo ($categoryFilter == '3') ? 'selected' : ''; ?>>Trẻ em</option> 
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control" id="statusFilter" name="status" onchange="window.location='admin-products.php?status='+this.value">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" <?php echo ($statusFilter == '1') ? 'selected' : ''; ?>>Còn hàng</option>
                        <option value="0" <?php echo ($statusFilter == '0') ? 'selected' : ''; ?>>Hết hàng</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control sort-select" onchange="applySorting(this.value)">
                        <option value="id_desc" <?php echo ($orderBy == 'id_desc') ? 'selected' : ''; ?>>Sắp xếp theo</option>
                        <option value="id_asc" <?php echo ($orderBy == 'id_asc') ? 'selected' : ''; ?>>ID tăng dần</option>
                        <option value="id_desc" <?php echo ($orderBy == 'id_desc') ? 'selected' : ''; ?>>ID giảm dần</option>
                        <option value="name_asc" <?php echo ($orderBy == 'name_asc') ? 'selected' : ''; ?>>Tên A-Z</option>
                        <option value="name_desc" <?php echo ($orderBy == 'name_desc') ? 'selected' : ''; ?>>Tên Z-A</option>
                        <option value="price_asc" <?php echo ($orderBy == 'price_asc') ? 'selected' : ''; ?>>Giá tăng dần</option>
                        <option value="price_desc" <?php echo ($orderBy == 'price_desc') ? 'selected' : ''; ?>>Giá giảm dần</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="search-box">
                        <form action="" method="GET" class="search-form">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sản phẩm..." 
                                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-search">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        

        <div class="admin-table">
            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll"></th>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td><input type='checkbox' class='product-checkbox' value='{$row['product_id']}'></td>";
                        echo "<td>{$row['product_id']}</td>";
                        echo "<td><img src='../{$row['image_url']}' alt='Ảnh sản phẩm' class='product-thumb' style='max-width: 100px;'></td>";
                        echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                        
                        echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
                        echo "<td>" . number_format($row['price'], 0, ',', '.') . " VNĐ</td>";
                        echo "<td>";
                        if (isset($row['status'])) {
                            echo "<span class='status-badge " . ($row['status'] == 1 ? 'in-stock' : 'out-of-stock') . "'>";
                            echo $row['status'] == 1 ? 'Còn hàng' : 'Hết hàng';
                            echo "</span>";
                        } else {
                            echo "Không xác định";
                        }
                        echo "</td>";
                        echo "<td>
                            <button type='button' class='btn btn-primary btn-edit' data-id='{$row['product_id']}'>
                                <i class='fa fa-edit'></i> Sửa
                            </button>
                            <a href='?delete_product_id={$row['product_id']}' class='btn btn-danger' onclick='return confirm(\"Bạn chắc chắn muốn xóa sản phẩm này?\")'>
                                <i class='fa fa-trash'></i> Xóa
                            </a>
                        </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div> 
<div class="popup-overlay" id="formOverlay" style="display: none;"></div>
<div class="popup-form" id="addProductForm" style="display: none;">
    <div class="modal-header">
        <h4>Thêm sản phẩm mới</h4>
        <button type="button" class="close" onclick="closeAddProductForm()">&times;</button>
    </div>
    <form action="" method="POST" enctype="multipart/form-data" id="productForm">
        <input type="hidden" name="MAX_FILE_SIZE" value="2097152" /> <!-- 2MB = 2 * 1024 * 1024 -->
        
        <div class="modal-body">
            <div class="form-group">
                <label>Tên sản phẩm:</label>
                <input type="text" class="form-control" name="product_name" required>
            </div>
            <div class="form-group">
                <label>Danh mục:</label>
                <select class="form-control" name="category_id" required>
                    <option value="">Chọn danh mục</option>
                    <?php 
                    $stmt_categories = $pdo->query("SELECT * FROM categories WHERE status = 1");
                    while($category = $stmt_categories->fetch()) {
                        echo "<option value='".$category['category_id']."'>".$category['category_name']."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Giá:</label>
                <input type="number" class="form-control" name="price" required>
            </div>
            <div class="form-group">
                <label>Mô tả:</label>
                <textarea class="form-control" name="description" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Số lượng:</label>
                <input type="number" class="form-control" name="stock_quantity" required>
            </div>
            <div class="form-group">
                <label>Kích thước:</label>
                <input type="text" class="form-control" name="size">
            </div>
            <div class="form-group">
                <label>Màu sắc:</label>
                <input type="text" class="form-control" name="color">
            </div>
            <div class="form-group">
                <label>Ảnh sản phẩm:</label>
                <input type="file" 
                       class="form-control" 
                       name="image_url" 
                       accept="image/*" 
                       required 
                       onchange="validateFileSize(this)">
                <small class="text-muted">Kích thước tối đa: 2MB</small>
                <img id="imagePreview" src="" alt="" style="max-width: 100px; margin-top: 10px; display: none;">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeAddProductForm()">Hủy</button>
            <button type="submit" name="add_product" class="btn btn-primary">Thêm sản phẩm</button>
        </div>
    </form>
</div> 
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Sửa sản phẩm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editProductForm" action="admin-products.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="edit_product" value="1">
                    <input type="hidden" name="product_id" id="edit_product_id">
                    <div class="form-group">
                        <label for="edit_product_name">Tên sản phẩm</label>
                        <input type="text" class="form-control" name="product_name" id="edit_product_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_category_id">Danh mục</label>
                        <select name="category_id" id="edit_category_id" class="form-control" required>
                            <option value="1">Áo nam</option>
                            <option value="2">Áo nữ</option>
                            <option value="3">Trẻ em</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_price">Giá sản phẩm</label>
                        <input type="number" class="form-control" name="price" id="edit_price" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Mô tả</label>
                        <textarea class="form-control" name="description" id="edit_description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_stock_quantity">Số lượng tồn kho</label>
                        <input type="number" class="form-control" name="stock_quantity" id="edit_stock_quantity" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_size">Kích thước</label>
                        <input type="text" class="form-control" name="size" id="edit_size">
                    </div>
                    <div class="form-group">
                        <label for="edit_color">Màu sắc</label>
                        <input type="text" class="form-control" name="color" id="edit_color">
                    </div>
                    <div class="form-group">
                        <label for="edit_image_url">Ảnh sản phẩm</label>
                        <input type="file" class="form-control" name="image_url" id="edit_image_url">
                        <img id="current_image" src="" alt="Current Image" style="max-width: 100px; margin-top: 10px;">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="submit" form="editProductForm" class="btn btn-primary">Cập nhật</button>
            </div>
        </div>
    </div>
</div>

<script>
function applySorting(sortValue) { 
    let currentUrl = new URL(window.location.href);
    let params = currentUrl.searchParams; 
    params.set('sort', sortValue); 
    window.location.href = currentUrl.toString();
} 
document.querySelector('.popup-form').addEventListener('click', function(e) {
    e.stopPropagation();
});

function showAddProductForm() {
    document.getElementById('addProductForm').style.display = 'block';
    document.getElementById('formOverlay').style.display = 'block';
}

function closeAddProductForm() {
    document.getElementById('addProductForm').style.display = 'none';
    document.getElementById('formOverlay').style.display = 'none';
    document.getElementById('productForm').reset();
    document.getElementById('imagePreview').style.display = 'none';
} 
document.getElementById('formOverlay').addEventListener('click', closeAddProductForm);

function previewImage(input) {
    var preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
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