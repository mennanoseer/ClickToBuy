@extends('layouts.admin')

@section('title', 'Dashboard')

@section('styles')
<style>
    .stats-card {
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        color: white;
        transition: transform 0.3s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .card-blue {
        background: linear-gradient(135deg, #3498db, #2980b9);
    }
    .card-green {
        background: linear-gradient(135deg, #2ecc71, #27ae60);
    }
    .card-orange {
        background: linear-gradient(135deg, #e67e22, #d35400);
    }
    .card-red {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
    }
    .card-purple {
        background: linear-gradient(135deg, #9b59b6, #8e44ad);
    }
    .card-teal {
        background: linear-gradient(135deg, #1abc9c, #16a085);
    }
    .stats-card i {
        font-size: 2.5rem;
        margin-bottom: 10px;
    }
    .stats-card h2 {
        margin-bottom: 5px;
        font-weight: bold;
    }
    .stats-card p {
        margin: 0;
        opacity: 0.8;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
    
    <!-- Statistics Cards -->
    <div class="row">
        <!-- Total Sales Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($totalSales, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalOrders }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Customers Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCustomers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProducts }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>{{ $order->order_id }}</td>
                                    <td>{{ $order->customer->user->user_name }}</td>
                                    <td>{{ $order->order_date->format('M d, Y') }}</td>
                                    <td>${{ number_format($order->total_price, 2) }}</td>
                                    <td>
                                        @if($order->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($order->status == 'processing')
                                            <span class="badge badge-info">Processing</span>
                                        @elseif($order->status == 'shipped')
                                            <span class="badge badge-primary">Shipped</span>
                                        @elseif($order->status == 'delivered')
                                            <span class="badge badge-success">Delivered</span>
                                        @elseif($order->status == 'cancelled')
                                            <span class="badge badge-danger">Cancelled</span>
                                        @endif
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
                </div>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Low Stock Products</h6>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $product->stock == 0 ? 'danger' : 'warning' }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.edit', $product->product_id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
