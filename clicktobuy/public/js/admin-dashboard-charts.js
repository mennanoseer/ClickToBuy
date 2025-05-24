// Dashboard Charts
$(document).ready(function() {
    // Sales Chart
    const salesChartCanvas = document.getElementById('salesChart');
    if (salesChartCanvas) {
        // Get the sales data from the data attribute on the canvas
        const salesChartData = salesChartCanvas.dataset.sales ? JSON.parse(salesChartCanvas.dataset.sales) : {};
        const labels = Object.keys(salesChartData);
        const values = Object.values(salesChartData);
        
        // Create chart and expose it globally for updates
        window.salesChart = new Chart(salesChartCanvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Sales',
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: values
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.raw.toFixed(2);
                            }
                        }
                    }
                }
            }
        });

        // Update chart period
        $('.period-option').click(function(e) {
            e.preventDefault();
            const period = $(this).data('period');
            
            $.ajax({
                url: '/admin/api/sales-data',
                data: { period: period },
                dataType: 'json',
                success: function(response) {
                    salesChart.data.labels = Object.keys(response.data);
                    salesChart.data.datasets[0].data = Object.values(response.data);
                    salesChart.update();
                    
                    $('#totalSales').text('$' + response.totals.sales.toFixed(2));
                    $('#orderCount').text(response.totals.orders);
                    $('#avgOrder').text('$' + response.totals.average.toFixed(2));
                    $('#totalSalesPeriod').text('(' + period + ' days)');
                    $('#orderCountPeriod').text('(' + period + ' days)');
                    $('#avgOrderPeriod').text('(' + period + ' days)');
                }
            });
        });
    }    // Order Status Chart
    const orderStatusChartCanvas = document.getElementById('orderStatusChart');
    if (orderStatusChartCanvas) {
        // Get the order status data from the data attribute on the canvas
        const orderStatusData = orderStatusChartCanvas.dataset.status ? JSON.parse(orderStatusChartCanvas.dataset.status) : {};
        
        const orderStatusChart = new Chart(orderStatusChartCanvas, {
            type: 'doughnut',
            data: {
                labels: Object.keys(orderStatusData).map(status => 
                    status.charAt(0).toUpperCase() + status.slice(1)
                ),
                datasets: [{
                    data: Object.values(orderStatusData),
                    backgroundColor: [
                        '#f6c23e', // warning - pending
                        '#36b9cc', // info - processing
                        '#4e73df', // primary - shipped
                        '#1cc88a', // success - delivered
                        '#e74a3b'  // danger - cancelled
                    ],
                    hoverBackgroundColor: [
                        '#d4a636',
                        '#2c9faf',
                        '#4264bf',
                        '#17a673',
                        '#c5382b'
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '70%'
            }
        });
    }
});
