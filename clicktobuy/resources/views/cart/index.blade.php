@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container">
    <h1 class="mb-4">Your Shopping Cart</h1>
    
    @if(isset($cart) && $cart->cartItems->count() > 0)
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Cart Items ({{ $cart->cartItems->count() }})</h5>
                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to empty your cart?')">
                                    <i class="fas fa-trash-alt me-1"></i> Empty Cart
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Product</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart->cartItems as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="https://via.placeholder.com/80x80?text={{ urlencode($item->product->name) }}" class="img-thumbnail me-3" style="width: 80px;" alt="{{ $item->product->name }}">
                                                    <div>
                                                        <h6 class="mb-0">
                                                            <a href="{{ route('products.show', $item->product->product_id) }}" class="text-decoration-none">
                                                                {{ $item->product->name }}
                                                            </a>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>${{ number_format($item->product->price, 2) }}</td>
                                            <td>
                                                <form action="{{ route('cart.update', $item->cart_item_id) }}" method="POST" class="d-flex align-items-center">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="form-control form-control-sm" style="width: 60px;">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary ms-2">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td>${{ number_format($item->product->price * $item->quantity, 2) }}</td>
                                            <td>
                                                <form action="{{ route('cart.remove', $item->cart_item_id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Continue Shopping
                    </a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $subtotal = $cart->cartItems->sum(function($item) {
                                return $item->product->price * $item->quantity;
                            });
                            $tax = $subtotal * 0.1; // 10% tax
                            $total = $subtotal + $tax;
                        @endphp
                        
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
                        
                        <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100">
                            <i class="fas fa-credit-card me-1"></i> Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                <h3>Your cart is empty</h3>
                <p class="mb-4">Looks like you haven't added any products to your cart yet.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-bag me-1"></i> Start Shopping
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
