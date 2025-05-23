@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Review #{{ $review->review_id }}</h1>
        <div>
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Reviews
            </a>
            <form action="{{ route('admin.reviews.destroy', $review->review_id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger shadow-sm ml-2" onclick="return confirm('Are you sure you want to delete this review?')">
                    <i class="fas fa-trash fa-sm text-white-50"></i> Delete Review
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Review Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Review Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Rating</h5>
                        <div class="h4 mb-0 text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                            <span class="text-gray-800 ml-2">{{ $review->rating }}/5</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="font-weight-bold">Review</h5>
                        <p class="mb-0">{{ $review->comment }}</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="font-weight-bold">Date Submitted</h5>
                        <p class="mb-0">{{ $review->created_at->format('F d, Y \a\t h:i A') }}</p>
                    </div>

                    @if($review->updated_at->gt($review->created_at))
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Last Updated</h5>
                        <p class="mb-0">{{ $review->updated_at->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="col-lg-4">
            <!-- Product Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Product Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($review->product && $review->product->image_url)
                            @if(filter_var($review->product->image_url, FILTER_VALIDATE_URL))
                                <img src="{{ $review->product->image_url }}" alt="{{ $review->product->name ?? 'Product Image' }}" class="img-fluid img-thumbnail" style="max-height: 150px;">
                            @else
                                <img src="{{ asset($review->product->image_url) }}" alt="{{ $review->product->name ?? 'Product Image' }}" class="img-fluid img-thumbnail" style="max-height: 150px;">
                            @endif
                        @else
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-image fa-3x text-secondary"></i>
                            </div>
                        @endif
                    </div>
                    
                    @if($review->product)
                        <h5 class="font-weight-bold">
                            <a href="{{ route('admin.products.edit', $review->product_id) }}">
                                {{ $review->product->name }}
                            </a>
                        </h5>
                        
                        <p class="small text-muted mb-2">
                            <strong>Price:</strong> ${{ number_format($review->product->price, 2) }}
                        </p>
                        
                        <p class="small text-muted mb-2">
                            <strong>Category:</strong> 
                            @if($review->product->category)
                                {{ $review->product->category->name }}
                            @else
                                Uncategorized
                            @endif
                        </p>
                        
                        <a href="{{ route('products.show', $review->product_id) }}" target="_blank" class="btn btn-sm btn-outline-primary btn-block">
                            <i class="fas fa-external-link-alt mr-1"></i> View Product Page
                        </a>
                    @else
                        <div class="alert alert-warning">
                            Product information not available. The product may have been deleted.
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Customer Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                </div>
                <div class="card-body">
                    <h5 class="font-weight-bold">
                        <a href="{{ route('admin.customers.show', $review->customer_id) }}">
                            {{ $review->customer->user->user_name }}
                        </a>
                    </h5>
                    
                    <p class="small text-muted mb-2">
                        <strong>Email:</strong> {{ $review->customer->user->email }}
                    </p>
                    
                    <p class="small text-muted mb-2">
                        <strong>Registered:</strong> {{ $review->customer->user->created_at->format('M d, Y') }}
                    </p>
                    
                    <p class="small text-muted mb-2">
                        <strong>Total Reviews:</strong> {{ $customerReviewCount }}
                    </p>
                    
                    <a href="{{ route('admin.customers.show', $review->customer_id) }}" class="btn btn-sm btn-outline-primary btn-block">
                        <i class="fas fa-user mr-1"></i> View Customer Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 