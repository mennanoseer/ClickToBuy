<div class="table-responsive">
    <table class="table table-bordered" id="customersTable" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Registered On</th>
                <th>Orders</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
            <tr>
                <td>{{ $customer->customer_id }}</td>
                <td>{{ $customer->user->user_name }}</td>
                <td>{{ $customer->user->email }}</td>
                <td>{{ $customer->user->phone_number ?? 'N/A' }}</td>
                <td>{{ $customer->user->created_at->format('M d, Y') }}</td>
                <td>
                    <span class="badge bg-info">{{ $customer->orders_count ?? 0 }}</span>
                </td>
                <td>
                    <div class="btn-group">
                        <a href="{{ route('admin.customers.show', $customer->customer_id) }}" class="btn btn-sm btn-info" title="View Customer">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.customers.edit', $customer->customer_id) }}" class="btn btn-sm btn-primary" title="Edit Customer">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3">
        {{ $customers->appends(request()->query())->links() }}
    </div>
</div>
