@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Customers</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Manage Customers</h6>
            <div class="dropdown no-arrow">
                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                    id="filtersDropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <i class="fas fa-filter fa-sm"></i> Filters
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="filtersDropdown">
                    <form action="{{ route('admin.customers.index') }}" method="GET" class="px-3 py-2" style="width: 300px;">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" class="form-control" name="search" id="search" value="{{ request('search') }}" placeholder="Name or Email">
                        </div>
                        <div class="form-group">
                            <label for="date_from">Registered From</label>
                            <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="form-group">
                            <label for="date_to">Registered To</label>
                            <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request('date_to') }}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary btn-block">Clear Filters</a>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
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
                                <span class="badge badge-info">{{ $customer->orders_count ?? 0 }}</span>
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
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#customersTable').DataTable({
            "paging": false,
            "info": false,
            "ordering": true,
            "order": [[0, 'desc']]
        });
    });
</script>
@endsection 