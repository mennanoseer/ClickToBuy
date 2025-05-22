@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Product Reviews</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Manage Reviews</h6>
            <div class="dropdown no-arrow">
                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                    id="filtersDropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <i class="fas fa-filter fa-sm"></i> Filters
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="filtersDropdown">
                    <form action="{{ route('admin.reviews.index') }}" method="GET" class="px-3 py-2" style="width: 300px;">
                        <div class="form-group">
                            <label for="product_id">Product</label>
                            <select class="form-control" name="product_id" id="product_id">
                                <option value="">All Products</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->product_id }}" {{ request('product_id') == $product->product_id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="rating">Rating</label>
                            <select class="form-control" name="rating" id="rating">
                                <option value="">All Ratings</option>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                        {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date_from">Date From</label>
                            <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="form-group">
                            <label for="date_to">Date To</label>
                            <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="form-group">
                            <label for="moderation_status">Moderation Status</label>
                            <select class="form-control" name="moderation_status" id="moderation_status">
                                <option value="">All Reviews</option>
                                <option value="needs_review" {{ request('moderation_status') == 'needs_review' ? 'selected' : '' }}>Needs Review</option>
                                <option value="approved" {{ request('moderation_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary btn-block">Clear Filters</a>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="reviewsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $review)
                        <tr class="{{ $review->requires_moderation ? 'table-warning' : '' }}">
                            <td>
                                {{ $review->review_id }}
                                @if($review->requires_moderation)
                                    <span class="badge bg-warning text-dark">Needs Review</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.products.edit', $review->product_id) }}">
                                    {{ $review->product->name }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.customers.show', $review->customer_id) }}">
                                    {{ $review->customer->user->user_name }}
                                </a>
                            </td>
                            <td>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                    @endfor
                                </div>
                            </td>
                            <td>{{ Str::limit($review->comment, 50) }}</td>
                            <td>{{ $review->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.reviews.show', $review->review_id) }}" class="btn btn-sm btn-info" title="View Review">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($review->requires_moderation)
                                    <form action="{{ route('admin.reviews.approve', $review->review_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Approve Review">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <form action="{{ route('admin.reviews.destroy', $review->review_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Review" onclick="return confirm('Are you sure you want to delete this review?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $reviews->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#reviewsTable').DataTable({
            "paging": false,
            "info": false,
            "ordering": true,
            "order": [[5, 'desc']] // Sort by date column (index 5) in descending order
        });
    });
</script>
@endsection 