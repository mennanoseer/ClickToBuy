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
                                                    @if($item->product->image_url)
                                                        @if(filter_var($item->product->image_url, FILTER_VALIDATE_URL))
                                                            <img src="{{ $item->product->image_url }}" class="img-thumbnail me-3" style="width: 80px; height: 80px; object-fit: contain;" alt="{{ $item->product->name }}">
                                                        @else
                                                            <img src="{{ asset($item->product->image_url) }}" class="img-thumbnail me-3" style="width: 80px; height: 80px; object-fit: contain;" alt="{{ $item->product->name }}">
                                                        @endif
                                                    @else
                                                        <img src="https://via.placeholder.com/80x80?text={{ urlencode($item->product->name) }}" class="img-thumbnail me-3" style="width: 80px;" alt="{{ $item->product->name }}">
                                                    @endif
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
                                                <form action="{{ route('cart.update', $item->cart_item_id) }}" method="POST" class="quantity-control">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-outline-secondary decrement-btn" {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="form-control form-control-sm quantity-input" style="width: 60px;" readonly>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary increment-btn" {{ $item->quantity >= $item->product->stock ? 'disabled' : '' }}>
                                                        <i class="fas fa-plus"></i>
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
            <div class="card-body empty-state">
                <i class="fas fa-shopping-cart fa-4x mb-3 text-muted"></i>
                <h3>Your Cart is Empty</h3>
                <p class="mb-4">Looks like you haven't added any products to your cart yet.</p>
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <a href="{{ route('products.index') }}" class="btn btn-primary w-100">
                            <i class="fas fa-shopping-bag me-2"></i> Browse Products
                        </a>
                    </div>
                </div>
                @auth
                    @if(isset(Auth::user()->customer->wishlist) && Auth::user()->customer->wishlist->wishlistItems->count() > 0)
                        <div class="mt-4 pt-4 border-top">
                            <p>You have {{ Auth::user()->customer->wishlist->wishlistItems->count() }} items in your wishlist</p>
                            <a href="{{ route('wishlist.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-heart me-2"></i> View Your Wishlist
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    @endif
</div>
@endsection
