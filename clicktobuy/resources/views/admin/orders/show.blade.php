@extends('layouts.admin')

@section('styles')
<link href="{{ asset('css/admin-order-details.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Order #{{ $order->order_id }}</h1>
        <div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Orders
            </a>
            <button class="btn btn-primary shadow-sm ml-2" onclick="window.print()">
                <i class="fas fa-print fa-sm text-white-50"></i> Print Order
            </button>
        </div>
    </div>
    
    <!-- Order Timeline -->
    @include('admin.orders.partials.status-timeline')

    <div class="row">
        <!-- Order Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-5 font-weight-bold text-gray-800">Order ID:</div>
                        <div class="col-md-7">{{ $order->order_id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 font-weight-bold text-gray-800">Order Date:</div>
                        <div class="col-md-7">{{ $order->order_date->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 font-weight-bold text-gray-800">Status:</div>
                        <div class="col-md-7">
                            <span class="badge status-badge badge-{{ 
                                $order->status == 'pending' ? 'warning' : 
                                ($order->status == 'processing' ? 'info' : 
                                ($order->status == 'shipped' ? 'primary' : 
                                ($order->status == 'delivered' ? 'success' : 
                                'danger'))) }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 font-weight-bold text-gray-800">Payment Status:</div>
                        <div class="col-md-7">
                            <span class="badge badge-{{ $order->payment_status == 'paid' ? 'success' : 'secondary' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 font-weight-bold text-gray-800">Payment Method:</div>
                        <div class="col-md-7">{{ $order->payment_method }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 font-weight-bold text-gray-800">Total Amount:</div>
                        <div class="col-md-7">${{ number_format($order->total_price, 2) }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Update Order Status -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Order Status</h6>
                </div>
                <div class="card-body">
                    <form id="updateStatusForm" action="{{ route('admin.orders.updateStatus', $order->order_id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="note">Note (Optional)</label>
                            <textarea class="form-control" id="note" name="note" rows="2" placeholder="Add a note about this status change"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" id="updateStatusBtn">Update Status</button>
                    </form>
                    <div id="statusUpdateMessage" class="mt-2 text-center" style="display: none;"></div>
                    
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.getElementById('updateStatusForm');
                        
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();
                            
                            const formData = new FormData(form);
                            const messageDiv = document.getElementById('statusUpdateMessage');
                            const submitBtn = document.getElementById('updateStatusBtn');
                            
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                            
                            fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                messageDiv.textContent = data.message;
                                messageDiv.className = 'mt-2 text-center alert alert-success';
                                messageDiv.style.display = 'block';
                                
                                // Update the status badge in the order info
                                const statusBadge = document.querySelector('.status-badge');
                                const newStatus = document.getElementById('status').value;
                                
                                if (statusBadge) {
                                    // Remove old status classes
                                    statusBadge.classList.remove('badge-warning', 'badge-info', 'badge-primary', 'badge-success', 'badge-danger');
                                    
                                    // Add appropriate class
                                    if (newStatus === 'pending') {
                                        statusBadge.classList.add('badge-warning');
                                        statusBadge.textContent = 'Pending';
                                    } else if (newStatus === 'processing') {
                                        statusBadge.classList.add('badge-info');
                                        statusBadge.textContent = 'Processing';
                                    } else if (newStatus === 'shipped') {
                                        statusBadge.classList.add('badge-primary');
                                        statusBadge.textContent = 'Shipped';
                                    } else if (newStatus === 'delivered') {
                                        statusBadge.classList.add('badge-success');
                                        statusBadge.textContent = 'Delivered';
                                    } else if (newStatus === 'cancelled') {
                                        statusBadge.classList.add('badge-danger');
                                        statusBadge.textContent = 'Cancelled';
                                    }
                                }
                                
                                // Add new status to history section
                                const historyList = document.querySelector('.list-group-flush');
                                if (historyList) {
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1500);
                                }
                                
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = 'Update Status';
                            })
                            .catch(error => {
                                messageDiv.textContent = 'An error occurred. Please try again.';
                                messageDiv.className = 'mt-2 text-center alert alert-danger';
                                messageDiv.style.display = 'block';
                                
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = 'Update Status';
                                console.error('Error:', error);
                            });
                        });
                    });
                    </script>
                </div>
            </div>
            
            <!-- Status History -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status History</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($order->status_history as $history)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Changed to <strong>{{ ucfirst($history->status) }}</strong></h6>
                                <small>{{ $history->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                            <p class="mb-1">
                                <small class="text-muted">From {{ ucfirst($history->previous_status) }}</small>
                            </p>
                            @if($history->note)
                                <p class="mb-1">{{ $history->note }}</p>
                            @endif
                            <small>By {{ $history->user ? $history->user->user_name : 'System' }}</small>
                        </div>
                        @empty
                        <div class="list-group-item">
                            <p class="mb-0">No status history available.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Shipment Information -->
            @if($order->status == 'shipped' || $order->status == 'delivered')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Shipment Information</h6>
                </div>
                <div class="card-body">
                    @if($order->tracking_number)
                    <div class="row mb-3">
                        <div class="col-md-5 font-weight-bold text-gray-800">Carrier:</div>
                        <div class="col-md-7">{{ $order->shipping_carrier }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 font-weight-bold text-gray-800">Tracking #:</div>
                        <div class="col-md-7">{{ $order->tracking_number }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-5 font-weight-bold text-gray-800">Shipped Date:</div>
                        <div class="col-md-7">{{ $order->shipped_date ? $order->shipped_date->format('M d, Y') : 'N/A' }}</div>
                    </div>
                    @endif
                    
                    <form action="{{ route('admin.orders.updateShipment', $order->order_id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label for="shipping_carrier">Shipping Carrier</label>
                            <input type="text" class="form-control" id="shipping_carrier" name="shipping_carrier" value="{{ old('shipping_carrier', $order->shipping_carrier) }}">
                        </div>
                        <div class="form-group">
                            <label for="tracking_number">Tracking Number</label>
                            <input type="text" class="form-control" id="tracking_number" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update Shipment</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Customer Information -->
        <div class="col-lg-8">
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold text-gray-800">Name:</div>
                                <div class="col-md-8">
                                    <a href="{{ route('admin.customers.show', $order->customer_id) }}">
                                        {{ $order->customer->user->user_name }}
                                    </a>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold text-gray-800">Email:</div>
                                <div class="col-md-8">{{ $order->customer->user->email }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold text-gray-800">Phone:</div>
                                <div class="col-md-8">{{ $order->customer->user->phone_number ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Shipping Address</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $order->shipping_address_line1 }}</p>
                            @if($order->shipping_address_line2)
                                <p class="mb-0">{{ $order->shipping_address_line2 }}</p>
                            @endif
                            <p class="mb-0">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                            <p class="mb-0">{{ $order->shipping_country }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Items</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product && $item->product->image_url)
                                                <img src="{{ asset($item->product->image_url) }}" alt="{{ $item->product_name ?? $item->product->product_name }}" class="img-thumbnail mr-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <div class="font-weight-bold">{{ $item->product_name ?? $item->product->product_name }}</div>
                                                @if($item->product)
                                                    <div class="small text-muted">SKU: {{ $item->product->sku ?? 'N/A' }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Subtotal:</th>
                                    <td>${{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">Shipping:</th>
                                    <td>${{ number_format($order->shipping_cost, 2) }}</td>
                                </tr>
                                @if($order->discount > 0)
                                <tr>
                                    <th colspan="3" class="text-right">Discount:</th>
                                    <td>-${{ number_format($order->discount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th colspan="3" class="text-right">Tax:</th>
                                    <td>${{ number_format($order->tax, 2) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">Total:</th>
                                    <td class="font-weight-bold">${{ number_format($order->total_price, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Order Notes -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Notes</h6>
                </div>
                <div class="card-body">
                    @if($order->notes)
                        <p>{{ $order->notes }}</p>
                    @else
                        <p class="text-muted">No notes for this order.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style media="print">
    @page {
        size: auto;
        margin: 10mm;
    }
    
    body {
        background-color: #fff !important;
    }
    
    .no-print, .no-print * {
        display: none !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        margin-bottom: 20px !important;
        break-inside: avoid;
    }
    
    .card-header {
        background-color: #f8f9fc !important;
        border-bottom: 1px solid #ddd !important;
        padding: 10px 15px !important;
    }
    
    .shadow {
        box-shadow: none !important;
    }
    
    .table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    
    .table td, .table th {
        border: 1px solid #ddd !important;
        padding: 8px !important;
    }
</style>
@endsection 