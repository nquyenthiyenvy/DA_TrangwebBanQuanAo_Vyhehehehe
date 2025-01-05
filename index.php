<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <title>H&V Shop | Home</title>
    
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
                      <i class="fa fa-dong"></i>DONG
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

    <!--MENU-->
  </section>
  <section id="aa-slider">
    <div class="aa-slider-area">
      <div id="sequence" class="seq">
        <div class="seq-screen">
          <ul class="seq-canvas">
            <li>
              <div class="seq-model">
              <img data-seq src="img/slider/3.jpg" alt="Men slide img" />
              </div>
            </li>
            <li>
              <div class="seq-model">
                <img data-seq src="img/slider/5.jpg" alt="Wristwatch slide img" />
              </div>
            </li>
            <li>
              <div class="seq-model">
                <img data-seq src="img/slider/2.jpg" alt="Women Jeans slide img"/>
              </div>
            <li>
              <div class="seq-model">
                <img data-seq src="img/slider/11.jpg" alt="Shoes slide img"/>
              </div>
            </li>
             <li>
              <div class="seq-model">
                <img data-seq src="img/slider/1.jpg" alt="Male Female slide img"/>
              </div>
            </li>                   
          </ul>
        </div>
      </div>
    </div>
  </section>


  <section id="aa-product-category">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
          <div class="aa-product-catg-content">       
    </div>
  </section>

<!-- Popular category section -->
<section id="aa-popular-category">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="row">
          <div class="aa-popular-category-area">
            <ul class="nav nav-tabs aa-products-tab">
              <?php if(isset($_GET['query'])): ?>
                <li class="active"><a href="#search">Kết quả tìm kiếm</a></li>
              <?php else: ?>
                <li class="active"><a href="#popular" data-toggle="tab">Sản phẩm nổi bật</a></li>
                <li><a href="#featured" data-toggle="tab">Mới nhất</a></li>
                <li><a href="#latest" data-toggle="tab">Bán chạy</a></li>
              <?php endif; ?>
            </ul>
            <div class="tab-content">
              <?php if(isset($_GET['query'])): ?>
                <div class="tab-pane fade in active" id="search">
                  <h3>Kết quả tìm kiếm cho: '<?php echo htmlspecialchars($_GET['query']); ?>'</h3>
                  <ul class="aa-product-catg">
                    <?php
                    try {
                        $pdo = new PDO("mysql:host=localhost;dbname=db_web", "root", "");
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $searchQuery = $_GET['query'];
                        $stmt = $pdo->prepare("SELECT * FROM products WHERE product_name LIKE :searchQuery");
                        $stmt->bindValue(':searchQuery', '%' . $searchQuery . '%', PDO::PARAM_STR);
                        $stmt->execute();
                        
                        if ($stmt->rowCount() > 0) {
                            echo "<p>Tìm thấy " . $stmt->rowCount() . " sản phẩm</p>";
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <li>
                                  <figure>
                                    <a class="aa-product-img" href="product-detail.php?id=<?php echo $row['product_id']; ?>">
                                      <img src="<?php echo htmlspecialchars($row['image_url']); ?>" 
                                           alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                                    </a>
                                    
                                    <form class="add-to-cart-form" method="POST" style="display: inline;">
                                        <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="aa-add-card-btn">
                                            <span class="fa fa-shopping-cart"></span>Thêm vào giỏ
                                        </button>
                                    </form>
                                    <figcaption>
                                        <h4 class="aa-product-title">
                                            <a href="product-detail.php?id=<?php echo $row['product_id']; ?>">
                                                <?php echo htmlspecialchars($row['product_name']); ?>
                                            </a>
                                        </h4>
                                        <span class="aa-product-price">
                                            <?php echo number_format($row['price'], 0, ',', '.'); ?>đ
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
                                <?php
                            }
                        } else {
                            echo "<p>Không tìm thấy sản phẩm nào phù hợp.</p>";
                        }
                        $pdo = null;
                    } catch(PDOException $e) {
                        echo "Lỗi: " . $e->getMessage();
                    }
                    ?>
                  </ul>
                  </div>


              <?php else: ?>
                <div class="tab-pane fade in active" id="popular">
                  <ul class="aa-product-catg">
                    <?php
                    try {
                        $pdo = new PDO("mysql:host=localhost;dbname=db_web", "root", "");
                        $stmt = $pdo->query("SELECT * FROM products ORDER BY RAND() LIMIT 12");
                        while($row = $stmt->fetch()) {
                            ?>
                            <li>
                              <figure>
                                <a class="aa-product-img" href="product-detail.php?id=<?php echo $row['product_id']; ?>">
                                  <img src="<?php echo htmlspecialchars($row['image_url']); ?>" 
                                       alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                                </a>
                                <form class="add-to-cart-form" method="POST" style="display: inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="aa-add-card-btn">
                                        <span class="fa fa-shopping-cart"></span>Thêm vào giỏ
                                    </button>
                                </form>
                                <figcaption>
                                    <h4 class="aa-product-title">
                                        <a href="product-detail.php?id=<?php echo $row['product_id']; ?>">
                                            <?php echo htmlspecialchars($row['product_name']); ?>
                                        </a>
                                    </h4>
                                    <span class="aa-product-price">
                                        <?php echo number_format($row['price'], 0, ',', '.'); ?>đ
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
                            <?php
                        }
                    } catch(PDOException $e) {
                        echo "Lỗi: " . $e->getMessage();
                    }
                    ?>
                  </ul>
                </div>
                <!-- Tab sản phẩm mới -->
                <div class="tab-pane fade" id="featured">
                  <ul class="aa-product-catg">
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT * FROM products LIMIT 8 OFFSET 8");
                        while($row = $stmt->fetch()) {
                            ?>
                            <li>
                              <figure>
                                <a class="aa-product-img" href="product-detail.php?id=<?php echo $row['product_id']; ?>">
                                  <img src="<?php echo htmlspecialchars($row['image_url']); ?>" 
                                       alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                                </a>
                                <form class="add-to-cart-form" method="POST" style="display: inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="aa-add-card-btn">
                                        <span class="fa fa-shopping-cart"></span>Thêm vào giỏ
                                    </button>
                                </form>
                                <figcaption>
                                    <h4 class="aa-product-title">
                                        <a href="product-detail.php?id=<?php echo $row['product_id']; ?>">
                                            <?php echo htmlspecialchars($row['product_name']); ?>
                                        </a>
                                    </h4>
                                    <span class="aa-product-price">
                                        <?php echo number_format($row['price'], 0, ',', '.'); ?>đ
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
                            <?php
                        }
                    } catch(PDOException $e) {
                        echo "Lỗi: " . $e->getMessage();
                    }
                    ?>
                  </ul>
                </div>
                <!-- Tab sản phẩm bán chạy -->
                <div class="tab-pane fade" id="latest">
                  <ul class="aa-product-catg">
                    <?php
                    try {
                        $pdo = new PDO("mysql:host=localhost;dbname=db_web", "root", "");
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $stmt = $pdo->query("SELECT * FROM products ORDER BY RAND() LIMIT 8");
                        while($row = $stmt->fetch()) {
                            ?>
                            <li>
                              <figure>
                                <a class="aa-product-img" href="product-detail.php?id=<?php echo $row['product_id']; ?>">
                                  <img src="<?php echo htmlspecialchars($row['image_url']); ?>" 
                                       alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                                </a>
                                <form class="add-to-cart-form" method="POST" style="display: inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="aa-add-card-btn">
                                        <span class="fa fa-shopping-cart"></span>Thêm vào giỏ
                                    </button>
                                </form>
                                <figcaption>
                                    <h4 class="aa-product-title">
                                        <a href="product-detail.php?id=<?php echo $row['product_id']; ?>">
                                            <?php echo htmlspecialchars($row['product_name']); ?>
                                        </a>
                                    </h4>
                                    <span class="aa-product-price">
                                        <?php echo number_format($row['price'], 0, ',', '.'); ?>đ
                                    </span>
                                </figcaption>
                              </figure>
                              <div class="aa-product-hvr-content">
                                <a href="#" data-toggle="tooltip" data-placement="top" title="Yêu th��ch">
                                  <span class="fa fa-heart-o"></span>
                                </a>
                                <a href="#" data-toggle="tooltip" data-placement="top" title="So sánh">
                                  <span class="fa fa-exchange"></span>
                                </a>
                              </div>
                              <span class="aa-badge aa-sale">GIẢM GIÁ!</span>
                            </li>
                            <?php
                        }
                    } catch(PDOException $e) {
                        echo "Lỗi: " . $e->getMessage();
                    }
                    ?>
                  </ul>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Browse all product button -->
  <section id="aa-product-category">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 text-center">
          <div class="aa-product-catg-content">
            <a href="product.php" class="aa-browse-btn">Xem tất cả sản phẩm <span class="fa fa-long-arrow-right"></span></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Support section -->
  <section id="aa-support">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="aa-support-area">
            <!-- single support -->
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="aa-support-single">
                <span class="fa fa-truck"></span>
                <h4>FREE SHIPPING</h4>
                <P>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quam, nobis.</P>
              </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="aa-support-single">
                <span class="fa fa-clock-o"></span>
                <h4>30 DAYS MONEY BACK</h4>
                <P>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quam, nobis.</P>
              </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="aa-support-single">
                <span class="fa fa-phone"></span>
                <h4>SUPPORT 24/7</h4>
                <P>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quam, nobis.</P>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Latest Blog -->
  <section id="aa-latest-blog">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="aa-latest-blog-area">
                    <h2>TIN TỨC MỚI NHẤT</h2>
                    <div class="row">
                        <div class="col-md-4 col-sm-4">
                            <div class="aa-latest-blog-single">
                                <figure class="aa-blog-img">
                                    <a href="#"><img src="img/xuhuongmuahe2024.png" alt="Xu hướng thời trang mùa hè 2024"></a>
                                    <figcaption class="aa-blog-img-caption">
                                        <span href="#"><i class="fa fa-clock-o"></i>20/03/2024</span>
                                    </figcaption>                          
                                </figure>
                                <div class="aa-blog-info">
                                    <h3 class="aa-blog-title"><a href="https://levents.asia/blogs/xu-huong-thoi-trang/xu-huong-thoi-trang-xuan-he-2024">Xu hướng thời trang mùa hè 2024</a></h3>
                                    <p>Khám phá những xu hướng thời trang hot nhất mùa hè năm nay...</p>
                                    <a href="https://levents.asia/blogs/xu-huong-thoi-trang/xu-huong-thoi-trang-xuan-he-2024" class="aa-read-mor-btn">Xem thêm <span class="fa fa-long-arrow-right"></span></a>
                                </div>
                            </div>
                        </div>
                         <div class="col-md-4 col-sm-4">
                            <div class="aa-latest-blog-single">
                                <figure class="aa-blog-img">
                                    <a href="#"><img src="img/chanvaynuvuadepvuasang.png" alt="Chân váy xòe phối với áo gì giúp nàng vừa đẹp vừa sang?"></a>
                                    <figcaption class="aa-blog-img-caption">
                                        <span href="#"><i class="fa fa-clock-o"></i>20/03/2024</span>
                                    </figcaption>                          
                                </figure>
                                <div class="aa-blog-info">
                                    <h3 class="aa-blog-title"><a href="https://theciu.vn/blog/bo-tui-5-loi-phoi-do-da-phong-cach-cung-chan-vay-midi">Chân váy xòe phối với áo gì giúp nàng vừa đẹp vừa sang?</a></h3>
                                    <p>Khám phá những cách phối đồ nữ vừa đẹp vừa sang...</p>
                                    <a href="https://theciu.vn/blog/bo-tui-5-loi-phoi-do-da-phong-cach-cung-chan-vay-midi" class="aa-read-mor-btn">Xem thêm <span class="fa fa-long-arrow-right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="aa-latest-blog-single">
                                <figure class="aa-blog-img">
                                    <a href="#"><img src="img/sweaternam.png" alt="Cách phối đồ nam với sweater"></a>
                                    <figcaption class="aa-blog-img-caption">
                                        <span href="#"><i class="fa fa-clock-o"></i>18/03/2024</span>
                                    </figcaption>                          
                                </figure>
                                <div class="aa-blog-info">
                                    <h3 class="aa-blog-title"><a href="https://routine.vn/tin-thoi-trang/cach-phoi-do-voi-ao-cardigan-nam">Cách phối đồ nam với sweater</a></h3>
                                    <p>Những gợi ý phối đồ công sở nam thanh lịch và chuyên nghiệp...</p>
                                    <a href="https://routine.vn/tin-thoi-trang/cach-phoi-do-voi-ao-cardigan-nam" class="aa-read-mor-btn">Xem thêm <span class="fa fa-long-arrow-right"></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    
        </div>
    </div>
</section>

  <!-- Subscribe section -->
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
                  <h3>Menu Chính</h3>
                  <ul class="aa-footer-nav">
                    <li><a href="#">Trang chủ</a></li>
                    <li><a href="#">Dịch vụ</a></li>
                    <li><a href="#">Sản phẩm</a></li>
                    <li><a href="#">Về chúng tôi</a></li>
                    <li><a href="#">Liên hệ</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <h3>Kiến thức</h3>
                  <ul class="aa-footer-nav">
                    <li><a href="#">Vận chuyển</a></li>
                    <li><a href="#">Hoàn trả</a></li>
                    <li><a href="#">Chính sách bảo hành</a></li>
                    <li><a href="#">Thanh toán</a></li>
                    <li><a href="#">Giảm giá</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <h3>Hữu ích</h3>
                  <ul class="aa-footer-nav">
                    <li><a href="#">Bản đ��� site</a></li>
                    <li><a href="#">Tìm kiếm</a></li>
                    <li><a href="#">Tìm kiếm nâng cao</a></li>
                    <li><a href="#">Nhà cung cấp</a></li>
                    <li><a href="#">Câu hỏi thường gặp</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <h3>Liên hệ</h3>
                  <address>
                    <p>Quận 9, TP.HCM</p>
                    <p><span class="fa fa-phone"></span>+84 123-456-789</p>
                    <p><span class="fa fa-envelope"></span>info@hvshop.com</p>
                  </address>
                  <div class="aa-footer-social">
                    <a href="#"><span class="fa fa-facebook"></span></a>
                    <a href="#"><span class="fa fa-twitter"></span></a>
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
    <div class="aa-footer-bottom">
      <div class="container">
        <div class="row">
        <div class="col-md-12">
          <div class="aa-footer-bottom-area">
            <p>Bản quyền &copy; 2024 H&V Shop</p>
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

<!-- Chat Icon -->
<div class="chat-icon" onclick="toggleChat()">
    <i class="fa fa-comments"></i>
</div>

<!-- Chat Box -->
<div class="admin-chat-box" id="adminChatBox">
    <div class="chat-header">
        <h5><i class="fa fa-comments"></i> Chat với H&V Shop</h5>
        <button class="btn-minimize" onclick="toggleChat()">
            <i class="fa fa-times"></i>
        </button>
    </div>
    <div class="chat-body" id="chatBody">
        <div class="chat-messages" id="chatMessages">
            <div class="message system">
                Xin chào! H&V Shop có thể giúp gì cho bạn?
            </div>
        </div>
        <div class="chat-input">
            <input type="text" id="messageInput" placeholder="Nhập tin nhắn...">
            <button class="btn-send" onclick="sendMessage()">
                <i class="fa fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<script>
let lastMessageId = 0;
const autoResponses = {
    'hi': 'Xin chào! H&V Shop có thể giúp gì cho bạn?',
    'hello': 'Xin chào! H&V Shop có thể giúp gì cho bạn?',
    'chào': 'Xin chào! H&V Shop có thể giúp gì cho bạn?',
    'giá': 'Bạn có thể xem giá sản phẩm trực tiếp trên website hoặc liên hệ hotline 0344-207-275 để được tư vấn chi tiết.',
    'size': 'H&V Shop có đầy đủ các size từ S đến XXL. Bạn có thể tham khảo bảng size trong mục hướng dẫn chọn size.',
    'ship': 'H&V Shop free ship cho đơn hàng từ 500k, thời gian giao hàng từ 2-3 ngày.',
    'giao hàng': 'H&V Shop free ship cho đơn hàng từ 500k, thời gian giao hàng từ 2-3 ngày.',
    'thanh toán': 'H&V Shop hỗ trợ thanh toán COD (nhận hàng mới trả tiền) hoặc chuyển khoản qua ngân hàng.',
    'đổi trả': 'H&V Shop cho phép đổi trả trong vòng 7 ngày nếu sản phẩm còn nguyên tem mác.',
    'sale': 'H&V Shop thường xuyên có chương trình khuyến mãi, bạn có thể theo dõi fanpage của shop để cập nhật thông tin mới nhất.',
    'khuyến mãi': 'H&V Shop thường xuyên có chương trình khuyến mãi, bạn có thể theo dõi fanpage của shop để cập nhật thông tin mới nhất.'
};

function getAutoResponse(message) {
    message = message.toLowerCase();
    for (let key in autoResponses) {
        if (message.includes(key)) {
            return autoResponses[key];
        }
    }
    return 'Cảm ơn bạn đã liên hệ. Nhân viên của H&V Shop sẽ phản hồi sớm nhất có thể!';
}

function toggleChat() {
    const chatBox = document.getElementById('adminChatBox');
    chatBox.classList.toggle('show');
}

function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    if (message) {
        const messagesDiv = document.getElementById('chatMessages');
        messagesDiv.innerHTML += `
            <div class="message outgoing">
                <div class="message-content">${message}</div>
            </div>
        `;
        input.value = '';
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
        
        setTimeout(() => {
            const autoResponse = getAutoResponse(message);
            messagesDiv.innerHTML += `
                <div class="message incoming">
                    <div class="message-content">${autoResponse}</div>
                </div>
            `;
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }, 1000);

        <?php if(isset($_SESSION['account_id'])): ?>
        $.ajax({
            url: 'send_message.php',
            type: 'POST',
            data: { message: message },
            success: function(response) {
                const data = JSON.parse(response);
                if (!data.success && data.message === 'Vui lòng đăng nhập để gửi tin nhắn') {
                    window.location.href = 'dangnhap.php';
                }
            }
        });
        <?php endif; ?>
    }
}

document.getElementById('messageInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
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