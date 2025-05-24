@extends('layouts.app')

@section('content')
<!-- Full-width Hero Banner (outside container for full-width effect) -->
<div class="hero-banner position-relative mb-5">
    <div class="hero-overlay position-absolute w-100 h-100" style="background-color: rgba(0,0,0,0.3);"></div>
    <div class="container position-relative" style="z-index: 2;">
        <div class="row py-5">
            <div class="col-md-6 text-white py-5">
                <h1 class="display-4 fw-bold">Welcome to ClickToBuy</h1>
                <p class="lead my-4">Discover amazing products at unbeatable prices.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg px-4 me-md-2">Shop Now</a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-lg px-4">New Arrivals</a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Promotional Banners -->
    <div class="row mb-5">
        <div class="col-md-4 mb-3">
            <div class="promo-card bg-primary text-white p-4 rounded h-100">
                <h3>Free Shipping</h3>
                <p>On orders over $50</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="promo-card bg-success text-white p-4 rounded h-100">
                <h3>24/7 Support</h3>
                <p>Contact us anytime</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="promo-card bg-info text-white p-4 rounded h-100">
                <h3>Easy Returns</h3>
                <p>30-day money back guarantee</p>
            </div>
        </div>
    </div>

    <!-- Featured Categories with Visual Design -->
    <div class="category-section mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0">Shop by Category</h2>
            <a href="{{ route('products.index') }}" class="text-decoration-none">View All Categories</a>
        </div>
        
        <div class="row g-4">
            @foreach($categories as $category)
                <div class="col-md-3 col-sm-6 mb-3">
                    <a href="{{ route('products.index', ['category_id' => $category->category_id]) }}" class="text-decoration-none">
                        <div class="category-card position-relative rounded overflow-hidden shadow-sm h-100">
                            <div class="category-card-body bg-light p-4 d-flex flex-column h-100">
                                <div class="category-icon mb-3 text-{{ ['primary', 'success', 'info', 'warning', 'danger'][rand(0,4)] }}">
                                    <!-- Icon placeholder - you can replace with actual icons -->
                                    <i class="fas fa-tag fa-2x"></i>
                                </div>
                                <h4 class="category-title mb-3">{{ $category->name }}</h4>
                                
                                @if($category->subcategories->count() > 0)
                                    <div class="subcategories">
                                        <ul class="list-unstyled small">
                                            @foreach($category->subcategories->take(3) as $subcategory)
                                                <li class="mb-1">
                                                    <a href="{{ route('products.index', ['category_id' => $subcategory->category_id]) }}" class="text-muted">
                                                        {{ $subcategory->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                <div class="mt-auto">
                                    <span class="btn btn-sm btn-outline-secondary w-100">Browse {{ $category->name }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Featured Products Section with Enhanced Design -->
    <div class="featured-products-section mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0">Featured Products</h2>
            <a href="{{ route('products.index') }}" class="text-decoration-none">View All Featured</a>
        </div>
        
        <div class="row">        @foreach($featuredProducts as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="product-card card h-100 border-0 shadow-sm">
                    <div class="product-badge position-absolute" style="top: 10px; left: 10px;">
                        @if(isset($product->discount) && $product->discount > 0)
                            <span class="badge bg-danger">-{{ $product->discount }}%</span>
                        @elseif(isset($product->created_at) && $product->created_at > now()->subDays(7))
                            <span class="badge bg-success">New</span>
                        @endif
                    </div>
                        
                        <div class="card-img-wrapper position-relative" style="height: 200px;">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" class="card-img-top h-100" alt="{{ $product->name }}" style="object-fit: contain; padding: 15px;">
                            @else
                                <div class="no-image-placeholder h-100 bg-light d-flex align-items-center justify-content-center">
                                    <span class="text-muted">No image</span>
                                </div>
                            @endif
                            
                            <div class="product-actions position-absolute w-100 bottom-0 start-0 p-2 d-flex justify-content-center" style="opacity: 0; transition: all 0.3s;">
                                <form action="{{ route('wishlist.add') }}" method="POST" class="me-2">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Add to Wishlist">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </form>
                                
                                <a href="{{ route('products.show', $product->product_id) }}" class="btn btn-sm btn-outline-primary me-2" title="Quick View">
                                    <i class="far fa-eye"></i>
                                </a>
                                
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Add to Cart">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="product-ratings mb-2">
                                @php
                                    $rating = isset($product->reviews) && $product->reviews->count() > 0 ? $product->reviews->avg('rating') : 0;
                                    $fullStars = floor($rating);
                                    $halfStar = $rating - $fullStars >= 0.5;
                                @endphp
                                
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $fullStars)
                                        <i class="fas fa-star text-warning"></i>
                                    @elseif($i == $fullStars + 1 && $halfStar)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    @else
                                        <i class="far fa-star text-warning"></i>
                                    @endif
                                @endfor
                                
                                <span class="text-muted small ms-1">({{ isset($product->reviews) ? $product->reviews->count() : 0 }})</span>
                            </div>
                            
                            <h5 class="card-title mb-1 product-title">
                                <a href="{{ route('products.show', $product->product_id) }}" class="text-decoration-none text-dark">
                                    {{ \Illuminate\Support\Str::limit($product->name, 40) }}
                                </a>
                            </h5>
                            
                            <p class="card-text text-muted small mb-3" style="height: 40px; overflow: hidden;">
                                {{ \Illuminate\Support\Str::limit($product->description, 60) }}
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="product-price">
                                    <span class="fw-bold fs-5">${{ number_format($product->price, 2) }}</span>
                                    @if(isset($product->original_price) && $product->original_price > $product->price)
                                        <span class="text-muted text-decoration-line-through ms-2">${{ number_format($product->original_price, 2) }}</span>
                                    @endif
                                </div>
                                
                                <a href="{{ route('products.show', $product->product_id) }}" class="btn btn-sm btn-outline-primary">Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
