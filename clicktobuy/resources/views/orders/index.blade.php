@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container">
    <h1 class="mb-4">My Orders</h1>
    
    @if($orders->count() > 0)
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order History</h5>
                </div>
            </div>
            <div class="card-body p-0">
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
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->order_id }}</td>
                                    <td>{{ $order->order_date->format('M d, Y') }}</td>
                                    <td>{{ $order->orderItems->count() }}</td>
                                    <td>${{ number_format($order->total_price, 2) }}</td>
                                    <td>
                                        @if($order->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($order->status == 'processing')
                                            <span class="badge bg-info">Processing</span>
                                        @elseif($order->status == 'shipped')
                                            <span class="badge bg-primary">Shipped</span>
                                        @elseif($order->status == 'delivered')
                                            <span class="badge bg-success">Delivered</span>
                                        @elseif($order->status == 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                        @endif
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
