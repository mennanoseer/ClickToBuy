@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center p-5" style="background-color: #f8f9fa;">
                    <h1 class="display-4">Welcome to ClickToBuy</h1>
                    <p class="lead">Your One-Stop Online Shopping Destination</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">Shop Now</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Categories -->
    <h2 class="mb-4">Shop by Category</h2>
    <div class="row mb-5">
        @foreach($categories as $category)
            <div class="col-md-2 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $category->name }}</h5>
                        @if($category->subcategories->count() > 0)
                            <ul class="list-unstyled">
                                @foreach($category->subcategories->take(3) as $subcategory)
                                    <li>
                                        <a href="{{ route('products.index', ['category_id' => $subcategory->category_id]) }}">
                                            {{ $subcategory->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        <a href="{{ route('products.index', ['category_id' => $category->category_id]) }}" class="btn btn-sm btn-outline-secondary">View All</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Featured Products -->
    <h2 class="mb-4">Featured Products</h2>
    <div class="row">
        @foreach($featuredProducts as $product)
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-truncate">{{ $product->description }}</p>
                        <p class="card-text font-weight-bold">${{ number_format($product->price, 2) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('products.show', $product->product_id) }}" class="btn btn-sm btn-primary">View Details</a>
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-sm btn-success">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
