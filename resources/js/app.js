import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

document.addEventListener('click', (event) => {
    const galleryThumbnail = event.target.closest('[data-product-image-src]');
    if (galleryThumbnail) {
        const mainImage = document.getElementById('product-main-image');
        if (mainImage) {
            mainImage.src = galleryThumbnail.dataset.productImageSrc;
            mainImage.alt = galleryThumbnail.dataset.productImageAlt;
            document.querySelectorAll('[data-product-image-src]').forEach((thumbnail) => {
                thumbnail.classList.remove('ring-1', 'ring-[var(--ink)]');
            });
            galleryThumbnail.classList.add('ring-1', 'ring-[var(--ink)]');
        }
        return;
    }

    const button = event.target.closest('[data-add-cart]');
    if (!button) return;

    event.preventDefault();
    const variantId = button.dataset.addCart;

    const cartItemsUrl = window.AppRoutes?.cartItems || '/api/v1/cart/items';
    const loginUrl = window.AppRoutes?.login || '/login';

    axios.post(cartItemsUrl, {
        product_variant_id: Number(variantId),
        quantity: 1,
    }).then((response) => {
        const cartCount = response.data?.cart_items?.reduce((total, item) => total + Number(item.quantity), 0);
        if (Number.isFinite(cartCount)) {
            document.querySelectorAll('[data-cart-count]').forEach((count) => {
                count.textContent = cartCount;
            });
        }

        if (button.hasAttribute('data-product-add')) {
            window.location.reload();
            return;
        }

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

document.addEventListener('click', async (event) => {
    const quantityButton = event.target.closest('[data-cart-quantity]');
    const removeButton = event.target.closest('[data-cart-remove]');
    if (!quantityButton && !removeButton) return;

    const cartItem = event.target.closest('[data-cart-item]');
    if (!cartItem) return;

    const itemUrl = `${window.AppRoutes?.cartItemBase || '/api/v1/cart/items'}/${cartItem.dataset.cartItem}`;
    const quantityOutput = cartItem.querySelector('[data-cart-quantity-value]');

    try {
        cartItem.querySelectorAll('button').forEach((button) => {
            button.disabled = true;
        });

        if (removeButton) {
            await axios.delete(itemUrl);
        } else {
            const currentQuantity = Number(quantityOutput.textContent);
            const quantity = currentQuantity + (quantityButton.dataset.cartQuantity === 'increase' ? 1 : -1);
            await axios.patch(itemUrl, { quantity });
        }

        window.location.reload();
    } catch (error) {
        if (error?.response?.status === 401 || error?.response?.status === 419) {
            window.location.href = window.AppRoutes?.login || '/login';
            return;
        }

        cartItem.querySelectorAll('button').forEach((button) => {
            button.disabled = false;
        });
        alert('Unable to update your cart right now. Please try again.');
    }
});
