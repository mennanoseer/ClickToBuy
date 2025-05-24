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
