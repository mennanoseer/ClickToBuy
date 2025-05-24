@extends('layouts.admin')

@section('title', 'Dashboard')

@section('styles')
<!-- Add this meta tag to set up CSRF token for all AJAX requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">
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

    <!-- Analytics Charts Row -->
    <div class="row">
        <div class="col-lg-12">
            @include('admin.partials.sales-chart')
        </div>
    </div>

    <!-- Recent Orders and Top Products -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                    <div>
                        <button id="refreshRecentOrders" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="recent-orders-container">
                        @include('admin.partials.recent-orders-table')
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
                                        <span class="badge bg-{{ $product->stock == 0 ? 'danger' : 'warning' }} text-{{ $product->stock == 0 ? 'white' : 'dark' }}">
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
    
    <!-- Top Products and Notifications -->
    <div class="row">
        <div class="col-lg-8">
            @include('admin.partials.top-products')
        </div>
        <div class="col-lg-4">
            @include('admin.partials.notifications')
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Set up CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Auto-refresh sales chart on page load
        refreshSalesData(30); // Default to 30 days
        
        // Refresh sales data when period buttons are clicked
        $('.period-option').on('click', function() {
            const period = $(this).data('period');
            $('.period-option').removeClass('btn-primary').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            refreshSalesData(period);
        });
        
        // Refresh recent orders functionality
        $('#refreshRecentOrders').on('click', function() {
            $(this).find('i').addClass('fa-spin');
            refreshRecentOrders();
        });
        
        function refreshRecentOrders() {
            $.ajax({
                url: '{{ route("admin.api.recentOrders") }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#recent-orders-container').html(response.html);
                    }
                    $('#refreshRecentOrders').find('i').removeClass('fa-spin');
                },
                error: function() {
                    alert('Error refreshing recent orders. Please try again.');
                    $('#refreshRecentOrders').find('i').removeClass('fa-spin');
                }
            });
        }
        
        function refreshSalesData(period) {
            $.ajax({
                url: '{{ route("admin.api.salesData") }}',
                type: 'GET',
                data: { period: period },
                dataType: 'json',
                success: function(response) {
                    // Update the sales chart with new data
                    if (window.salesChart) {
                        window.salesChart.data.labels = Object.keys(response.data);
                        window.salesChart.data.datasets[0].data = Object.values(response.data);
                        window.salesChart.update();
                        
                        // Update summary information
                        $('#totalSales').text('$' + response.totals.sales.toFixed(2));
                        $('#orderCount').text(response.totals.orders);
                        $('#avgOrder').text('$' + response.totals.average.toFixed(2));
                        $('#totalSalesPeriod').text('(' + period + ' days)');
                        $('#orderCountPeriod').text('(' + period + ' days)');
                        $('#avgOrderPeriod').text('(' + period + ' days)');
                    }
                },
                error: function() {
                    console.error('Error loading sales data');
                }
            });
        }
        
        // Set a timer to refresh the recent orders every 30 seconds
        setInterval(function() {
            refreshRecentOrders();
        }, 30000);
    });
</script>
@endsection
