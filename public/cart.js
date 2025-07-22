// Cart Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeCartPage();
});

function initializeCartPage() {
    initializeCartActions();
    initializeQuantityControls();
    initializePromoCode();
    updateOrderSummary();
    updateCartCount();
    updateWishlistCount();
}

// Cart Actions
function initializeCartActions() {
    const clearCartBtn = document.getElementById('clearCartBtn');
    const checkoutBtn = document.getElementById('checkoutBtn');
    const removeButtons = document.querySelectorAll('.removeItemBtn');
    const saveForLaterButtons = document.querySelectorAll('.saveForLaterBtn');

    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', clearCart);
    }

    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', proceedToCheckout);
    }

    removeButtons.forEach(btn => {
        btn.addEventListener('click', removeCartItem);
    });

    saveForLaterButtons.forEach(btn => {
        btn.addEventListener('click', saveForLater);
    });
}

function clearCart() {
    if (confirm('Are you sure you want to clear your entire cart? This action cannot be undone.')) {
        const cartItems = document.querySelectorAll('.cartItem');
        cartItems.forEach(item => {
            item.remove();
        });
        
        showEmptyCartState();
        updateOrderSummary();
        updateCartCount();
        showNotification('Cart cleared successfully', 'info');
    }
}

function removeCartItem(event) {
    const cartItem = event.target.closest('.cartItem');
    const itemTitle = cartItem.querySelector('.itemTitle').textContent;
    
    if (confirm(`Remove "${itemTitle}" from your cart?`)) {
        cartItem.style.transform = 'scale(0.8)';
        cartItem.style.opacity = '0';
        
        setTimeout(() => {
            cartItem.remove();
            updateOrderSummary();
            updateCartCount();
            updateCartHeader();
            checkEmptyCartState();
            showNotification('Item removed from cart', 'info');
        }, 300);
    }
}

function saveForLater(event) {
    const cartItem = event.target.closest('.cartItem');
    const itemTitle = cartItem.querySelector('.itemTitle').textContent;
    
    // Add to wishlist logic here
    updateWishlistCount();
    
    // Remove from cart
    cartItem.style.transform = 'scale(0.8)';
    cartItem.style.opacity = '0';
    
    setTimeout(() => {
        cartItem.remove();
        updateOrderSummary();
        updateCartCount();
        updateCartHeader();
        checkEmptyCartState();
        showNotification(`"${itemTitle}" saved to wishlist`, 'success');
    }, 300);
}

function proceedToCheckout() {
    const cartItems = document.querySelectorAll('.cartItem');
    
    if (cartItems.length === 0) {
        showNotification('Your cart is empty', 'error');
        return;
    }
    
    // Add loading state
    const checkoutBtn = document.getElementById('checkoutBtn');
    const originalText = checkoutBtn.innerHTML;
    checkoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    checkoutBtn.disabled = true;
    
    // Simulate checkout process
    setTimeout(() => {
        showNotification('Redirecting to secure checkout...', 'success');
        // In a real app, this would redirect to checkout page
        // window.location.href = 'checkout.html';
        
        // Reset button after demo
        setTimeout(() => {
            checkoutBtn.innerHTML = originalText;
            checkoutBtn.disabled = false;
        }, 2000);
    }, 1500);
}

// Quantity Controls
function initializeQuantityControls() {
    const minusButtons = document.querySelectorAll('.qtyBtn.minus');
    const plusButtons = document.querySelectorAll('.qtyBtn.plus');
    const qtyInputs = document.querySelectorAll('.qtyInput');

    minusButtons.forEach(btn => {
        btn.addEventListener('click', decreaseQuantity);
    });

    plusButtons.forEach(btn => {
        btn.addEventListener('click', increaseQuantity);
    });

    qtyInputs.forEach(input => {
        input.addEventListener('change', updateQuantity);
    });
}

function decreaseQuantity(event) {
    const qtyInput = event.target.parentElement.querySelector('.qtyInput');
    const currentValue = parseInt(qtyInput.value);
    
    if (currentValue > 1) {
        qtyInput.value = currentValue - 1;
        updateItemTotal(event.target.closest('.cartItem'));
        updateOrderSummary();
    }
}

function increaseQuantity(event) {
    const qtyInput = event.target.parentElement.querySelector('.qtyInput');
    const currentValue = parseInt(qtyInput.value);
    const maxValue = parseInt(qtyInput.max) || 10;
    
    if (currentValue < maxValue) {
        qtyInput.value = currentValue + 1;
        updateItemTotal(event.target.closest('.cartItem'));
        updateOrderSummary();
    } else {
        showNotification('Maximum quantity reached for this item', 'info');
    }
}

function updateQuantity(event) {
    const qtyInput = event.target;
    const minValue = parseInt(qtyInput.min) || 1;
    const maxValue = parseInt(qtyInput.max) || 10;
    let currentValue = parseInt(qtyInput.value);
    
    if (currentValue < minValue) {
        qtyInput.value = minValue;
    } else if (currentValue > maxValue) {
        qtyInput.value = maxValue;
        showNotification('Maximum quantity reached for this item', 'info');
    }
    
    updateItemTotal(event.target.closest('.cartItem'));
    updateOrderSummary();
}

function updateItemTotal(cartItem) {
    const price = parseInt(cartItem.dataset.price);
    const quantity = parseInt(cartItem.querySelector('.qtyInput').value);
    const total = price * quantity;
    
    const priceElement = cartItem.querySelector('.price');
    priceElement.textContent = `EGP ${total.toLocaleString()}`;
}

// Promo Code
function initializePromoCode() {
    const applyPromoBtn = document.getElementById('applyPromoBtn');
    const promoCodeInput = document.getElementById('promoCode');

    if (applyPromoBtn) {
        applyPromoBtn.addEventListener('click', applyPromoCode);
    }

    if (promoCodeInput) {
        promoCodeInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                applyPromoCode();
            }
        });
    }
}

function applyPromoCode() {
    const promoCodeInput = document.getElementById('promoCode');
    const promoCode = promoCodeInput.value.trim().toUpperCase();
    
    if (!promoCode) {
        showNotification('Please enter a promo code', 'error');
        return;
    }
    
    // Demo promo codes
    const validPromoCodes = {
        'SAVE10': { discount: 0.10, type: 'percentage' },
        'NEWUSER': { discount: 5000, type: 'fixed' },
        'ARTLOVER': { discount: 0.15, type: 'percentage' }
    };
    
    if (validPromoCodes[promoCode]) {
        const discount = validPromoCodes[promoCode];
        applyDiscount(discount);
        showNotification(`Promo code "${promoCode}" applied successfully!`, 'success');
        promoCodeInput.value = '';
    } else {
        showNotification('Invalid promo code', 'error');
    }
}

function applyDiscount(discount) {
    // Add discount line to order summary
    const summaryCard = document.querySelector('.summaryCard');
    const existingDiscount = summaryCard.querySelector('.discount-line');
    
    if (existingDiscount) {
        existingDiscount.remove();
    }
    
    const discountLine = document.createElement('div');
    discountLine.className = 'summaryLine discount-line';
    discountLine.style.color = '#22c55e';
    
    const subtotal = getCurrentSubtotal();
    let discountAmount;
    
    if (discount.type === 'percentage') {
        discountAmount = subtotal * discount.discount;
        discountLine.innerHTML = `
            <span>Discount (${(discount.discount * 100)}%):</span>
            <span>-EGP ${discountAmount.toLocaleString()}</span>
        `;
    } else {
        discountAmount = discount.discount;
        discountLine.innerHTML = `
            <span>Discount:</span>
            <span>-EGP ${discountAmount.toLocaleString()}</span>
        `;
    }
    
    // Insert before the divider
    const divider = summaryCard.querySelector('.summaryDivider');
    divider.parentNode.insertBefore(discountLine, divider);
    
    updateOrderSummary();
}

// Order Summary Updates
function updateOrderSummary() {
    const cartItems = document.querySelectorAll('.cartItem');
    let subtotal = 0;
    let itemCount = 0;
    
    cartItems.forEach(item => {
        const price = parseInt(item.dataset.price);
        const quantity = parseInt(item.querySelector('.qtyInput').value);
        subtotal += price * quantity;
        itemCount += quantity;
    });
    
    const shipping = subtotal > 100000 ? 0 : (subtotal > 0 ? 500 : 0); // Free shipping over EGP 100,000
    const insurance = Math.round(subtotal * 0.01); // 1% insurance
    const tax = Math.round(subtotal * 0.1); // 10% tax
    
    // Check for existing discount
    const discountElement = document.querySelector('.discount-line span:last-child');
    const discount = discountElement ? 
        parseInt(discountElement.textContent.replace(/[^\d]/g, '')) : 0;
    
    const total = subtotal + shipping + insurance + tax - discount;
    
    // Update display
    const subtotalElement = document.getElementById('subtotal');
    const shippingElement = document.getElementById('shipping');
    const insuranceElement = document.getElementById('insurance');
    const taxElement = document.getElementById('tax');
    const totalElement = document.getElementById('total');
    
    if (subtotalElement) {
        subtotalElement.textContent = `EGP ${subtotal.toLocaleString()}`;
    }
    
    if (shippingElement) {
        shippingElement.textContent = shipping === 0 ? 'Free' : `EGP ${shipping.toLocaleString()}`;
    }
    
    if (insuranceElement) {
        insuranceElement.textContent = `EGP ${insurance.toLocaleString()}`;
    }
    
    if (taxElement) {
        taxElement.textContent = `EGP ${tax.toLocaleString()}`;
    }
    
    if (totalElement) {
        totalElement.textContent = `EGP ${total.toLocaleString()}`;
    }
}

function getCurrentSubtotal() {
    const cartItems = document.querySelectorAll('.cartItem');
    let subtotal = 0;
    
    cartItems.forEach(item => {
        const price = parseInt(item.dataset.price);
        const quantity = parseInt(item.querySelector('.qtyInput').value);
        subtotal += price * quantity;
    });
    
    return subtotal;
}

// State Management
function updateCartHeader() {
    const cartItems = document.querySelectorAll('.cartItem');
    const cartHeader = document.querySelector('.cartHeader h2');
    
    if (cartHeader) {
        cartHeader.textContent = `Cart Items (${cartItems.length})`;
    }
}

function checkEmptyCartState() {
    const cartItems = document.querySelectorAll('.cartItem');
    
    if (cartItems.length === 0) {
        showEmptyCartState();
    }
}

function showEmptyCartState() {
    const cartLayout = document.querySelector('.cartLayout');
    const emptyCart = document.querySelector('.emptyCart');
    const continueShoppingSection = document.querySelector('.continueShoppingSection');
    
    if (cartLayout && emptyCart) {
        cartLayout.style.display = 'none';
        emptyCart.style.display = 'block';
        if (continueShoppingSection) {
            continueShoppingSection.style.display = 'none';
        }
    }
}

// Utility Functions
function updateCartCount() {
    const cartItems = document.querySelectorAll('.cartItem');
    let totalItems = 0;
    
    cartItems.forEach(item => {
        const quantity = parseInt(item.querySelector('.qtyInput').value);
        totalItems += quantity;
    });
    
    const cartCountElements = document.querySelectorAll('.cartCount, #cartCount, #burgerCartCount');
    
    cartCountElements.forEach(element => {
        if (element) {
            element.textContent = totalItems;
        }
    });
    
    // Store in localStorage
    localStorage.setItem('cartCount', totalItems.toString());
}

function updateWishlistCount() {
    // Get wishlist count from localStorage or API
    const wishlistCount = localStorage.getItem('wishlistCount') || '7';
    const wishlistCountElements = document.querySelectorAll('.wishlistCount, #wishlistCount, #burgerWishlistCount');
    
    wishlistCountElements.forEach(element => {
        if (element) {
            element.textContent = wishlistCount;
            element.style.display = wishlistCount > 0 ? 'inline' : 'none';
        }
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#22c55e' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after delay
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Export functions for external use
window.cartFunctions = {
    addToCart: function(itemData) {
        console.log('Adding to cart:', itemData);
    },
    removeFromCart: removeCartItem,
    clearCart: clearCart,
    updateOrderSummary: updateOrderSummary
};
