@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Orders</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Manage Orders</h6>
            <div class="dropdown no-arrow">
                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                    id="filtersDropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <i class="fas fa-filter fa-sm"></i> Filters
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="filtersDropdown">
                    <form class="px-3 py-2" style="width: 300px;" id="ordersFilterForm">
                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request('date_to') }}">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-2">Apply Filters</button>
                        <button type="button" id="clearFilters" class="btn btn-secondary w-100">Clear Filters</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @include('admin.orders.partials.orders_table')
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#ordersTable').DataTable({
        "paging": false,
        "info": false,
        "ordering": true,
        "order": [[2, 'desc']] // Sort by date column (index 2) in descending order
    });

    // Handle filter form submission
    $('#ordersFilterForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const url = "{{ route('admin.orders.index') }}";
        const formData = new FormData(form[0]);
        formData.append('ajax', '1');

        // Show loading spinner
        $('.card-body').append('<div class="text-center py-4" id="loadingSpinner"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
        
        // Make the AJAX request
        $.ajax({
            url: url,
            type: 'GET',
            data: form.serialize() + '&ajax=1',
            success: function(response) {
                // Remove loading spinner
                $('#loadingSpinner').remove();
                
                // Update the table content
                $('.table-responsive').html(response);
                
                // Reinitialize DataTable
                $('#ordersTable').DataTable({
                    "paging": false,
                    "info": false,
                    "ordering": true,
                    "order": [[2, 'desc']]
                });

                // Update URL with filter parameters
                const params = new URLSearchParams(form.serialize());
                const newUrl = window.location.pathname + '?' + params.toString();
                window.history.pushState({}, '', newUrl);

                // Close the dropdown
                $('#filtersDropdown').dropdown('hide');
            },
            error: function(xhr) {
                // Remove loading spinner
                $('#loadingSpinner').remove();
                alert('Error loading orders. Please try again.');
            }
        });
    });

    // Handle clear filters
    $('#clearFilters').on('click', function() {
        // Reset form fields
        $('#status').val('');
        $('#date_from').val('');
        $('#date_to').val('');
        
        // Submit the form to clear filters
        $('#ordersFilterForm').submit();
    });

    // Initialize status update functionality
    function initStatusUpdateForms() {
        $('.status-update-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const orderId = form.data('order-id');
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        // Update the status badge
                        const badge = $(`.status-badge[data-order-id="${orderId}"]`);
                        badge.removeClass().addClass(`badge status-badge ${response.order.statusClass}`);
                        badge.text(response.order.status.charAt(0).toUpperCase() + response.order.status.slice(1));
                        
                        // Show success message
                        alert('Order status updated successfully!');
                    }
                },
                error: function() {
                    alert('Error updating order status. Please try again.');
                }
            });
        });
    }

    // Initialize status update forms on page load
    initStatusUpdateForms();
});
</script>
@endsection 