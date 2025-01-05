<?php
session_start();

$output = '<ul>';

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $total = 0;
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $total += $item['price'] * $item['quantity'];
        $output .= '<li>
            <a class="aa-cartbox-img" href="product-detail.php?id=' . $product_id . '">
                <img src="' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['name']) . '">
            </a>
            <div class="aa-cartbox-info">
                <h4>
                    <a href="product-detail.php?id=' . $product_id . '">' . htmlspecialchars($item['name']) . '</a>
                </h4>
                <p>' . $item['quantity'] . ' x ' . number_format($item['price'], 0, ',', '.') . 'đ</p>
            </div>
            <a class="aa-remove-product" href="remove_from_cart.php?id=' . $product_id . '">
                <span class="fa fa-times"></span>
            </a>
        </li>';
    }
    
    $output .= '<li>
        <span class="aa-cartbox-total-title">Tổng cộng</span>
        <span class="aa-cartbox-total-price">' . number_format($total, 0, ',', '.') . 'đ</span>
    </li>';
} else {
    $output .= '<li><p>Giỏ hàng trống</p></li>';
}

$output .= '</ul>';
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $output .= '<a class="aa-cartbox-checkout aa-primary-btn" href="checkout.php">Thanh toán</a>';
}

echo $output;
?> 