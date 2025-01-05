<?php
session_start();
include 'config.php';

try {
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    $type = isset($_GET['type']) ? $_GET['type'] : null;
    $gender = isset($_GET['gender']) ? $_GET['gender'] : null;
    $search = isset($_GET['query']) ? trim($_GET['query']) : '';
    $pdo = new PDO("mysql:host=localhost;dbname=db_web", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT p.*, c.category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE 1=1";
    $params = array();

    if ($category == 4) { 
        $sql .= " AND (p.product_name LIKE :sport 
                 OR p.description LIKE :sport 
                 OR p.product_type = 'the-thao')";
        $params[':sport'] = '%thể thao%';
    } elseif ($category) {
        $sql .= " AND p.category_id = :category";
        $params[':category'] = $category;
    }

    if ($type) {
        $sql .= " AND p.product_type = :type";
        $params[':type'] = $type;
    }

    if ($gender) {
        $sql .= " AND p.gender = :gender";
        $params[':gender'] = $gender;
    }

    if ($search) {
        $sql .= " AND (p.product_name LIKE :search 
                 OR p.description LIKE :search 
                 OR c.category_name LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <title>H&V Shop | Tìm kiếm</title>
    
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">   
    <link href="css/jquery.smartmenus.bootstrap.css" rel="stylesheet">
    <link href="css/product.css" rel="stylesheet">    
</head>
<body>
<header id="aa-header">
    <div class="aa-header-top">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="aa-header-top-area">
              <div class="aa-header-top-left">
                <div class="aa-language">
                  <div class="dropdown">
                  </div>
                </div> 
               
              <div class="aa-header-top">
    </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="aa-header-bottom">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="aa-header-bottom-area">
              <div class="aa-logo">
                <a href="index.php">
                  <span class="fa fa-shopping-cart"></span>
                  <p>H&V<strong>Shop</strong> <span>Thời trang của bạn</span></p>
                </a>

              </div>
              <div class="aa-cartbox">
                <a class="aa-cart-link" href="cart.php">
                  <span class="fa fa-shopping-basket"></span>
                  <span class="aa-cart-title">GIỎ HÀNG</span>
                  <span class="aa-cart-notify">
                    <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                  </span>
                </a>
                <div class="aa-cartbox-summary">
                  <ul>
                    <?php
                    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                      $total = 0;
                      foreach ($_SESSION['cart'] as $product_id => $item) {
                        $total += $item['price'] * $item['quantity'];
                    ?>
                        <li>
                          <a class="aa-cartbox-img" href="product-detail.php?id=<?php echo $product_id; ?>">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>">
                          </a>
                          <div class="aa-cartbox-info">
                            <h4>
                              <a href="product-detail.php?id=<?php echo $product_id; ?>">
                                <?php echo htmlspecialchars($item['name']); ?>
                              </a>
                            </h4>
                            <p><?php echo $item['quantity']; ?> x <?php echo number_format($item['price'], 0, ',', '.'); ?>đ</p>
                          </div>
                          <a class="aa-remove-product" href="remove_from_cart.php?id=<?php echo $product_id; ?>">
                            <span class="fa fa-times"></span>
                          </a>
                        </li>
                    <?php
                      }
                    ?>
                      <li>
                        <span class="aa-cartbox-total-title">Tổng cộng</span>
                        <span class="aa-cartbox-total-price">
                          <?php echo number_format($total, 0, ',', '.'); ?>đ
                        </span>
                      </li>
                    <?php
                    } else {
                    ?>
                      <li><p>Giỏ hàng trống</p></li>
                    <?php
                    }
                    ?>
                  </ul>
                  <a class="aa-cartbox-checkout aa-primary-btn" href="checkout.php">Thanh toán</a>
                </div>
              </div>
              <div class="aa-search-box">
                <form action="search.php" method="GET">
                  <input type="text" name="query" placeholder="Tìm kiếm sản phẩm...">
                  <button type="submit"><span class="fa fa-search"></span></button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
  <section id="menu">
    <div class="container">
      <div class="menu-area">
        <div class="navbar navbar-default" role="navigation">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>          
          </div>
          <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li><a href="index.php">Trang chủ</a></li>
              <li><a href="#">Thời trang nam <span class="caret"></span></a>
                <ul class="dropdown-menu">                
                  <li><a href="product.php?category=1&type=ao-thun">Áo thun</a></li>
                  <li><a href="product.php?category=1&type=ao-somi">Áo sơ mi</a></li>
                  <li><a href="product.php?category=1&type=quan">Quần</a></li>
                  <li><a href="product.php?category=1">Xem tất cả</a></li>
                </ul>
              </li>
              <li><a href="#">Thời trang nữ <span class="caret"></span></a>
                <ul class="dropdown-menu">                
                  <li><a href="product.php?category=2&type=ao">Áo</a></li>
                  <li><a href="product.php?category=2&type=dam">Đầm</a></li>
                  <li><a href="product.php?category=2&type=chan-vay">Chân váy</a></li>
                  <li><a href="product.php?category=2">Xem tất cả</a></li>
                </ul>
              </li>
              <li><a href="#">Thời trang trẻ em <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="product.php?category=3&gender=boy">Bé trai</a></li>
                  <li><a href="product.php?category=3&gender=girl">Bé gái</a></li>
                  <li><a href="product.php?category=3">Xem tất cả</a></li>
                </ul>
              </li>
              <li><a href="product.php?category=4">Thời trang thể thao</a></li>
              <li><a href="contact.php">Liên hệ</a></li>
              <li><a href="#">Trang <span class="caret"></span></a>
                <ul class="dropdown-menu">                
                  <li><a href="product.php">Cửa hàng</a></li>
                  <li><a href="checkout.php">Thanh toán</a></li>
                </ul>
              </li>
            </ul>
          </div> 
        </div>
      </div>       
    </div>

    <!--MENU-->
  </section>
    <section id="aa-product-category" >
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="aa-product-catg-content">
                        <div class="aa-product-catg-head">
                            <div class="aa-product-catg-head-left">
                                <h2>
                                    <?php 
                                    if($search) {
                                        echo 'Kết quả tìm kiếm cho: "' . htmlspecialchars($search) . '"';
                                    } elseif($category) {
                                        echo 'Danh mục: ' . htmlspecialchars($products[0]['category_name'] ?? 'Tất cả sản phẩm');
                                    } else {
                                        echo 'Tất cả sản phẩm';
                                    }
                                    ?>
                                </h2>
                            </div>
                        </div>
                        <div class="aa-product-catg-body">
                            <ul class="aa-product-catg">
                                <?php foreach($products as $product): ?>
                                <li>
                                    <figure>
                                        <a class="aa-product-img" href="product-detail.php?id=<?php echo $product['product_id']; ?>">
                                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                        </a>
                                        <form class="add-to-cart-form" method="POST" style="display: inline;">
                                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="aa-add-card-btn">
                                                <span class="fa fa-shopping-cart"></span>Thêm vào giỏ
                                            </button>
                                        </form>
                                        <figcaption>
                                            <h4 class="aa-product-title">
                                                <a href="product-detail.php?id=<?php echo $product['product_id']; ?>">
                                                    <?php echo htmlspecialchars($product['product_name']); ?>
                                                </a>
                                            </h4>
                                            <span class="aa-product-price">
                                                <?php echo number_format($product['price'], 0, ',', '.'); ?>đ
                                            </span>
                                        </figcaption>
                                    </figure>
                                    <div class="aa-product-hvr-content">
                                        <a href="#" data-toggle="tooltip" data-placement="top" title="Yêu thích">
                                            <span class="fa fa-heart-o"></span>
                                        </a>
                                        <a href="#" data-toggle="tooltip" data-placement="top" title="So sánh">
                                            <span class="fa fa-exchange"></span>
                                        </a>
                                    </div>
                                    <span class="aa-badge aa-sale">GIẢM GIÁ!</span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if(count($products) == 0): ?>
                                <p class="text-center">Không tìm thấy sản phẩm nào phù hợp.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>  
    <script type="text/javascript" src="js/jquery.smartmenus.js"></script>
    <script type="text/javascript" src="js/jquery.smartmenus.bootstrap.js"></script>  
    <script>
    $(document).ready(function() {
        $('.add-to-cart-form').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: 'add_to_cart.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) { 
                        $('.aa-cart-notify').text(response.cart_count);  
                        var notification = $('<div class="alert alert-success">')
                            .css({
                                'position': 'fixed',
                                'top': '20px',
                                'right': '20px',
                                'z-index': '9999',
                                'padding': '15px',
                                'border-radius': '4px',
                                'background-color': '#dff0d8',
                                'border-color': '#d6e9c6',
                                'color': '#3c763d'
                            })
                            .text('Thêm vào giỏ hàng thành công!')
                            .appendTo('body')
                            .fadeIn();

                        setTimeout(function() {
                            notification.fadeOut(function() {
                                $(this).remove();
                            });
                        }, 2000); 
                        $.get('get_cart.php', function(cartData) {
                            $('.aa-cartbox-summary').html(cartData);
                        });
                    } else {
                        alert('Có lỗi xảy ra: ' + response.message);
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi thêm vào giỏ hàng');
                }
            });
        });
    });
    </script>

    <style>
    .alert-success {
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    </style>
</body>
</html>
