@extends('layouts.app')

@section('title', $product->name)

@section('styles')
<style>
    .product-image {
        max-height: 400px;
        object-fit: contain;
    }
    
    .product-details {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
    }
    
    .review-card {
        margin-bottom: 20px;
    }
    
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .related-product {
        transition: transform 0.3s;
    }
    
    .related-product:hover {
        transform: translateY(-5px);
    }
    
    .rating {
        color: #ffc107;
    }
</style>
@endsection

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row mb-5">
        <!-- Product Image -->
        <div class="col-md-5 mb-4">
            <div class="card">
                <div class="card-body">
                    <img src="https://via.placeholder.com/500x500?text={{ urlencode($product->name) }}" class="img-fluid" alt="{{ $product->name }}">
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-md-7">
            <h1 class="mb-3">{{ $product->name }}</h1>
            
            <div class="mb-3">
                @if($product->reviews->count() > 0)
                    <div class="d-flex align-items-center mb-2">
                        @php
                            $avgRating = $product->reviews->avg('rating');
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $avgRating)
                                <i class="fas fa-star text-warning"></i>
                            @elseif($i - 0.5 <= $avgRating)
                                <i class="fas fa-star-half-alt text-warning"></i>
                            @else
                                <i class="far fa-star text-warning"></i>
                            @endif
                        @endfor
                        <span class="ms-2">{{ number_format($avgRating, 1) }} ({{ $product->reviews->count() }} reviews)</span>
                    </div>
                @endif
            </div>
            
            <p class="lead mb-4">${{ number_format($product->price, 2) }}</p>
            
            <div class="mb-4">
                <h5>Description</h5>
                <p>{{ $product->description }}</p>
            </div>
            
            <div class="mb-4">
                <h5>Availability</h5>
                @if($product->stock > 0)
                    <p class="text-success">In Stock ({{ $product->stock }} available)</p>
                @else
                    <p class="text-danger">Out of Stock</p>
                @endif
            </div>
            
            <div class="d-flex">
                <form action="{{ route('cart.add') }}" method="POST" class="me-2">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                    <div class="input-group mb-3">
                        <input type="number" name="quantity" class="form-control" value="1" min="1" max="{{ $product->stock }}">
                        <button class="btn btn-primary" type="submit" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                            <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                        </button>
                    </div>
                </form>
                
                <form action="{{ route('wishlist.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="fas fa-heart me-1"></i> Add to Wishlist
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Product Reviews -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Reviews ({{ $product->reviews->count() }})</h3>
                    @auth
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal">
                            Write a Review
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">Login to Write a Review</a>
                    @endauth
                </div>
                <div class="card-body">
                    @if($reviews->count() > 0)
                        @foreach($reviews as $review)
                            <div class="mb-4 pb-4 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h5 class="mb-0">{{ $review->customer->user->user_name }}</h5>
                                        <div class="text-muted small">{{ $review->review_date->format('F j, Y') }}</div>
                                    </div>
                                    <div>
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <p>{{ $review->comment }}</p>
                            </div>
                        @endforeach
                    @else
                        <p>No reviews yet. Be the first to review this product!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4">Related Products</h3>
            </div>
            @foreach($relatedProducts as $relatedProduct)
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/300x300?text={{ urlencode($relatedProduct->name) }}" class="card-img-top" alt="{{ $relatedProduct->name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $relatedProduct->name }}</h5>
                            <p class="card-text text-truncate">{{ $relatedProduct->description }}</p>
                            <p class="card-text font-weight-bold">${{ number_format($relatedProduct->price, 2) }}</p>
                            <a href="{{ route('products.show', $relatedProduct->product_id) }}" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Review Modal -->
@auth
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('products.reviews.store', $product->product_id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="reviewModalLabel">Write a Review</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rating" class="form-label">Rating</label>
                            <select class="form-select" id="rating" name="rating" required>
                                <option value="">Select a rating</option>
                                <option value="5">5 - Excellent</option>
                                <option value="4">4 - Very Good</option>
                                <option value="3">3 - Good</option>
                                <option value="2">2 - Fair</option>
                                <option value="1">1 - Poor</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">Review</label>
                            <textarea class="form-control" id="comment" name="comment" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endauth
@endsection
