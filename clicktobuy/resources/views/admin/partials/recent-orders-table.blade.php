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
                    <span class="badge bg-warning text-dark">Pending</span>
                @elseif($order->status == 'processing')
                    <span class="badge bg-info text-white">Processing</span>
                @elseif($order->status == 'shipped')
                    <span class="badge bg-primary text-white">Shipped</span>
                @elseif($order->status == 'delivered')
                    <span class="badge bg-success text-white">Delivered</span>
                @elseif($order->status == 'cancelled')
                    <span class="badge bg-danger text-white">Cancelled</span>
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
