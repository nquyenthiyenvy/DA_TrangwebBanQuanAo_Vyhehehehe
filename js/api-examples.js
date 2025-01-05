// Ví dụ với WooCommerce REST API
async function getWooCommerceProducts() {
    const consumerKey = 'your_consumer_key';
    const consumerSecret = 'your_consumer_secret';
    const baseUrl = 'https://your-store.com/wp-json/wc/v3';
    
    try {
        const response = await fetch(`${baseUrl}/products`, {
            headers: {
                'Authorization': 'Basic ' + btoa(consumerKey + ':' + consumerSecret)
            }
        });
        return await response.json();
    } catch (error) {
        console.error('Lỗi WooCommerce API:', error);
        return [];
    }
}

// Ví dụ với Shopify Storefront API
async function getShopifyProducts() {
    const shopifyAccessToken = 'your_storefront_access_token';
    const shopDomain = 'your-store.myshopify.com';
    
    const query = `
        {
            products(first: 10) {
                edges {
                    node {
                        id
                        title
                        description
                        images(first: 1) {
                            edges {
                                node {
                                    url
                                }
                            }
                        }
                        priceRange {
                            minVariantPrice {
                                amount
                            }
                        }
                    }
                }
            }
        }
    `;

    try {
        const response = await fetch(`https://${shopDomain}/api/2024-01/graphql.json`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Shopify-Storefront-Access-Token': shopifyAccessToken
            },
            body: JSON.stringify({ query })
        });
        return await response.json();
    } catch (error) {
        console.error('Lỗi Shopify API:', error);
        return [];
    }
} 