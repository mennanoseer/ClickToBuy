@extends('layouts.app')

@section('title', 'Products')

@section('styles')
<style>
    .filter-sidebar {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
    }
    
    .product-grid .product-card {
        margin-bottom: 30px;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .product-grid .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .card-img-top {
        height: 200px;
        object-fit: cover;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar with filters -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filter Products</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.index') }}" method="GET">
                        <!-- Category Filter -->
                        <div class="mb-4">
                            <h6>Categories</h6>
                            <div class="list-group">
                                <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action {{ !request('category_id') ? 'active' : '' }}">
                                    All Categories
                                </a>
                                @foreach($categories as $category)
                                    <a href="{{ route('products.index', ['category_id' => $category->category_id]) }}" 
                                       class="list-group-item list-group-item-action {{ request('category_id') == $category->category_id ? 'active' : '' }}">
                                        {{ $category->name }}
                                    </a>
                                    @foreach($category->subcategories as $subcategory)
                                        <a href="{{ route('products.index', ['category_id' => $subcategory->category_id]) }}" 
                                           class="list-group-item list-group-item-action ps-4 {{ request('category_id') == $subcategory->category_id ? 'active' : '' }}">
                                            - {{ $subcategory->name }}
                                        </a>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Price Range Filter -->
                        <div class="mb-4">
                            <h6>Price Range</h6>
                            <div class="row g-2">
                                <div class="col">
                                    <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min" value="{{ request('min_price') }}">
                                </div>
                                <div class="col">
                                    <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max" value="{{ request('max_price') }}">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Search -->
                        <div class="mb-3">
                            <h6>Search</h6>
                            <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        
                        @if(request('category_id') || request('min_price') || request('max_price') || request('search'))
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">Clear Filters</a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Product Listings -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>{{ request('search') ? 'Search Results' : 'All Products' }}</h2>
                
                <div class="d-flex align-items-center">
                    <span class="me-2">Sort by:</span>
                    <select class="form-select form-select-sm" id="sort-select" style="width: auto;">
                        <option value="created_at-desc" {{ (request('sort_by') == 'created_at' && request('sort_order') == 'desc') ? 'selected' : '' }}>Newest</option>
                        <option value="price-asc" {{ (request('sort_by') == 'price' && request('sort_order') == 'asc') ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price-desc" {{ (request('sort_by') == 'price' && request('sort_order') == 'desc') ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name-asc" {{ (request('sort_by') == 'name' && request('sort_order') == 'asc') ? 'selected' : '' }}>Name: A to Z</option>
                    </select>
                </div>
            </div>
            
            @if($products->count() > 0)
                <div class="row">
                    @foreach($products as $product)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                @if($product->image_url)
                                    @if(filter_var($product->image_url, FILTER_VALIDATE_URL))
                                        <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: contain;">
                                    @else
                                        <img src="{{ asset($product->image_url) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: contain;">
                                    @endif
                                @else
                                    <img src="https://via.placeholder.com/300x200?text={{ urlencode($product->name) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: contain;">
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    <p class="card-text text-truncate">{{ $product->description }}</p>
                                    <p class="card-text font-weight-bold">${{ number_format($product->price, 2) }}</p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <a href="{{ route('products.show', $product->product_id) }}" class="btn btn-sm btn-primary">View Details</a>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <form action="{{ route('cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-sm btn-success add-to-cart" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                                            </button>
                                        </form>
                                        @if($product->isInWishlist())
                                            <form action="{{ route('wishlist.remove', $product->getWishlistItem()->wishlist_item_id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger add-to-wishlist">
                                                    <i class="fas fa-heart"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('wishlist.add') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger add-to-wishlist">
                                                    <i class="fas fa-heart"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    No products found matching your criteria.
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('sort-select').addEventListener('change', function() {
        const [sortBy, sortOrder] = this.value.split('-');
        const url = new URL(window.location);
        url.searchParams.set('sort_by', sortBy);
        url.searchParams.set('sort_order', sortOrder);
        window.location.href = url.toString();
    });
</script>
@endpush
@endsection
