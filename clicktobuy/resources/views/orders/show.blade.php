@extends('layouts.app')

@section('title', 'Order Details')

@section('styles')
<style>
    .order-status-timeline {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }

    .status-step {
        flex: 1;
        position: relative;
        text-align: center;
    }
    
    .status-step::before {
        content: '';
        position: absolute;
        top: 15px;
        left: calc(50% + 15px);
        right: calc(-50% + 15px);
        height: 2px;
        background-color: #ccc;
        z-index: 1;
    }
    
    .status-step:last-child::before {
        display: none;
    }
    
    .status-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #ccc;
        color: white;
        margin-bottom: 8px;
        position: relative;
        z-index: 2;
    }
    
    .status-icon.active {
        background-color: #007bff;
    }
    
    .status-label {
        font-size: 0.85rem;
        margin-top: 5px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Orders
        </a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Order #{{ $order->order_id }}</h5>
                <span class="badge bg-{{ 
                    $order->status == 'pending' ? 'warning' : 
                    ($order->status == 'processing' ? 'info' : 
                    ($order->status == 'shipped' ? 'primary' : 
                    ($order->status == 'delivered' ? 'success' : 
                    ($order->status == 'cancelled' ? 'danger' : 'secondary')))) 
                }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Order Information</h6>
                    <p class="mb-0">
                        <strong>Order Date:</strong> {{ $order->order_date->format('F j, Y, g:i A') }}<br>
                        <strong>Order Status:</strong> {{ ucfirst($order->status) }}<br>
                        <strong>Total Amount:</strong> ${{ number_format($order->total_price, 2) }}
                    </p>
                </div>
                <div class="col-md-6">
                    <h6>Customer Information</h6>
                    <p class="mb-0">
                        <strong>Name:</strong> {{ $order->customer->user->user_name }}<br>
                        <strong>Email:</strong> {{ $order->customer->user->email }}<br>
                        <strong>Phone:</strong> {{ $order->customer->user->phone_number }}
                    </p>
                </div>
            </div>
            
            <h6>Order Items</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('products.show', $item->product->product_id) }}">
                                        {{ $item->product->name }}
                                    </a>
                                </td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                            <td>${{ number_format($order->total_price / 1.1, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Tax (10%):</strong></td>
                            <td>${{ number_format($order->total_price - ($order->total_price / 1.1), 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td><strong>${{ number_format($order->total_price, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Information</h5>
                </div>
                <div class="card-body">
                    <address class="mb-0">
                        <strong>{{ $order->customer->user->user_name }}</strong><br>
                        {{ $order->shipment->address }}<br>
                        {{ $order->shipment->city }}, {{ $order->shipment->state }} {{ $order->shipment->zip_code }}<br>
                        {{ $order->shipment->country }}
                    </address>
                    
                    <hr>
                    
                    <h6>Shipping Details</h6>
                    <p class="mb-0">
                        <strong>Status:</strong> 
                        <span class="badge bg-{{ 
                            $order->shipment->status == 'pending' ? 'warning' : 
                            ($order->shipment->status == 'processing' ? 'info' : 
                            ($order->shipment->status == 'shipped' ? 'primary' : 
                            ($order->shipment->status == 'delivered' ? 'success' : 'secondary'))) 
                        }}">
                            {{ ucfirst($order->shipment->status) }}
                        </span><br>
                        <strong>Carrier:</strong> {{ $order->shipment->carrier ?? 'To be determined' }}<br>
                        @if($order->shipment->tracking_number)
                            <strong>Tracking Number:</strong> {{ $order->shipment->tracking_number }}<br>
                        @endif
                        @if($order->shipment->shipment_date)
                            <strong>Shipment Date:</strong> {{ $order->shipment->shipment_date->format('F j, Y') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Payment Information</h5>
                </div>
                <div class="card-body">
                    <p>
                        <strong>Payment Method:</strong> 
                        @if($order->payment->creditCardPayment)
                            Credit Card (xxxx-xxxx-xxxx-{{ substr($order->payment->creditCardPayment->card_number, -4) }})
                        @elseif($order->payment->paypalPayment)
                            PayPal ({{ $order->payment->paypalPayment->paypal_email }})
                        @elseif($order->payment->bankTransferPayment)
                            Bank Transfer
                        @else
                            {{ ucfirst(str_replace('_', ' ', $order->payment->payment_type ?? 'Unknown')) }}
                        @endif
                    </p>
                    
                    <p>
                        <strong>Payment Status:</strong> 
                        <span class="badge bg-{{ $order->payment->status === 'completed' ? 'success' : 'warning' }}">
                            {{ ucfirst($order->payment->status) }}
                        </span>
                    </p>
                    
                    <p>
                        <strong>Payment Date:</strong> 
                        {{ $order->payment->payment_date ? $order->payment->payment_date->format('F j, Y, g:i A') : 'Not processed yet' }}
                    </p>
                    
                    <p class="mb-0">
                        <strong>Amount:</strong> ${{ number_format($order->payment->amount, 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    @if($order->status !== 'cancelled' && $order->status !== 'delivered')
        <div class="card">
            <div class="card-body">
                <h5>Need Help?</h5>
                <p>If you have any questions about your order, please contact our customer support.</p>
                <a href="{{ route('contact') }}" class="btn btn-outline-primary">
                    <i class="fas fa-envelope me-1"></i> Contact Support
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
