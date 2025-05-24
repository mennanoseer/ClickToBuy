@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<style>
    /* Hide any badges appearing in the card header area */
    .order-history-header .badge, 
    .order-history-header + .badge {
        display: none !important;
    }
    
    /* Ensure badges are properly positioned in the status column */
    .order-status-badge {
        position: static !important;
        display: inline-block !important;
    }
</style>

<div class="container">
    <h1 class="mb-4">My Orders</h1>
    
    @if($orders->count() > 0)
        <div class="card">
            <div class="order-history-header py-2 px-3 bg-light border-bottom">
                <h5 class="mb-0">Order History</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $loop_index => $order)
                            <tr>
                                <td>{{ $loop_index + 1 }}</td>
                                <td>{{ $order->order_date->format('M d, Y') }}</td>
                                <td>{{ $order->orderItems->count() }}</td>
                                <td>${{ number_format($order->total_price, 2) }}</td>
                                <td>
                                    <span class="order-status-badge badge rounded-pill {{ $order->status == 'pending' ? 'bg-warning text-dark' : 
                                                        ($order->status == 'processing' ? 'bg-info text-dark' : 
                                                        ($order->status == 'shipped' ? 'bg-primary' : 
                                                        ($order->status == 'delivered' ? 'bg-success' : 
                                                        ($order->status == 'cancelled' ? 'bg-danger' : 'bg-secondary')))) 
                                    }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('orders.show', $order->order_id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
                <div class="card-footer">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                <h3>No Orders Yet</h3>
                <p class="mb-4">You haven't placed any orders yet.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-cart me-1"></i> Start Shopping
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
