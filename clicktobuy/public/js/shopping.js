// Cart and Wishlist functionality
document.addEventListener('DOMContentLoaded', function() {
    // Set up CSRF token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        window.csrfToken = csrfToken.getAttribute('content');
    }

    // Flash messages auto-hide
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert.alert-success, .alert.alert-info');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // AJAX Add to Cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const form = this.closest('form');
            const productId = form.querySelector('input[name="product_id"]').value;
            const quantity = form.querySelector('input[name="quantity"]')?.value || 1;
            
            // Add loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Adding...';
            this.disabled = true;
            
            fetch('/cart/add-ajax', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: parseInt(quantity)
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart badge
                    updateCartBadge(data.cart_count);
                    
                    // Show success message
                    showNotification(data.message, 'success');
                    
                    // Flash animation
                    this.classList.add('btn-flash');
                    setTimeout(() => {
                        this.classList.remove('btn-flash');
                    }, 500);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while adding to cart', 'error');
            })
            .finally(() => {
                // Restore button state
                this.innerHTML = originalText;
                this.disabled = false;
            });
        });
    });

    // AJAX Quantity Update functionality for cart page
    const quantityControls = document.querySelectorAll('.quantity-control');
    quantityControls.forEach(control => {
        const decrementBtn = control.querySelector('.decrement-btn');
        const incrementBtn = control.querySelector('.increment-btn');
        const quantityInput = control.querySelector('.quantity-input');
        const cartItemId = control.getAttribute('data-item-id');
        const productStock = parseInt(control.getAttribute('data-product-stock'));

        if (decrementBtn) {
            decrementBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const currentQuantity = parseInt(quantityInput.value);
                if (currentQuantity > 1) {
                    updateCartQuantity(cartItemId, currentQuantity - 1, quantityInput, control);
                }
            });
        }

        if (incrementBtn) {
            incrementBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const currentQuantity = parseInt(quantityInput.value);
                if (currentQuantity < productStock) {
                    updateCartQuantity(cartItemId, currentQuantity + 1, quantityInput, control);
                }
            });
        }
    });

    // AJAX Remove from Cart functionality
    const removeButtons = document.querySelectorAll('.remove-item-btn');
    removeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to remove this item from your cart?')) {
                return;
            }
            
            const cartItemId = this.getAttribute('data-item-id');
            const row = this.closest('tr');
            
            // Add loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled = true;
            
            fetch(`/cart/${cartItemId}/ajax`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the row with animation
                    row.classList.add('removing');
                    setTimeout(() => {
                        row.remove();
                        
                        // Update cart totals
                        updateCartTotals(data);
                        
                        // Update cart badge
                        updateCartBadge(data.cart_count);
                        
                        // Show success message
                        showNotification(data.message, 'success');
                        
                        // Check if cart is empty
                        checkEmptyCart();
                    }, 300);
                } else {
                    showNotification(data.message, 'error');
                    // Restore button state
                    this.innerHTML = originalText;
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while removing the item', 'error');
                // Restore button state
                this.innerHTML = originalText;
                this.disabled = false;
            });
        });
    });

    // Function to update cart quantity via AJAX
    function updateCartQuantity(cartItemId, newQuantity, quantityInput, control) {
        const row = control.closest('tr');
        const originalQuantity = quantityInput.value;
        
        // Update input optimistically
        quantityInput.value = newQuantity;
        
        fetch(`/cart/${cartItemId}/ajax`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                quantity: newQuantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update item total in the row
                const itemTotalCell = row.querySelector('.item-total');
                if (itemTotalCell) {
                    itemTotalCell.textContent = '$' + data.item_total;
                    // Add success highlight
                    itemTotalCell.classList.add('success-highlight');
                    setTimeout(() => {
                        itemTotalCell.classList.remove('success-highlight');
                    }, 2000);
                }
                
                // Update cart totals
                updateCartTotals(data);
                
                // Update cart badge
                updateCartBadge(data.cart_count);
                
                // Update button states
                updateQuantityButtonStates(control, newQuantity);
                
                // Show success message
                showNotification(data.message, 'success');
            } else {
                // Revert quantity on error
                quantityInput.value = originalQuantity;
                control.classList.add('error-shake');
                setTimeout(() => {
                    control.classList.remove('error-shake');
                }, 500);
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert quantity on error
            quantityInput.value = originalQuantity;
            showNotification('An error occurred while updating quantity', 'error');
        });
    }

    // Function to update cart badge
    function updateCartBadge(count) {
        const cartBadges = document.querySelectorAll('.cart-count .badge, .cart-badge');
        cartBadges.forEach(badge => {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline';
                // Add pulse animation
                badge.classList.add('updated');
                setTimeout(() => {
                    badge.classList.remove('updated');
                }, 500);
            } else {
                badge.style.display = 'none';
            }
        });
        
        // Also update cart items count in cart header
        const cartHeader = document.querySelector('.cart-items-count');
        if (cartHeader) {
            cartHeader.textContent = count;
        }
    }

    // Function to update cart totals
    function updateCartTotals(data) {
        const subtotalElement = document.querySelector('.cart-subtotal');
        const taxElement = document.querySelector('.cart-tax');
        const totalElement = document.querySelector('.cart-total');
        
        if (subtotalElement) subtotalElement.textContent = '$' + data.cart_subtotal;
        if (taxElement) taxElement.textContent = '$' + data.cart_tax;
        if (totalElement) totalElement.textContent = '$' + data.cart_total;
    }

    // Function to update quantity button states
    function updateQuantityButtonStates(control, quantity) {
        const decrementBtn = control.querySelector('.decrement-btn');
        const incrementBtn = control.querySelector('.increment-btn');
        const productStock = parseInt(control.getAttribute('data-product-stock'));
        
        if (decrementBtn) {
            decrementBtn.disabled = quantity <= 1;
        }
        
        if (incrementBtn) {
            incrementBtn.disabled = quantity >= productStock;
        }
    }

    // Function to check if cart is empty and show empty state
    function checkEmptyCart() {
        const cartTableBody = document.querySelector('.cart-table tbody');
        if (cartTableBody && cartTableBody.children.length === 0) {
            // Reload page to show empty cart state
            window.location.reload();
        }
    }

    // Function to show notifications
    function showNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show notification-toast`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 300px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        `;
        
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.classList.add('fade-out');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }
        }, 4000);
        
        // Remove on close button click
        const closeBtn = notification.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                notification.classList.add('fade-out');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            });
        }
    }

    // Animation for wishlist buttons
    const addToWishlistButtons = document.querySelectorAll('.add-to-wishlist');
    addToWishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            button.classList.add('heart-pulse');
            setTimeout(() => {
                button.classList.remove('heart-pulse');
            }, 500);
        });
    });
});
