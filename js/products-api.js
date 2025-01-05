// Hàm lấy danh sách sản phẩm
async function getProducts() {
    try {
        // Thay thế URL bằng API endpoint thực tế của bạn
        const response = await fetch('https://api.example.com/products');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Lỗi khi lấy dữ liệu sản phẩm:', error);
        return [];
    }
}

// Hàm lấy chi tiết một sản phẩm
async function getProductDetail(productId) {
    try {
        const response = await fetch(`https://api.example.com/products/${productId}`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Lỗi khi lấy chi tiết sản phẩm:', error);
        return null;
    }
}

// Hàm hiển thị sản phẩm lên trang
function displayProducts(products) {
    const productContainer = document.querySelector('.aa-product-catg');
    if (!productContainer) return;

    let html = '';
    products.forEach(product => {
        html += `
            <li>
                <figure>
                    <a class="aa-product-img" href="product-detail.html?id=${product.id}">
                        <img src="${product.image}" alt="${product.name}">
                    </a>
                    <a class="aa-add-card-btn" href="#" onclick="addToCart(${product.id})">
                        <span class="fa fa-shopping-cart"></span>Thêm vào giỏ
                    </a>
                    <figcaption>
                        <h4 class="aa-product-title">
                            <a href="product-detail.html?id=${product.id}">${product.name}</a>
                        </h4>
                        <span class="aa-product-price">${formatPrice(product.price)}đ</span>
                        ${product.oldPrice ? `<span class="aa-product-price"><del>${formatPrice(product.oldPrice)}đ</del></span>` : ''}
                    </figcaption>
                </figure>
                <div class="aa-product-hvr-content">
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Thêm vào yêu thích" onclick="addToWishlist(${product.id})">
                        <span class="fa fa-heart-o"></span>
                    </a>
                </div>
                ${product.isOnSale ? '<span class="aa-badge aa-sale" href="#">GIẢM GIÁ!</span>' : ''}
            </li>
        `;
    });
    
    productContainer.innerHTML = html;
}

// Hàm định dạng giá tiền
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price);
}

// Khởi tạo trang
async function initializePage() {
    // Kiểm tra nếu đang ở trang chi tiết sản phẩm
    if (window.location.pathname.includes('product-detail.html')) {
        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('id');
        if (productId) {
            const product = await getProductDetail(productId);
            if (product) {
                displayProductDetail(product);
            }
        }
    } else {
        // Trang danh sách sản phẩm
        const products = await getProducts();
        displayProducts(products);
    }
}

// Hàm hiển thị chi tiết sản phẩm
function displayProductDetail(product) {
    // Cập nhật thông tin sản phẩm trong trang chi tiết
    document.querySelector('.aa-product-view-content h3').textContent = product.name;
    document.querySelector('.aa-product-view-price').textContent = `${formatPrice(product.price)}đ`;
    document.querySelector('.aa-product-avilability span').textContent = 
        product.inStock ? 'Còn hàng' : 'Hết hàng';
    document.querySelector('#description p').textContent = product.description;
    
    // Cập nhật hình ảnh sản phẩm
    const mainImage = document.querySelector('.simpleLens-big-image');
    if (mainImage) {
        mainImage.src = product.image;
        mainImage.parentElement.setAttribute('data-lens-image', product.largeImage);
    }
}

// Khởi chạy khi trang đã tải xong
document.addEventListener('DOMContentLoaded', initializePage); 