@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Orders</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Manage Orders</h6>
            <div class="dropdown no-arrow">
                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                    id="filtersDropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <i class="fas fa-filter fa-sm"></i> Filters
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="filtersDropdown">
                    <form action="{{ route('admin.orders.index') }}" method="GET" class="px-3 py-2" style="width: 300px;">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status" id="status">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date_from">Date From</label>
                            <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="form-group">
                            <label for="date_to">Date To</label>
                            <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request('date_to') }}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-block">Clear Filters</a>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->order_id }}</td>
                            <td>
                                <a href="{{ route('admin.customers.show', $order->customer_id) }}">
                                    {{ $order->customer->user->user_name }}
                                </a>
                            </td>
                            <td>{{ $order->order_date->format('M d, Y H:i') }}</td>
                            <td>${{ number_format($order->total_price, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $order->status == 'pending' ? 'warning' : 
                                    ($order->status == 'processing' ? 'info' : 
                                    ($order->status == 'shipped' ? 'primary' : 
                                    ($order->status == 'delivered' ? 'success' : 
                                    'danger'))) }} status-badge" data-order-id="{{ $order->order_id }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.orders.show', $order->order_id) }}" class="btn btn-sm btn-info" title="View Order">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Update Status">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                                            @if($status != $order->status)
                                            <form action="{{ route('admin.orders.updateStatus', $order->order_id) }}" class="status-update-form" method="POST" data-order-id="{{ $order->order_id }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $status }}">
                                                <button type="submit" class="dropdown-item">
                                                    Mark as {{ ucfirst($status) }}
                                                </button>
                                            </form>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#ordersTable').DataTable({
            "paging": false,
            "info": false,
            "ordering": true,
            "order": [[2, 'desc']] // Sort by date column (index 2) in descending order
        });
        
        // Handle form submission with AJAX
        $('.status-update-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const orderId = form.data('order-id');
            const statusValue = form.find('input[name="status"]').val();
            const csrfToken = form.find('input[name="_token"]').val();
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: {
                    _token: csrfToken,
                    _method: 'PATCH',
                    status: statusValue
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update the badge
                        const statusBadge = $(`span.status-badge[data-order-id="${orderId}"]`);
                        statusBadge.removeClass('bg-warning bg-info bg-primary bg-success bg-danger');
                        
                        // Add the appropriate class based on the new status
                        switch(statusValue) {
                            case 'pending':
                                statusBadge.addClass('bg-warning');
                                break;
                            case 'processing':
                                statusBadge.addClass('bg-info');
                                break;
                            case 'shipped':
                                statusBadge.addClass('bg-primary');
                                break;
                            case 'delivered':
                                statusBadge.addClass('bg-success');
                                break;
                            case 'cancelled':
                                statusBadge.addClass('bg-danger');
                                break;
                        }
                        
                        // Update the text
                        statusBadge.text(statusValue.charAt(0).toUpperCase() + statusValue.slice(1));
                        
                        // Show success message
                        alert('Order status updated to ' + statusValue);
                        
                        // If we're on the dashboard page, refresh the recent orders
                        if (window.refreshRecentOrders) {
                            window.refreshRecentOrders();
                        }
                        
                        // If there's an instance of the dashboard with recent orders, refresh it
                        if ($('#recent-orders-container').length) {
                            $.ajax({
                                url: '/admin/api/recent-orders',
                                type: 'GET',
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        $('#recent-orders-container').html(response.html);
                                    }
                                }
                            });
                        }
                    }
                },
                error: function() {
                    alert('Error updating order status. Please try again.');
                }
            });
        });
    });
</script>
@endsection 