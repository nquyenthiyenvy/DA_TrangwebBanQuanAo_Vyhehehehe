<?php
session_start();
error_log("Debug Session Data: " . print_r($_SESSION, true));

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$isLoggedIn = isset($_SESSION['username']);
if ($isLoggedIn) {
    if (!isset($_SESSION['user_id'])) {
        error_log("User logged in but no user_id found in session");
        echo "Vui lòng đăng nhập lại để tiếp tục.";
        exit();
    } else {
        error_log("User ID from session: " . $_SESSION['user_id']);
    }
}

function updateCart($cartItems) {
    foreach ($cartItems as $productId => $quantity) {
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }
    }
}
function removeProduct($productId) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    if (isset($_POST['quantity'])) {
        updateCart($_POST['quantity']);
    }
}
if (isset($_GET['remove'])) {
    $productIdToRemove = $_GET['remove'];
    removeProduct($productIdToRemove);
}
$cart_items = $_SESSION['cart'];
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fullname'])) {
      if (!$isLoggedIn) {
        header('Location: dangnhap.php');
        exit();
    }

    // Kiểm tra các trường bắt buộc
    $required_fields = [
        'fullname' => 'Họ và tên',
        'phone' => 'Số điện thoại',
        'address' => 'Địa chỉ',
        'city' => 'Tỉnh/Thành phố',
        'district' => 'Quận/Huyện',
        'ward' => 'Phường/Xã',
        'payment_method' => 'Phương thức thanh toán'
    ];

    $errors = [];
    foreach ($required_fields as $field => $label) {
        if (empty($_POST[$field])) {
            $errors[] = "Vui lòng nhập " . $label;
        }
    } 
    if (!empty($_POST['phone']) && !preg_match('/^[0-9]{10}$/', $_POST['phone'])) {
        $errors[] = "Số điện thoại không hợp lệ";
    }
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    }
    if (!empty($errors)) {
        echo "<div class='alert alert-danger'>";
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
        echo "</div>";
    } else {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=db_web", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("SELECT account_id FROM account WHERE username = ?");
            $stmt->execute([$_SESSION['username']]);
            $user = $stmt->fetch();
            
            if (!$user) {
                throw new Exception("Không tìm thấy thông tin tài khoản.");
            }

            $shipping = [
                'fullname' => $_POST['fullname'],
                'phone' => $_POST['phone'],
                'email' => $_POST['email'],
                'address' => $_POST['address'],
                'city' => $_POST['city'],
                'district' => $_POST['district'],
                'ward' => $_POST['ward'],
                'note' => $_POST['note'],
                'payment_method' => $_POST['payment_method'],
                'total_amount' => $total
            ];
            $_SESSION['shipping'] = $shipping;
            if ($_POST['payment_method'] === 'vnpay') {
                header('Location: vnpay_php/vnpay_pay.php');
                exit();
            }
            $stmt = $pdo->prepare("INSERT INTO orders (
                user_id, fullname, phone, email, 
                address, city, district, ward, 
                note, total_amount, payment_method, 
                status,
                order_date
            ) VALUES (
                ?, ?, ?, ?, 
                ?, ?, ?, ?, 
                ?, ?, ?, 
                'pending',
                NOW()
            )");
            
            $stmt->execute([
                $user['account_id'],
                $shipping['fullname'],
                $shipping['phone'],
                $shipping['email'],
                $shipping['address'],
                $shipping['city'],
                $shipping['district'],
                $shipping['ward'],
                $shipping['note'],
                $shipping['total_amount'],
                $shipping['payment_method']
            ]);
            
            $order_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare("INSERT INTO orderdetails (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            
            foreach ($_SESSION['cart'] as $productId => $item) {
                $stmt->execute([
                    $order_id,
                    $productId,
                    $item['quantity'],
                    $item['price']
                ]);
            }
            $_SESSION['last_order'] = [
                'order_id' => $order_id,
                'fullname' => $shipping['fullname'],
                'phone' => $shipping['phone'],
                'email' => $shipping['email'],
                'address' => $shipping['address'],
                'city' => $shipping['city'],
                'district' => $shipping['district'],
                'ward' => $shipping['ward'],
                'note' => $shipping['note'],
                'total' => $shipping['total_amount'],
                'payment_method' => $shipping['payment_method']
            ];

            // Xóa giỏ hàng
            unset($_SESSION['cart']);
            
            header('Location: order_success.php');
            exit();
            
        } catch(Exception $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <title>H&V Shop | Checkout Page</title>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <style>
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }

    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }

    .alert-danger p {
        margin: 5px 0;
    }

    .required {
        color: red;
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
                  <p>H&V<strong>Shop</strong> <span>Your Shopping Partner</span></p>
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
    </div>
  </section> 

 <section id="cart-view">
   <div class="container">
     <div class="row">
       <div class="col-md-12">
         <div class="cart-view-area">
           <div class="cart-view-table">
             <form action="" method="POST">
               <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th></th>
                        <th>Hình ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Tổng tiền</th>
                        <th>Hành động</th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cart_items)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Giỏ hàng trống</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cart_items as $productId => $item): ?>
                                <tr>
                                    <td>
                                        <a class="remove" href="?remove=<?php echo $productId; ?>">
                                            <fa class="fa fa-close"></fa>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="product-detail.php?id=<?php echo $productId; ?>">
                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                 style="width: 100px; height: 100px; object-fit: cover;">
                                        </a>
                                    </td>
                                    <td>
                                        <a class="aa-cart-title" href="product-detail.php?id=<?php echo $productId; ?>">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                                    <td>
                                        <input 
                                            class="aa-cart-quantity" 
                                            type="number" 
                                            name="quantity[<?php echo $productId; ?>]" 
                                            value="<?php echo $item['quantity']; ?>" 
                                            min="1">
                                    </td>
                                    <td>
                                        <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ
                                    </td>
                                    <td>
                                        <a class="aa-remove-product" href="?remove=<?php echo $productId; ?>">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                  </table>
                </div>
                <button type="submit" name="update_cart" class="aa-cart-view-btn">Cập nhật giỏ hàng</button>
             </form>
             <div class="cart-view-total">
               <h4>Tổng giá hàng</h4>
               <table class="aa-totals-table">
                 <tbody>
                   <tr>
                     <th>Tổng cộng</th>
                     <td><?php echo number_format($total, 0, ',', '.'); ?>đ</td>
                   </tr>
                 </tbody>
               </table>
             </div>
             <form id="shipping-form" method="POST" action="">
               <div class="shipping-info">
                 <h4>Thông tin giao hàng</h4>
                 <div class="row">
                   <div class="col-md-6">
                     <div class="form-group">
                       <label for="fullname">Họ và tên <span class="required">*</span></label>
                       <input type="text" id="fullname" name="fullname" class="form-control" required>
                     </div>
                     <div class="form-group">
                       <label for="phone">Số điện thoại <span class="required">*</span></label>
                       <input type="tel" id="phone" name="phone" class="form-control" required>
                     </div>
                     <div class="form-group">
                       <label for="email">Email</label>
                       <input type="email" id="email" name="email" class="form-control">
                     </div>
                   </div>
                   <div class="col-md-6">
                     <div class="form-group">
                       <label for="address">Địa chỉ <span class="required">*</span></label>
                       <input type="text" id="address" name="address" class="form-control" required>
                     </div>
                     <div class="form-group">
                       <label for="city">Tỉnh/Thành phố <span class="required">*</span></label>
                       <input type="text" id="city" name="city" class="form-control" required>
                     </div>
                     <div class="form-group">
                       <label for="district">Quận/Huyện <span class="required">*</span></label>
                       <input type="text" id="district" name="district" class="form-control" required>
                     </div>
                     <div class="form-group">
                       <label for="ward">Phường/Xã <span class="required">*</span></label>
                       <input type="text" id="ward" name="ward" class="form-control" required>
                     </div>
                   </div>
                 </div>
                 <div class="form-group">
                   <label for="note">Ghi chú</label>
                   <textarea id="note" name="note" class="form-control" rows="3"></textarea>
                 </div>
               </div>

               <div class="aa-payment-method">                    
                 <label for="cashdelivery">
                   <input type="radio" id="cashdelivery" name="payment_method" value="cod"> Thanh toán khi nhận hàng
                 </label>
                 <label for="paypal">
                   <input type="radio" id="paypal" name="payment_method" value="vnpay"> Thanh toán qua VNPAY 
                 </label>
                 <div id="payment-error" class="error-message" style="display: none; color: #dc3545; margin-top: 10px;"></div>
                 
                 <?php if($isLoggedIn): ?>
                   <button type="submit" class="aa-browse-btn" onclick="return validateForm()">Đặt hàng</button>
                 <?php else: ?>
                   <button type="button" onclick="redirectToLogin()" class="aa-browse-btn">Vui lòng đăng nhập để đặt hàng</button>
                 <?php endif; ?>
               </div>
             </form>
           </div>
         </div>
       </div>
     </div>
   </div>
 </section>
 <script>
function redirectToLogin() {
    window.location.href = 'dangnhap.php';
}

function validateForm() { 
    const fullname = document.getElementById('fullname').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const address = document.getElementById('address').value.trim();
    const city = document.getElementById('city').value.trim();
    const district = document.getElementById('district').value.trim();
    const ward = document.getElementById('ward').value.trim();
    const paymentMethods = document.getElementsByName('payment_method');
    const email = document.getElementById('email').value.trim(); 

    document.getElementById('payment-error').style.display = 'none';
    
    if (!fullname) {
        alert('Vui lòng nhập họ và tên');
        return false;
    }
    if (!phone) {
        alert('Vui lòng nhập số điện thoại');
        return false;
    }
    if (!address) {
        alert('Vui lòng nhập địa chỉ');
        return false;
    }
    if (!city) {
        alert('Vui lòng nhập tỉnh/thành phố');
        return false;
    }
    if (!district) {
        alert('Vui lòng nhập quận/huyện');
        return false;
    }
    if (!ward) {
        alert('Vui lòng nhập phường/xã');
        return false;
    } 

    const phoneRegex = /^[0-9]{10}$/;
    if (!phoneRegex.test(phone)) {
        alert('Số điện thoại không hợp lệ');
        return false;
    }
    if (email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Email không hợp lệ');
            return false;
        }
    } 
    //PTTToan
    let paymentSelected = false;
    for (let i = 0; i < paymentMethods.length; i++) {
        if (paymentMethods[i].checked) {
            paymentSelected = true;
            break;
        }
    }
    
    if (!paymentSelected) {
        document.getElementById('payment-error').textContent = 'Vui lòng chọn phương thức thanh toán';
        document.getElementById('payment-error').style.display = 'block';
        return false;
    }

    return true;
}
</script>

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
    
  </body>
</html>