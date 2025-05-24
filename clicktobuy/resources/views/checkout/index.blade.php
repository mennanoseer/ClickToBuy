@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Checkout</h1>
    
    @if(isset($cart) && $cart->cartItems->count() > 0)
        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf
            
            <div class="row">
                <!-- Checkout Form -->
                <div class="col-md-8">
                    <!-- Shipping Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Shipping Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="shipping_address" class="form-label">Address</label>
                                    <input type="text" class="form-control @error('shipping_address') is-invalid @enderror" id="shipping_address" name="shipping_address" value="{{ old('shipping_address', auth()->user()->address) }}" required>
                                    @error('shipping_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="shipping_city" class="form-label">City</label>
                                    <input type="text" class="form-control @error('shipping_city') is-invalid @enderror" id="shipping_city" name="shipping_city" value="{{ old('shipping_city') }}" required>
                                    @error('shipping_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="shipping_state" class="form-label">State</label>
                                    <input type="text" class="form-control @error('shipping_state') is-invalid @enderror" id="shipping_state" name="shipping_state" value="{{ old('shipping_state') }}" required>
                                    @error('shipping_state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="shipping_country" class="form-label">Country</label>
                                    <input type="text" class="form-control @error('shipping_country') is-invalid @enderror" id="shipping_country" name="shipping_country" value="{{ old('shipping_country', 'United States') }}" required>
                                    @error('shipping_country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="shipping_zip_code" class="form-label">ZIP Code</label>
                                    <input type="text" class="form-control @error('shipping_zip_code') is-invalid @enderror" id="shipping_zip_code" name="shipping_zip_code" value="{{ old('shipping_zip_code') }}" required>
                                    @error('shipping_zip_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', auth()->user()->phone_number) }}" required>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Payment Method</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" checked>
                                    <label class="form-check-label" for="credit_card">
                                        Credit Card
                                    </label>
                                </div>
                                
                                <div id="credit_card_details" class="payment-details mt-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="card_number" class="form-label">Card Number</label>
                                            <input type="text" class="form-control @error('card_number') is-invalid @enderror" id="card_number" name="card_number" placeholder="XXXX XXXX XXXX XXXX" value="{{ old('card_number') }}">
                                            @error('card_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="card_holder" class="form-label">Cardholder Name</label>
                                            <input type="text" class="form-control @error('card_holder') is-invalid @enderror" id="card_holder" name="card_holder" value="{{ old('card_holder') }}">
                                            @error('card_holder')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="expiry_date" class="form-label">Expiry Date</label>
                                            <input type="text" class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date" name="expiry_date" placeholder="MM/YY" value="{{ old('expiry_date') }}">
                                            @error('expiry_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cvv" class="form-label">CVV</label>
                                            <input type="text" class="form-control @error('cvv') is-invalid @enderror" id="cvv" name="cvv" placeholder="XXX" value="{{ old('cvv') }}">
                                            @error('cvv')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                    <label class="form-check-label" for="paypal">
                                        PayPal
                                    </label>
                                </div>
                                
                                <div id="paypal_details" class="payment-details mt-3" style="display: none;">
                                    <div class="mb-3">
                                        <label for="paypal_email" class="form-label">PayPal Email</label>
                                        <input type="email" class="form-control @error('paypal_email') is-invalid @enderror" id="paypal_email" name="paypal_email" value="{{ old('paypal_email') }}">
                                        @error('paypal_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="transaction_id" class="form-label">Transaction ID</label>
                                        <input type="text" class="form-control @error('transaction_id') is-invalid @enderror" id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}">
                                        @error('transaction_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer">
                                    <label class="form-check-label" for="bank_transfer">
                                        Bank Transfer
                                    </label>
                                </div>
                                
                                <div id="bank_transfer_details" class="payment-details mt-3" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="bank_name" class="form-label">Bank Name</label>
                                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name') }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="account_number" class="form-label">Account Number</label>
                                            <input type="text" class="form-control" id="account_number" name="account_number" value="{{ old('account_number') }}">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="routing_number" class="form-label">Routing Number</label>
                                        <input type="text" class="form-control" id="routing_number" name="routing_number" value="{{ old('routing_number') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cart->cartItems as $item)
                                            <tr>
                                                <td>
                                                    {{ $item->product->name }} <span class="text-muted">× {{ $item->quantity }}</span>
                                                </td>
                                                <td class="text-end">${{ number_format($item->product->price * $item->quantity, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            @php
                                $subtotal = $cart->cartItems->sum(function($item) {
                                    return $item->product->price * $item->quantity;
                                });
                                $tax = $subtotal * 0.1; // 10% tax
                                $total = $subtotal + $tax;
                            @endphp
                            
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax (10%):</span>
                                <span>${{ number_format($tax, 2) }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong>${{ number_format($total, 2) }}</strong>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-lock me-1"></i> Place Order
                            </button>
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-arrow-left me-1"></i> Return to Cart
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                <h3>Your cart is empty</h3>
                <p class="mb-4">You need to add items to your cart before checking out.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-bag me-1"></i> Go Shopping
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Initialize payment details on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Hide all payment details first
        document.querySelectorAll('.payment-details').forEach(function(details) {
            details.style.display = 'none';
        });
        
        // Show the selected payment method's details
        const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (selectedPaymentMethod) {
            document.getElementById(selectedPaymentMethod.value + '_details').style.display = 'block';
        }
        
        // Toggle payment method details
        document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.payment-details').forEach(function(details) {
                    details.style.display = 'none';
                });
                
                if (this.checked) {
                    document.getElementById(this.value + '_details').style.display = 'block';
                }
            });
        });
    });
    
    // Form validation
    document.querySelector('form').addEventListener('submit', function(event) {
        const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        // Validate payment specific fields
        let isValid = true;
        let errorMessage = '';
        
        if (selectedPaymentMethod === 'credit_card') {
            const cardNumber = document.getElementById('card_number').value;
            const cardHolder = document.getElementById('card_holder').value;
            const expiryDate = document.getElementById('expiry_date').value;
            const cvv = document.getElementById('cvv').value;
            
            if (!cardNumber || !cardHolder || !expiryDate || !cvv) {
                isValid = false;
                errorMessage = 'Please fill in all credit card details';
            }
        } else if (selectedPaymentMethod === 'paypal') {
            const paypalEmail = document.getElementById('paypal_email').value;
            
            if (!paypalEmail) {
                isValid = false;
                errorMessage = 'Please enter your PayPal email';
            }
        } else if (selectedPaymentMethod === 'bank_transfer') {
            const bankName = document.getElementById('bank_name').value;
            const accountNumber = document.getElementById('account_number').value;
            const routingNumber = document.getElementById('routing_number').value;
            
            if (!bankName || !accountNumber || !routingNumber) {
                isValid = false;
                errorMessage = 'Please fill in all bank details';
            }
        }
        
        if (!isValid) {
            event.preventDefault();
            alert(errorMessage);
        }
    });
</script>
@endpush
@endsection