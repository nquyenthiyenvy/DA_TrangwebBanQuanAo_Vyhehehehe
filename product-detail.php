<?php
session_start();
try {
    $pdo = new PDO("mysql:host=localhost;dbname=db_web", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    $product = null;
    
    if (isset($_GET['id'])) {
        $product_id = $_GET['id'];
        if (!is_numeric($product_id)) {
            throw new Exception("ID sản phẩm không hợp lệ");
        }
        
        $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        if (!$product) {
            throw new Exception("Không tìm thấy sản phẩm");
        }
        if ($product) {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND product_id != ? LIMIT 4");
            $stmt->execute([$product['category_id'], $product_id]);
            $related_products = $stmt->fetchAll();
        }
    }
    
} catch(PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $error_message = "Có lỗi xảy ra khi truy cập cơ sở dữ liệu";
} catch(Exception $e) {
    error_log("Application Error: " . $e->getMessage());
    $error_message = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <title>H&V Shop | Chi tiết sản phẩm</title>
    
     <link href="css/font-awesome.css" rel="stylesheet">
     <link href="css/bootstrap.css" rel="stylesheet">   
     <link href="css/jquery.smartmenus.bootstrap.css" rel="stylesheet">
     <link rel="stylesheet" type="text/css" href="css/jquery.simpleLens.css">    
     <link rel="stylesheet" type="text/css" href="css/slick.css">
     <link rel="stylesheet" type="text/css" href="css/nouislider.css">
     <link id="switcher" href="css/theme-color/default-theme.css" rel="stylesheet">
     <link href="css/sequence-theme.modern-slide-in.css" rel="stylesheet" media="all">
    <link href="css/style.css" rel="stylesheet">    
    <link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
    <style>
      /* CSS cho phần star rating */
      .star-rating {
          display: inline-block;
          direction: rtl;
          margin-bottom: 15px;
      }
      
      .star-rating input {
          display: none;
      }
      
      .star-rating label {
          color: #ddd;
          font-size: 35px;
          padding: 0 5px;
          cursor: pointer;
          transition: all 0.3s ease;
      }
      
      .star-rating input:checked ~ label,
      .star-rating label:hover,
      .star-rating label:hover ~ label {
          color: #FFD700;
          transform: scale(1.1);
      }
      
 
      .rating-stars {
          color: #FFD700;
          font-size: 24px;
          letter-spacing: 2px;
      }
      
      .rating-form {
          background: #f9f9f9;
          padding: 20px;
          border-radius: 8px;
          margin-bottom: 30px;
          box-shadow: 0 2px 5px rgba(0,0,0,0.1);
          transition: all 0.3s ease;
      }
      
      .rating-form:hover {
          box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      }
      
      .rating-form button {
          background: #ff6666;
          color: white;
          border: none;
          padding: 10px 25px;
          border-radius: 4px;
          cursor: pointer;
          transition: all 0.3s ease;
          font-weight: bold;
      }
      
      .rating-form button:hover {
          background: #ff4444;
          transform: translateY(-2px);
      }
      
      .rating-item {
          border-bottom: 1px solid #eee;
          padding: 15px 0;
          transition: all 0.3s ease;
      }
      
      .rating-item:hover {
          background: #f9f9f9;
          padding-left: 10px;
      }
      
      .rating-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 10px;
      }
      
      .username {
          font-weight: bold;
          color: #333;
      }
      
      .rating-date {
          color: #888;
          font-size: 14px;
      }
      
      .rating-comment {
          color: #666;
          line-height: 1.6;
          margin-top: 10px;
          padding: 10px;
          background: #fff;
          border-radius: 4px;
      }
      
      /* Style cho textarea */
      .rating-form textarea {
          width: 100%;
          padding: 12px;
          border: 1px solid #ddd;
          border-radius: 4px;
          margin: 15px 0;
          min-height: 100px;
          resize: vertical;
      }
      
      .rating-form textarea:focus {
          border-color: #FFD700;
          outline: none;
          box-shadow: 0 0 5px rgba(255, 215, 0, 0.3);
      }
    </style>
  </head>
  <body>  
    <div id="wpf-loader-two">          
      <div class="wpf-loader-two-inner">
        <span>Loading</span>
      </div>
    </div> 
    <a class="scrollToTop" href="#"><i class="fa fa-chevron-up"></i></a>

  <header id="aa-header">
    <div class="aa-header-top">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="aa-header-top-area">
              <div class="aa-header-top-left">
                <div class="aa-language">
                  <div class="dropdown">
                    <a class="btn dropdown-toggle" href="#" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                      <img src="img/flag/flag_vietnam.png" alt="english flag">VIETNAM
                      <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                      <li><a href="#"><img src="img/flag/french.jpg" alt="">FRENCH</a></li>
                      <li><a href="#"><img src="img/flag/english.jpg" alt="">ENGLISH</a></li>
                      <li><a href="#" ><img src="img/flag/flag_vietnam.png" alt="">VIETNAM</a></li>
                    </ul>
                  </div>
                </div> 
                <div class="aa-currency">
                  <div class="dropdown">
                    <a class="btn dropdown-toggle" href="#" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                      <i class="fa fa-usd"></i>DONG
                      <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                      <li><a href="#"><i class="fa fa-euro"></i>EURO</a></li>
                      <li><a href="#"><i class="fa fa-jpy"></i>YEN</a></li>
                      <li><a href="#"><i class="fa fa -dong"></i>DONG</a></li>
                    </ul>
                  </div>
                </div> 
                <div class="cellphone hidden-xs">
                  <p><span class="fa fa-phone"></span>0344-207-275</p>
                </div>
              </div>
               <div class="aa-header-top-right">
                <ul class="aa-head-top-nav-right">
                  <?php if(isset($_SESSION['username'])): ?>
                    <li><a href="account.php"><i class="fa fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
                    <li class="hidden-xs"><a href="logout.php"><i class="fa fa-sign-out"></i> Đăng xuất</a></li>
                  <?php else: ?>
                    <li><a href="dangnhap.php">Đăng nhập</a></li>
                  <?php endif; ?>
                  <li><a href="cart.php"><i class="fa fa-shopping-cart"></i> Giỏ hàng</a></li>
                  <li><a href="checkout.php"><i class="fa fa-credit-card"></i> Thanh toán</a></li>
                </ul>
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
    </div>
  </section> 

  <section id="aa-product-details">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="aa-product-details-area">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php elseif ($product): ?>
            <div class="aa-product-details-content">
              <div class="row"> 
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <div class="aa-product-view-slider">
                    <div class="simpleLens-gallery-container">
                      <div class="simpleLens-container">
                        <div class="simpleLens-big-image-container">
                          <a data-lens-image="<?php echo isset($product['image_url']) ? htmlspecialchars($product['image_url']) : ''; ?>">
                            <img src="<?php echo isset($product['image_url']) ? htmlspecialchars($product['image_url']) : ''; ?>" 
                                 class="simpleLens-big-image"
                                 alt="<?php echo isset($product['product_name']) ? htmlspecialchars($product['product_name']) : 'Sản phẩm'; ?>">
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                

                <div class="col-md-7 col-sm-7 col-xs-12">
                  <div class="aa-product-view-content">
                    <h3><?php echo isset($product['product_name']) ? htmlspecialchars($product['product_name']) : 'Sản phẩm không có tên'; ?></h3>
                    <div class="aa-price-block">
                      <span class="aa-product-price">
                        <?php echo isset($product['price']) ? number_format($product['price'], 0, ',', '.') : '0đ'; ?>
                      </span>
                    </div>
                    <p><?php echo isset($product['description']) ? nl2br(htmlspecialchars($product['description'])) : 'Mô tả sản phẩm không có'; ?></p>
                    
                    <!-- Form thêm vào giỏ hàng -->
                    <form class="aa-add-to-cart-form" action="add_to_cart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <div class="aa-prod-quantity">
                            <div class="quantity-controls">
                                <button type="button" class="quantity-btn minus" onclick="updateQuantity(-1)">-</button>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" max="99">
                                <button type="button" class="quantity-btn plus" onclick="updateQuantity(1)">+</button>
                            </div>
                        </div>
                        <button type="submit" class="aa-add-to-cart-btn">Thêm vào giỏ hàng</button>
                    </form>
                   
                    <!-- Phần hiển thị đánh giá -->
                    <div class="product-ratings">
                        <h3>Đánh giá sản phẩm</h3>
                        
                        <?php if(isset($product) && $product): ?>
                        <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="rating-form">
                            <form id="ratingForm">
                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                <div class="star-rating">
                                    <?php for($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                                    <label for="star<?php echo $i; ?>">☆</label>
                                    <?php endfor; ?>
                                </div>
                                <textarea name="comment" placeholder="Nhận xét của bạn..." required></textarea>
                                <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                            </form>
                        </div>
                        <?php else: ?>
                        <p>Vui lòng <a href="dangnhap.php">đăng nhập</a> để đánh giá sản phẩm</p>
                        <?php endif; ?>

                        <!-- Hiển thị các đánh giá -->
                        <div class="rating-list">
                            <?php
                            $ratings = $pdo->prepare("
                                SELECT r.*, a.username 
                                FROM ratings r
                                JOIN account a ON r.user_id = a.account_id
                                WHERE r.product_id = ?
                                ORDER BY r.created_at DESC
                            ");
                            $ratings->execute([$product['product_id']]);
                            
                            while($rating = $ratings->fetch()): ?>
                            <div class="rating-item">
                                <div class="rating-header">
                                    <span class="username"><?php echo htmlspecialchars($rating['username']); ?></span>
                                    <span class="rating-stars">
                                        <?php
                                        for($i = 1; $i <= 5; $i++) {
                                            echo ($i <= $rating['rating']) ? '★' : '☆';
                                        }
                                        ?>
                                    </span>
                                    <span class="rating-date"><?php echo date('d/m/Y', strtotime($rating['created_at'])); ?></span>
                                </div>
                                <div class="rating-comment">
                                    <?php echo nl2br(htmlspecialchars($rating['comment'])); ?>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="aa-product-related-item">
              <h3>Sản phẩm liên quan</h3>
              <ul class="aa-product-catg aa-related-item-slider">
                <?php if(isset($related_products) && !empty($related_products)): ?>
                  <?php foreach($related_products as $related): ?>
                    <li>
                      <figure>
                        <a class="aa-product-img" href="product-detail.php?id=<?php echo $related['product_id']; ?>">
                          <img src="<?php echo htmlspecialchars($related['image_url']); ?>" 
                               alt="<?php echo htmlspecialchars($related['product_name']); ?>">
                        </a>
                        <form class="add-to-cart-form" method="POST" style="display: inline;">
                            <input type="hidden" name="product_id" value="<?php echo $related['product_id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="aa-add-card-btn">
                                <span class="fa fa-shopping-cart"></span>Thêm vào giỏ
                            </button>
                        </form>
                        <figcaption>
                          <h4 class="aa-product-title">
                            <a href="product-detail.php?id=<?php echo $related['product_id']; ?>">
                              <?php echo htmlspecialchars($related['product_name']); ?>
                            </a>
                          </h4>
                          <span class="aa-product-price"><?php echo number_format($related['price'], 0, ',', '.'); ?>đ</span>
                        </figcaption>
                      </figure>
                    </li>
                  <?php endforeach; ?>
                <?php endif; ?>
              </ul>
            </div>

            <?php else: ?>
                <div class="alert alert-warning">
                    Không tìm thấy sản phẩm
                </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section> 

  <section id="aa-subscribe">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="aa-subscribe-area">
            <h3>Subscribe our newsletter </h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ex, velit!</p>
            <form action="" class="aa-subscribe-form">
              <input type="email" name="" id="" placeholder="Enter your Email">
              <input type="submit" value="Subscribe">
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
  <footer id="aa-footer"> 
    <div class="aa-footer-top">
     <div class="container">
        <div class="row">
        <div class="col-md-12">
          <div class="aa-footer-top-area">
            <div class="row">
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <h3>Main Menu</h3>
                  <ul class="aa-footer-nav">
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Our Services</a></li>
                    <li><a href="#">Our Products</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <div class="aa-footer-widget">
                    <h3>Knowledge Base</h3>
                    <ul class="aa-footer-nav">
                      <li><a href="#">Delivery</a></li>
                      <li><a href="#">Returns</a></li>
                      <li><a href="#">Services</a></li>
                      <li><a href="#">Discount</a></li>
                      <li><a href="#">Special Offer</a></li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <div class="aa-footer-widget">
                    <h3>Useful Links</h3>
                    <ul class="aa-footer-nav">
                      <li><a href="#">Site Map</a></li>
                      <li><a href="#">Search</a></li>
                      <li><a href="#">Advanced Search</a></li>
                      <li><a href="#">Suppliers</a></li>
                      <li><a href="#">FAQ</a></li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <div class="aa-footer-widget">
                    <h3>Contact Us</h3>
                    <address>
                      <p> 25 Astor Pl, NY 10003, USA</p>
                      <p><span class="fa fa-phone"></span>+1 212-982-4589</p>
                      <p><span class="fa fa-envelope"></span>HVshop@gmail.com</p>
                    </address>
                    <div class="aa-footer-social">
                      <a href="#"><span class="fa fa-facebook"></span></a>
                      <a href="#"><span class="fa fa-twitter"></span></a>
                      <a href="#"><span class="fa fa-google-plus"></span></a>
                      <a href="#"><span class="fa fa-youtube"></span></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
     </div>
    </div>
    <div class="aa-footer-bottom">
      <div class="container">
        <div class="row">
        <div class="col-md-12">
          <div class="aa-footer-bottom-area">
            <p>Designed by <a href="http://www.markups.io/">MarkUps.io</a></p>
            <div class="aa-footer-payment">
              <span class="fa fa-cc-mastercard"></span>
              <span class="fa fa-cc-visa"></span>
              <span class="fa fa-paypal"></span>
              <span class="fa fa-cc-discover"></span>
            </div>
          </div>
        </div>
      </div>
      </div>
    </div>
  </footer>
  <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">                      
        <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4>Login or Register</h4>
          <form class="aa-login-form" action="">
            <label for="">Username or Email address<span>*</span></label>
            <input type="text" placeholder="Username or email">
            <label for="">Password<span>*</span></label>
            <input type="password" placeholder="Password">
            <button class="aa-browse-btn" type="submit">Login</button>
            <label for="rememberme" class="rememberme"><input type="checkbox" id="rememberme"> Remember me </label>
            <p class="aa-lost-password"><a href="#">Lost your password?</a></p>
            <div class="aa-register-now">
              Don't have an account?<a href="account.html">Register now!</a>
            </div>
          </form>
        </div>                        
      </div>
    </div>
  </div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="js/bootstrap.js"></script>  
  <script type="text/javascript" src="js/jquery.smartmenus.js"></script>
  <script type="text/javascript" src="js/jquery.smartmenus.bootstrap.js"></script>  
  <script src="js/sequence.js"></script>
  <script src="js/sequence-theme.modern-slide-in.js"></script>  
  <script type="text/javascript" src="js/jquery.simpleGallery.js"></script>
  <script type="text/javascript" src="js/jquery.simpleLens.js"></script>
  <script type="text/javascript" src="js/slick.js"></script>
  <script type="text/javascript" src="js/nouislider.js"></script>
  <script src="js/custom.js"></script> 

  

  <script>
  function decreaseQuantity() {
    var input = document.getElementById('quantity');
    var value = parseInt(input.value);
    if (value > 1) {
        input.value = value - 1;
    }
  }

  function increaseQuantity() {
    var input = document.getElementById('quantity');
    var value = parseInt(input.value);
    if (value < 10) {
        input.value = value + 1;
    }
  }
  </script>

  <script>
  $(document).ready(function() {
    // Hàm cập nhật giỏ hàng mini
    function updateMiniCart() {
        $.ajax({
            url: 'get_mini_cart.php',
            type: 'GET',
            success: function(response) {
                // Cập nhật nội dung giỏ hàng mini
                $('.aa-cartbox-summary').html(response);
                // Cập nhật số lượng hiển thị trên icon giỏ hàng
                $.get('get_cart_count.php', function(count) {
                    $('.aa-cart-notify').text(count);
                });
            }
        });
    }

    // Xử lý submit form thêm vào giỏ
    $('.aa-add-to-cart-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'add_to_cart.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Cập nhật giỏ hàng mini
                    updateMiniCart();
                    
                    // Hiển thị thông báo thành công
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
  .quantity-controls {
    display: flex;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: fit-content;
  }

  .quantity-btn {
    background: #f8f9fa;
    border: none;
    padding: 8px 12px;
    cursor: pointer;
  }

  .quantity-btn:hover {
    background: #e9ecef;
  }

  input[type="number"] {
    width: 50px;
    text-align: center;
    border: none;
    border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
    padding: 8px 0;
  }

  /* Ẩn mũi tên tăng giảm mặc định của input number */
  input[type="number"]::-webkit-inner-spin-button,
  input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }
  </style>

  <script>
  function updateQuantity(change) {
    const input = document.getElementById('quantity');
    let value = parseInt(input.value) + change;
    
    // Giới hạn giá trị từ 1 đến 99
    value = Math.max(1, Math.min(99, value));
    
    input.value = value;
  }

  // Xử lý khi người dùng nhập trực tiếp
  document.getElementById('quantity').addEventListener('change', function() {
    let value = parseInt(this.value);
    
    // Kiểm tra và giới hạn giá trị
    if (isNaN(value) || value < 1) {
        value = 1;
    } else if (value > 99) {
        value = 99;
    }
    
    this.value = value;
  });
  </script>

  </body>
</html>