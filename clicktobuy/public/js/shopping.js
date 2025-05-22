// Cart and Wishlist functionality
document.addEventListener('DOMContentLoaded', function() {
    // Flash messages auto-hide
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert.alert-success, .alert.alert-info');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Quantity incrementer/decrementer for cart items
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        const decrementBtn = input.previousElementSibling;
        const incrementBtn = input.nextElementSibling;

        if (decrementBtn && decrementBtn.classList.contains('decrement-btn')) {
            decrementBtn.addEventListener('click', function() {
                if (input.value > 1) {
                    input.value = parseInt(input.value) - 1;
                    if (input.form) {
                        input.form.submit();
                    }
                }
            });
        }

        if (incrementBtn && incrementBtn.classList.contains('increment-btn')) {
            incrementBtn.addEventListener('click', function() {
                const max = input.getAttribute('max');
                if (!max || parseInt(input.value) < parseInt(max)) {
                    input.value = parseInt(input.value) + 1;
                    if (input.form) {
                        input.form.submit();
                    }
                }
            });
        }
    });

    // Animation for adding to cart/wishlist
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            button.classList.add('btn-flash');
            setTimeout(() => {
                button.classList.remove('btn-flash');
            }, 500);
        });
    });

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
