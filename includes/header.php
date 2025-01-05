<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header id="aa-header">
    <!-- Thêm menu màu hồng -->
    <div class="top-menu">
        <div class="container">
            <ul>
                <li><a href="index.php">Trang chủ</a></li>
                <li><a href="product.php?category=1">Thời trang nam</a></li>
                <li><a href="product.php?category=2">Thời trang nữ</a></li>
                <li><a href="product.php?category=3">Thời trang trẻ em</a></li>
                <li><a href="product.php?category=4">Thời trang thể thao</a></li>
                <li><a href="contact.html">Liên hệ</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Trang <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="product.php">Cửa hàng</a></li>
                        <li><a href="cart.php">Giỏ hàng</a></li>
                        <li><a href="checkout.php">Thanh toán</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <!-- Phần header còn lại giữ nguyên -->
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
                                        <li><a href="#"><img src="img/flag/flag_vietnam.png" alt="">VIETNAM</a></li>
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
                                        <li><a href="#"><i class="fa -dong"></i>DONG</a></li>
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
                                    <?php if(isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'admin'): ?>
                                        <li><a href="admin/admin-dashboard.php"><i class="fa fa-dashboard"></i> Quản trị</a></li>
                                    <?php endif; ?>
                                    <li><a href="logout.php"><i class="fa fa-sign-out"></i> Đăng xuất</a></li>
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
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                Thời trang nam <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">                
                                <li><a href="product.php?category=1&type=ao-thun">Áo thun</a></li>
                                <li><a href="product.php?category=1&type=ao-somi">Áo sơ mi</a></li>
                                <li><a href="product.php?category=1&type=quan">Quần</a></li>
                                <li><a href="product.php?category=1">Xem tất cả</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                Thời trang nữ <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">                
                                <li><a href="product.php?category=2&type=ao">Áo</a></li>
                                <li><a href="product.php?category=2&type=dam">Đầm</a></li>
                                <li><a href="product.php?category=2&type=chan-vay">Chân váy</a></li>
                                <li><a href="product.php?category=2">Xem tất cả</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                Thời trang trẻ em <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="product.php?category=3&gender=boy">Bé trai</a></li>
                                <li><a href="product.php?category=3&gender=girl">Bé gái</a></li>
                                <li><a href="product.php?category=3">Xem tất cả</a></li>
                            </ul>
                        </li>
                        <li><a href="product.php?category=4">Thời trang thể thao</a></li>
                        <li><a href="contact.html">Liên hệ</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section> 