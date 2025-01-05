const store = {
    state: {
        products: {
            'polo-nam': {
                id: 'polo-nam',
                name: 'Áo Polo Nam Pique Cotton USA',
                price: 250000,
                oldPrice: 300000,
                images: ['img/QuanAo/polo.jpg', 'img/QuanAo/polo-2.jpg'],
                description: 'Chất liệu: 97% Cotton USA + 3% Spandex<br>- Co giãn 4 chiều',
                sizes: ['S', 'M', 'L', 'XL'],
                colors: ['Đen', 'Trắng', 'Navy']
            },
            'dam-nu': {
                id: 'dam-nu',
                name: 'Đầm Nữ Dự Tiệc',
                price: 450000,
                images: ['img/QuanAo/dam1.jpg', 'img/QuanAo/dam1-2.jpg'],
                description: 'Chất liệu cao cấp<br>- Kiểu dáng thời trang',
                sizes: ['S', 'M', 'L'],
                colors: ['Đen', 'Đỏ']
            }
            // Thêm các sản phẩm khác
        }
    },
    getProduct(id) {
        return this.state.products[id];
    },
    getAllProducts() {
        return Object.values(this.state.products);
    }
}; 