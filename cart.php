<?php 
session_start(); 
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
} 
//XOA
if (isset($_GET['remove'])) {
    $productIdToRemove = $_GET['remove'];
    if (isset($_SESSION['cart'][$productIdToRemove])) {
        unset($_SESSION['cart'][$productIdToRemove]);
    }
}

// CAP NHAT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    if (isset($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] = max(1, (int)$quantity);
            }
        }
    }
}

// Tinh tong gio hang
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <title>H&V Shop | Cart Page</title>
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
                      <img src="img/flag/english.jpg" alt="english flag">ENGLISH
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
                      <i class="fa fa-usd"></i>USD
                      <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                      <li><a href="#"><i class="fa fa-euro"></i>EURO</a></li>
                      <li><a href="#"><i class="fa fa-jpy"></i>YEN</a></li>
                      <li><a href="#"><i class="fa fa-dong"></i>ĐỒNG</a></li>
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
               <!-- cart box -->
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
                <form action="">
                  <input type="text" name="" id="" placeholder="Tìm kiếm sản phẩm...">
                  <button type="submit"><span class="fa fa-search"></span></button>
                </form>
              </div>             
            </div>
          </div>
        </div>
      </div>
    </div> 
  </header> 
  <!-- Menu section -->
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
                  <li><a href="#">Áo thun</a></li>
                  <li><a href="#">Áo sơ mi</a></li>
                  <li><a href="#">Xem thêm... <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                    </ul>
                  </li>
                </ul>
              </li>
              <li><a href="#">Thời trang nữ <span class="caret"></span></a>
                <ul class="dropdown-menu">                
                  <li><a href="#">Áo</a></li>
                  <li><a href="#">Đầm</a></li>
                  <li><a href="#">Xem thêm... <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                    </ul>
                  </li>
                </ul>
              </li>
              <li><a href="#">Thời trang trẻ em <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Bé trai</a></li>
                  <li><a href="#">Bé gái</a></li>
                </ul>
              </li>
              <li><a href="#">Thời trang thể thao</a></li>
              <li><a href="contact.php">Liên hệ</a></li>
              <li><a href="#">Trang <span class="caret"></span></a>
                <ul class="dropdown-menu">                
                  <li><a href="product.php">Cửa hàng</a></li>
                  <li><a href="product-detail.php">Chi tiết sản phẩm</a></li>
                  <li><a href="cart.php">Giỏ hàng</a></li>
                  <li><a href="checkout.php">Thanh toán</a></li>
                </ul>
              </li>
            </ul>
          </div> 
        </div>
      </div>       
    </div>
  </section>

<section id="cart-view">
   <div class="container">
     <div class="row">
       <div class="col-md-12">
         <div class="cart-view-area">
           <div class="cart-view-table">
             <form action="" method="post">
               <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Xóa</th>
                        <th>Hình ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Tổng tiền</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (!empty($_SESSION['cart'])): ?>
                          <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                              <tr>
                                  <td>
                                      <a class="remove" href="?remove=<?php echo $product_id; ?>">
                                          <fa class="fa fa-close"></fa>
                                      </a>
                                  </td>
                                  <td>
                                      <a href="product-detail.php?id=<?php echo $product_id; ?>">
                                          <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                               alt="<?php echo htmlspecialchars($item['name']); ?>"
                                               style="width: 100px;">
                                      </a>
                                  </td>
                                  <td>
                                      <a class="aa-cart-title" href="product-detail.php?id=<?php echo $product_id; ?>">
                                          <?php echo htmlspecialchars($item['name']); ?>
                                      </a>
                                  </td>
                                  <td><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                                  <td>
                                      <input class="aa-cart-quantity" type="number" 
                                             name="quantity[<?php echo $product_id; ?>]" 
                                             value="<?php echo $item['quantity']; ?>" min="1">
                                  </td>
                                  <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ</td>
                              </tr>
                          <?php endforeach; ?>
                      <?php else: ?>
                          <tr>
                              <td colspan="6" class="text-center">Giỏ hàng trống</td>
                          </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
                <button type="submit" name="update_cart" class="aa-cart-view-btn">Cập nhật giỏ hàng</button>
             </form>
             <!-- Cart Total -->
             <div class="cart-view-total">
               <h4>Tổng giỏ hàng</h4>
               <table class="aa-totals-table">
                 <tbody>
                   <tr>
                     <th>Tổng cộng</th>
                     <td><?php echo number_format($total, 0, ',', '.'); ?>đ</td>
                   </tr>
                 </tbody>
               </table>
               <a href="checkout.php" class="aa-cart-view-btn">Đặt hàng</a>
             </div>
           </div>
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

  <!-- footer -->  
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
                      <p><span class="fa fa-envelope"></span>dailyshop@gmail.com</p>
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

  </body>
</html>