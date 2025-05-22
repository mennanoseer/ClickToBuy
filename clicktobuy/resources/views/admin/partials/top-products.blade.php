<!-- Top Products Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Units Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $product)
                    <tr>
                        <td>
                            <a href="{{ route('admin.products.edit', $product->product_id) }}">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td>{{ $product->category->name }}</td>
                        <td>{{ $product->units_sold }}</td>
                        <td>${{ number_format($product->revenue, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
