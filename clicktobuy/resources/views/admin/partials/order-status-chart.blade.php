<!-- Order Status Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Order Status Breakdown</h6>
    </div>    <div class="card-body">
        <div class="chart-pie mb-4">
            <canvas id="orderStatusChart" data-status="{{ json_encode($orderStatusCounts) }}"></canvas>
        </div>
        <div class="mt-4 text-center small">
            <span class="mr-2">
                <i class="fas fa-circle text-warning"></i> Pending
            </span>
            <span class="mr-2">
                <i class="fas fa-circle text-info"></i> Processing
            </span>
            <span class="mr-2">
                <i class="fas fa-circle text-primary"></i> Shipped
            </span>
            <span class="mr-2">
                <i class="fas fa-circle text-success"></i> Delivered
            </span>
            <span class="mr-2">
                <i class="fas fa-circle text-danger"></i> Cancelled
            </span>
        </div>
    </div>
</div>
