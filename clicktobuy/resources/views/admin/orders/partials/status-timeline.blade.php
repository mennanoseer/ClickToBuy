<!-- Order Status Timeline -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Order Status Timeline</h6>
    </div>
    <div class="card-body">
        <div class="timeline">
            <div class="timeline-item {{ in_array($order->status, ['pending', 'processing', 'shipped', 'delivered']) ? 'complete' : ($order->status == 'cancelled' ? 'cancelled' : '') }}">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <h4 class="timeline-title">Order Placed</h4>
                    <p class="timeline-date">{{ $order->order_date->format('M d, Y h:i A') }}</p>
                </div>
            </div>
            
            <div class="timeline-item {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'complete' : ($order->status == 'cancelled' ? 'cancelled' : '') }}">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <h4 class="timeline-title">Processing</h4>
                    <p class="timeline-date">
                        @if($order->status_history && $order->status_history->where('status', 'processing')->first())
                            {{ $order->status_history->where('status', 'processing')->first()->created_at->format('M d, Y h:i A') }}
                        @else
                            Pending
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="timeline-item {{ in_array($order->status, ['shipped', 'delivered']) ? 'complete' : ($order->status == 'cancelled' ? 'cancelled' : '') }}">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <h4 class="timeline-title">Shipped</h4>
                    <p class="timeline-date">
                        @if($order->status_history && $order->status_history->where('status', 'shipped')->first())
                            {{ $order->status_history->where('status', 'shipped')->first()->created_at->format('M d, Y h:i A') }}
                        @else
                            Pending
                        @endif
                    </p>
                    @if($order->tracking_number)
                        <p class="mb-0">
                            <strong>Carrier:</strong> {{ $order->shipping_carrier }}<br>
                            <strong>Tracking:</strong> {{ $order->tracking_number }}
                        </p>
                    @endif
                </div>
            </div>
            
            <div class="timeline-item {{ $order->status == 'delivered' ? 'complete' : ($order->status == 'cancelled' ? 'cancelled' : '') }}">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <h4 class="timeline-title">Delivered</h4>
                    <p class="timeline-date">
                        @if($order->status_history && $order->status_history->where('status', 'delivered')->first())
                            {{ $order->status_history->where('status', 'delivered')->first()->created_at->format('M d, Y h:i A') }}
                        @else
                            Pending
                        @endif
                    </p>
                </div>
            </div>
            
            @if($order->status == 'cancelled')
            <div class="timeline-item cancelled">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <h4 class="timeline-title">Cancelled</h4>
                    <p class="timeline-date">
                        @if($order->status_history && $order->status_history->where('status', 'cancelled')->first())
                            {{ $order->status_history->where('status', 'cancelled')->first()->created_at->format('M d, Y h:i A') }}
                        @else
                            {{ $order->updated_at->format('M d, Y h:i A') }}
                        @endif
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
