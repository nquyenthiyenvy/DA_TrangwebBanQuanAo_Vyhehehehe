const API_CONFIG = {
    BASE_URL: 'https://api.example.com',
    ENDPOINTS: {
        PRODUCTS: '/products',
        PRODUCT_DETAIL: (id) => `/products/${id}`,
        CATEGORIES: '/categories',
    },
    API_KEY: 'your_api_key_here' // Nếu cần
}; 