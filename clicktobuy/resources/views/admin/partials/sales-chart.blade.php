<!-- Sales Chart -->
<div class="card shadow mb-4">    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Sales Overview</h6>
    </div>
    <div class="btn-group btn-group-sm mb-3 w-100 px-3" role="group" aria-label="Period options">
        <button type="button" class="btn btn-outline-primary period-option" data-period="7">Last 7 days</button>
        <button type="button" class="btn btn-primary period-option" data-period="30">Last 30 days</button>
        <button type="button" class="btn btn-outline-primary period-option" data-period="90">Last 90 days</button>
        <button type="button" class="btn btn-outline-primary period-option" data-period="365">Last year</button>
    </div><div class="card-body">
        <div class="chart-area">
            <canvas id="salesChart" data-sales="{{ json_encode($salesData) }}"></canvas>
        </div>
        <hr>
        <div class="row text-center">
            <div class="col-4">
                <h4 class="small font-weight-bold">Total Sales <span class="float-right" id="totalSalesPeriod"></span></h4>
                <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSales"></div>
            </div>
            <div class="col-4">
                <h4 class="small font-weight-bold">Orders <span class="float-right" id="orderCountPeriod"></span></h4>
                <div class="h5 mb-0 font-weight-bold text-gray-800" id="orderCount"></div>
            </div>
            <div class="col-4">
                <h4 class="small font-weight-bold">Avg. Order <span class="float-right" id="avgOrderPeriod"></span></h4>
                <div class="h5 mb-0 font-weight-bold text-gray-800" id="avgOrder"></div>
            </div>
        </div>
    </div>
</div>
