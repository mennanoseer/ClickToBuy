@extends('layouts.app')

@section('title', 'Order Confirmation')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-check-circle me-2"></i> Order Confirmed</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle text-success fa-5x mb-3"></i>
                        <h2>Thank You for Your Order!</h2>
                        <p class="lead">Your order has been placed successfully.</p>
                    </div>
                    
                    <div class="alert alert-info">
                        <strong>Order Number:</strong> #{{ $order->order_id }}<br>
                        <strong>Order Date:</strong> {{ $order->order_date->format('F j, Y, g:i A') }}
                    </div>
                    
                    <h5 class="mt-4">Order Details</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->price, 2) }}</td>
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
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>Shipping Information</h5>
                            <address>
                                <strong>{{ $order->customer->user->user_name }}</strong><br>
                                {{ $order->shipment->address }}<br>
                                {{ $order->shipment->city }}, {{ $order->shipment->state }} {{ $order->shipment->zip_code }}<br>
                                {{ $order->shipment->country }}<br>
                                <abbr title="Phone">P:</abbr> {{ $order->customer->user->phone_number }}
                            </address>
                        </div>
                        <div class="col-md-6">
                            <h5>Payment Information</h5>
                            <p>
                                <strong>Payment Method:</strong>                            @if(isset($order->payment) && $order->payment)
                                @if($order->payment->payment_type == 'credit_card' && isset($order->payment->creditCardPayment))
                                    Credit Card (xxxx-xxxx-xxxx-{{ substr($order->payment->creditCardPayment->card_number, -4) }})
                                @elseif($order->payment->payment_type == 'paypal' && isset($order->payment->paypalPayment))
                                    PayPal ({{ $order->payment->paypalPayment->paypal_email }})
                                @elseif($order->payment->payment_type == 'bank_transfer' && isset($order->payment->bankTransferPayment))
                                    Bank Transfer
                                    @else
                                        {{ ucfirst($order->payment->payment_type ?? 'Unknown') }}
                                    @endif
                                    <br>
                                    <strong>Payment Status:</strong> 
                                    <span class="badge bg-{{ $order->payment->status === 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($order->payment->status) }}
                                    </span>
                                @else
                                    Information not available
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>Shipping Details</h5>
                        <p>
                            @if(isset($order->shipment) && $order->shipment)
                                <strong>Status:</strong> 
                                <span class="badge bg-{{ $order->shipment->status === 'processing' ? 'warning' : 'info' }}">
                                    {{ ucfirst($order->shipment->status) }}
                                </span><br>
                                <strong>Carrier:</strong> {{ $order->shipment->carrier ?? 'To be determined' }}<br>
                                @if(isset($order->shipment->tracking_number) && $order->shipment->tracking_number)
                                    <strong>Tracking Number:</strong> {{ $order->shipment->tracking_number }}
                                @endif
                            @else
                                Shipping information not available yet
                            @endif
                        </p>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('orders.show', $order->order_id) }}" class="btn btn-primary">
                            <i class="fas fa-file-alt me-1"></i> View Order Details
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-list me-1"></i> View All Orders
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-home me-1"></i> Return to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
