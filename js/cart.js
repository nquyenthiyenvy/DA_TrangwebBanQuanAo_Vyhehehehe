// Định dạng giá tiền
function formatPrice(price) {
    return price.toLocaleString('vi-VN') + 'đ';
}

// Quản lý giỏ hàng
const cart = {
    items: [],

    addItem(product, quantity = 1) {
        // Kiểm tra sản phẩm đã tồn tại trong giỏ hàng
        const existingItem = this.items.find(item => item.id === product.id);
    
        if (existingItem) {
            // Nếu sản phẩm đã có, tăng số lượng
            existingItem.quantity += quantity;
        } else {
            // Nếu chưa có, thêm một bản sao độc lập vào giỏ hàng
            this.items.push({ ...product, quantity });
        }
    
        this.saveCart();
        this.updateCartUI();
    },

    removeItem(productId) {
        // Lọc bỏ sản phẩm có ID tương ứng
        this.items = this.items.filter(item => item.id !== productId);
    
        this.saveCart();
        this.updateCartUI();
    },

    updateQuantity(productId, quantity) {
        const item = this.items.find(item => item.id === productId);
        if (item) {
            // Đảm bảo số lượng tối thiểu là 1
            item.quantity = Math.max(Number(quantity), 1);
            this.saveCart();
            this.updateCartUI();
        }
    },

    saveCart() {
        localStorage.setItem('cart', JSON.stringify(this.items));
    },

    loadCart() {
        const savedCart = localStorage.getItem('cart');
        if (savedCart) {
            this.items = JSON.parse(savedCart);
            this.updateCartUI();
        }
    },

    updateCartUI() {
        // Cập nhật thông báo giỏ hàng
        const cartNotify = document.querySelector('.aa-cart-notify');
        if (cartNotify) {
            const totalItems = this.items.reduce((sum, item) => sum + item.quantity, 0);
            cartNotify.textContent = totalItems;
        }

        // Cập nhật danh sách sản phẩm trong giỏ hàng
        const cartList = document.querySelector('.aa-cartbox-summary ul');
        if (cartList) {
            let html = '';
            let total = 0;

            this.items.forEach(item => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;

                html += `
                    <li>
                        <a class="aa-cartbox-img" href="#"><img src="${item.image}" alt="${item.name}"></a>
                        <div class="aa-cartbox-info">
                            <h4><a href="#">${item.name}</a></h4>
                            <p>${item.quantity} x ${formatPrice(item.price)}</p>
                        </div>
                        <a class="aa-remove-product" href="#" onclick="cart.removeItem(${item.id})">
                            <span class="fa fa-times"></span>
                        </a>
                    </li>
                `;
            });

            html += `
                <li>
                    <span class="aa-cartbox-total-title">Tổng cộng</span>
                    <span class="aa-cartbox-total-price">${formatPrice(total)}</span>
                </li>
            `;

            cartList.innerHTML = html;
        }

        // Cập nhật bảng trong trang giỏ hàng (nếu có)
        this.updateCartTableUI();
    },

    updateCartTableUI() {
        const cartTableBody = document.querySelector('tbody.cart-items');
        if (cartTableBody) {
            let html = '';
            let total = 0;

            this.items.forEach(item => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;

                html += `
                    <tr data-id="${item.id}">
                        <td><a class="remove" href="#" onclick="cart.removeItem(${item.id})"><span class="fa fa-times"></span></a></td>
                        <td><a class="aa-cart-title" href="#">${item.name}</a></td>
                        <td class="price" data-price="${item.price}">${formatPrice(item.price)}</td>
                        <td>
                            <input 
                                class="aa-cart-quantity" 
                                type="number" 
                                value="${item.quantity}" 
                                min="1" 
                                data-id="${item.id}" 
                                onchange="cart.handleQuantityChange(this)">
                        </td>
                        <td class="total">${formatPrice(itemTotal)}</td>
                    </tr>
                `;
            });

            cartTableBody.innerHTML = html;

            // Cập nhật tổng cộng
            const grandTotalElement = document.querySelector('.aa-cartbox-total-price');
            if (grandTotalElement) {
                grandTotalElement.textContent = formatPrice(total);
            }
        }
    },

    handleQuantityChange(inputElement) {
        const productId = Number(inputElement.dataset.id); // Lấy ID sản phẩm từ thuộc tính data-id
        const quantity = Number(inputElement.value); // Lấy số lượng từ input
        this.updateQuantity(productId, quantity); // Cập nhật giỏ hàng cho sản phẩm có ID tương ứng
    }
};

// Khởi tạo giỏ hàng khi trang tải xong
document.addEventListener('DOMContentLoaded', () => {
    cart.loadCart();
});
