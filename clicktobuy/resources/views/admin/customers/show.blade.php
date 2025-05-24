@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Customer: {{ $customer->user->user_name }}</h1>
        <div>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Customers
            </a>
            <a href="{{ route('admin.customers.edit', $customer->customer_id) }}" class="btn btn-primary shadow-sm ml-2">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Customer
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Customer Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-5 font-weight-bold text-gray-800">Customer ID:</div>
                        <div class="col-md-7">{{ $customer->customer_id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 font-weight-bold text-gray-800">Name:</div>
                        <div class="col-md-7">{{ $customer->user->user_name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 font-weight-bold text-gray-800">Email:</div>
                        <div class="col-md-7">{{ $customer->user->email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 font-weight-bold text-gray-800">Phone:</div>
                        <div class="col-md-7">{{ $customer->user->phone_number ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 font-weight-bold text-gray-800">Registered:</div>
                        <div class="col-md-7">{{ $customer->user->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 font-weight-bold text-gray-800">Total Orders:</div>
                        <div class="col-md-7">{{ $customer->orders_count }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 font-weight-bold text-gray-800">Total Spent:</div>
                        <div class="col-md-7">${{ number_format($customer->total_spent, 2) }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Default Address -->
            @if($customer->defaultAddress)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Default Address</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $customer->defaultAddress->address_line1 }}</p>
                    @if($customer->defaultAddress->address_line2)
                        <p class="mb-0">{{ $customer->defaultAddress->address_line2 }}</p>
                    @endif
                    <p class="mb-0">{{ $customer->defaultAddress->city }}, {{ $customer->defaultAddress->state }} {{ $customer->defaultAddress->postal_code }}</p>
                    <p class="mb-0">{{ $customer->defaultAddress->country }}</p>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Customer Orders -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                    @if(count($orders) > 0)
                    <a href="{{ route('admin.orders.index', ['customer_id' => $customer->customer_id]) }}" class="btn btn-sm btn-primary">View All Orders</a>
                    @endif
                </div>
                <div class="card-body">
                    @if(count($orders) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->order_id }}</td>
                                    <td>{{ $order->order_date->format('M d, Y') }}</td>
                                    <td>${{ number_format($order->total_price, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ 
                                            $order->status == 'pending' ? 'warning' : 
                                            ($order->status == 'processing' ? 'info' : 
                                            ($order->status == 'shipped' ? 'primary' : 
                                            ($order->status == 'delivered' ? 'success' : 
                                            'danger'))) }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->order_id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-center text-muted mb-0">This customer has no orders yet.</p>
                    @endif
                </div>
            </div>
            
            <!-- Customer Reviews -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Reviews</h6>
                    @if(count($reviews) > 0)
                    <a href="{{ route('admin.reviews.index', ['customer_id' => $customer->customer_id]) }}" class="btn btn-sm btn-primary">View All Reviews</a>
                    @endif
                </div>
                <div class="card-body">
                    @if(count($reviews) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reviews as $review)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.products.edit', $review->product_id) }}">
                                            {{ $review->product->name }}
                                        </a>
                                    </td>
                                    <td>
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-secondary' }}"></i>
                                        @endfor
                                    </td>
                                    <td>{{ Str::limit($review->comment, 50) }}</td>
                                    <td>{{ $review->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-center text-muted mb-0">This customer has not left any reviews yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 