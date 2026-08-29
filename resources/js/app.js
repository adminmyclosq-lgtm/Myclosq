import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-add-cart]');
    if (!button) return;

    event.preventDefault();
    const variantId = button.dataset.addCart;

    const cartItemsUrl = window.AppRoutes?.cartItems || '/api/v1/cart/items';
    const loginUrl = window.AppRoutes?.login || '/login';

    axios.post(cartItemsUrl, {
        product_variant_id: Number(variantId),
        quantity: 1,
    }).then(() => {
        button.textContent = 'Added';
        button.disabled = true;
        setTimeout(() => {
            button.textContent = 'Add to cart';
            button.disabled = false;
        }, 1200);
    }).catch((error) => {
        if (error?.response?.status === 401 || error?.response?.status === 419) {
            window.location.href = loginUrl;
            return;
        }

        alert('Unable to add item to cart right now. Please try again.');
    });
});
