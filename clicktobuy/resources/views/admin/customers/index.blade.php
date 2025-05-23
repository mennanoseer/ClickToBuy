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
                    <form class="px-3 py-2" style="width: 300px;" id="customersFilterForm">
                        <div class="form-group mb-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" name="search" id="customer_search" value="{{ request('search') }}" placeholder="Name or Email">
                        </div>
                        <div class="form-group mb-3">
                            <label for="date_from" class="form-label">Registered From</label>
                            <input type="date" class="form-control" name="date_from" id="customer_date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="date_to" class="form-label">Registered To</label>
                            <input type="date" class="form-control" name="date_to" id="customer_date_to" value="{{ request('date_to') }}">
                        </div>
                        <button type="button" id="applyCustomerFilters" class="btn btn-primary w-100 mb-2">Apply Filters</button>
                        <button type="button" id="clearCustomerFilters" class="btn btn-secondary w-100">Clear Filters</button>
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
        
        // AJAX Filtering for Customers
        $('#applyCustomerFilters').on('click', function() {
            loadCustomersWithFilters();
        });
        
        $('#clearCustomerFilters').on('click', function() {
            $('#customer_search').val('');
            $('#customer_date_from').val('');
            $('#customer_date_to').val('');
            loadCustomersWithFilters();
        });
        
        function loadCustomersWithFilters() {
            const search = $('#customer_search').val();
            const dateFrom = $('#customer_date_from').val() || '';
            const dateTo = $('#customer_date_to').val() || '';
            
            // Show loading indicator
            $('.card-body').append('<div class="text-center py-4" id="loadingSpinner"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
            
            $.ajax({
                url: "{{ route('admin.customers.index') }}",
                type: 'GET',
                data: {
                    search: search,
                    date_from: dateFrom,
                    date_to: dateTo,
                    ajax: 1
                },
                success: function(response) {
                    // Remove loading indicator
                    $('#loadingSpinner').remove();
                    
                    // Replace table content with new data
                    $('.table-responsive').html(response);
                    
                    // Re-initialize DataTable
                    $('#customersTable').DataTable({
                        "paging": false,
                        "info": false,
                        "ordering": true,
                        "order": [[0, 'desc']]
                    });
                },
                error: function(xhr) {
                    // Remove loading indicator
                    $('#loadingSpinner').remove();
                    alert('Error loading customers. Please try again.');
                }
            });
        }
    });
</script>
@endsection 