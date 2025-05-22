@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<div class="container">
    <h1 class="mb-4">My Wishlist</h1>
    
    @if(isset($wishlist) && $wishlist->wishlistItems->count() > 0)
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Saved Items ({{ $wishlist->wishlistItems->count() }})</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($wishlist->wishlistItems as $item)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="position-relative">
                                    <img src="https://via.placeholder.com/300x200?text={{ urlencode($item->product->name) }}" class="card-img-top" alt="{{ $item->product->name }}">
                                    <form action="{{ route('wishlist.remove', $item->wishlist_item_id) }}" method="POST" class="position-absolute top-0 end-0 m-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $item->product->name }}</h5>
                                    <p class="card-text text-truncate">{{ $item->product->description }}</p>
                                    <p class="card-text font-weight-bold">${{ number_format($item->product->price, 2) }}</p>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="{{ route('products.show', $item->product->product_id) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-eye me-1"></i> View Details
                                            </a>
                                            <form action="{{ route('wishlist.moveToCart', $item->wishlist_item_id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary" {{ $item->product->stock <= 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    <small class="text-muted">Added on {{ $item->added_date instanceof \DateTime ? $item->added_date->format('M d, Y') : date('M d, Y', strtotime($item->added_date)) }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body empty-state">
                <i class="fas fa-heart fa-4x text-muted mb-3"></i>
                <h3>Your Wishlist is Empty</h3>
                <p class="mb-4">Save items you like to your wishlist for future reference.</p>
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <a href="{{ route('products.index') }}" class="btn btn-primary w-100">
                            <i class="fas fa-shopping-bag me-2"></i> Browse Products
                        </a>
                    </div>
                </div>
                @auth
                    @if(isset(Auth::user()->customer->cart) && Auth::user()->customer->cart->cartItems->count() > 0)
                        <div class="mt-4 pt-4 border-top">
                            <p>You have {{ Auth::user()->customer->cart->cartItems->count() }} items in your cart</p>
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-success">
                                <i class="fas fa-shopping-cart me-2"></i> View Your Cart
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    @endif
</div>
@endsection
