import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-add-cart]');
    if (!button) return;

    event.preventDefault();
    const variantId = button.dataset.addCart;

    axios.post('/api/v1/cart/items', {
        product_variant_id: Number(variantId),
        quantity: 1,
    }).then(() => {
        button.textContent = 'Added';
        button.disabled = true;
        setTimeout(() => {
            button.textContent = 'Add to cart';
            button.disabled = false;
        }, 1200);
    }).catch(() => {
        window.location.href = '/login';
    });
});
