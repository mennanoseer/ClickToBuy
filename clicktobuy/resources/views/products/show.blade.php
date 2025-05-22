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
                        <span class="input-group-text decrement-btn">
                            <i class="fas fa-minus"></i>
                        </span>
                        <input type="number" name="quantity" class="form-control quantity-input" value="1" min="1" max="{{ $product->stock }}" style="text-align: center;">
                        <span class="input-group-text increment-btn">
                            <i class="fas fa-plus"></i>
                        </span>
                        <button class="btn btn-primary add-to-cart" type="submit" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                            <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                        </button>
                    </div>
                </form>
                
                @if($product->isInWishlist())
                    <form action="{{ route('wishlist.remove', $product->getWishlistItem()->wishlist_item_id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger add-to-wishlist">
                            <i class="fas fa-heart-broken me-1"></i> Remove from Wishlist
                        </button>
                    </form>
                @else
                    <form action="{{ route('wishlist.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                        <button type="submit" class="btn btn-outline-secondary add-to-wishlist">
                            <i class="fas fa-heart me-1"></i> Add to Wishlist
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Product Reviews -->
    <div class="row mb-5">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Reviews ({{ $reviews->count() }})</h3>
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
                                        <h5 class="mb-0">{{ $review->customer && $review->customer->user ? $review->customer->user->user_name : 'Anonymous User' }}</h5>
                                        <div class="text-muted small">{{ $review->review_date instanceof \DateTime ? $review->review_date->format('F j, Y') : date('F j, Y', strtotime($review->review_date)) }}</div>
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
                            <select class="form-select @error('rating') is-invalid @enderror" id="rating" name="rating" required>
                                <option value="">Select a rating</option>
                                <option value="5" {{ old('rating') == 5 ? 'selected' : '' }}>5 - Excellent</option>
                                <option value="4" {{ old('rating') == 4 ? 'selected' : '' }}>4 - Very Good</option>
                                <option value="3" {{ old('rating') == 3 ? 'selected' : '' }}>3 - Good</option>
                                <option value="2" {{ old('rating') == 2 ? 'selected' : '' }}>2 - Fair</option>
                                <option value="1" {{ old('rating') == 1 ? 'selected' : '' }}>1 - Poor</option>
                            </select>
                            @error('rating')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">Review</label>
                            <textarea class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment" rows="4" required>{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum 10 characters. Be honest and helpful in your review.</div>
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

@push('scripts')
<script>
    // Initialize review form validation
    document.addEventListener('DOMContentLoaded', function() {
        const reviewForm = document.querySelector('#reviewModal form');
        if (reviewForm) {
            reviewForm.addEventListener('submit', function(event) {
                const rating = document.getElementById('rating').value;
                const comment = document.getElementById('comment').value;
                let isValid = true;
                
                if (!rating) {
                    isValid = false;
                    document.getElementById('rating').classList.add('is-invalid');
                } else {
                    document.getElementById('rating').classList.remove('is-invalid');
                }
                
                if (!comment || comment.length < 10) {
                    isValid = false;
                    document.getElementById('comment').classList.add('is-invalid');
                } else {
                    document.getElementById('comment').classList.remove('is-invalid');
                }
                
                if (!isValid) {
                    event.preventDefault();
                } else {
                    // Add loading state to prevent multiple submissions
                    const submitButton = reviewForm.querySelector('button[type="submit"]');
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
                    submitButton.disabled = true;
                }
            });
        }
        
        // Show review modal with validation errors if any
        @if($errors->any())
            const reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));
            reviewModal.show();
        @endif
    });
</script>
@endpush
@endsection
