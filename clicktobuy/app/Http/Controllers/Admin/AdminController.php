<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        // Get statistics for dashboard
        $totalSales = Order::whereIn('status', ['completed', 'delivered'])->sum('total_price');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        
        // Get recent orders
        $recentOrders = Order::with('customer.user')
                           ->orderBy('order_date', 'desc')
                           ->take(10)
                           ->get();
                           
        // Get low stock products
        $lowStockProducts = Product::where('stock', '<', 10)
                                 ->orderBy('stock', 'asc')
                                 ->take(5)
                                 ->get();
        
        // Get order status counts for pie chart
        $orderStatusCounts = Order::selectRaw('status, count(*) as count')
                              ->groupBy('status')
                              ->pluck('count', 'status')
                              ->toArray();
        
        // Get top selling products
        $topProducts = Product::withCount(['orderItems as units_sold' => function ($query) {
                            $query->whereHas('order', function ($subQuery) {
                                $subQuery->whereIn('status', ['completed', 'delivered']);
                            });
                          }])
                          ->withSum(['orderItems as revenue' => function ($query) {
                              $query->whereHas('order', function ($subQuery) {
                                  $subQuery->whereIn('status', ['completed', 'delivered']);
                              });
                              $query->select(\DB::raw('quantity * price'));
                          }], 'price')
                          ->with('category')
                          ->orderByDesc('units_sold')
                          ->take(5)
                          ->get();
        
        // Get sales data for the past 30 days for chart
        $salesData = Order::whereIn('status', ['completed', 'delivered'])
                      ->where('order_date', '>=', now()->subDays(30))
                      ->selectRaw('DATE(order_date) as date, SUM(total_price) as total')
                      ->groupBy('date')
                      ->orderBy('date')
                      ->get()
                      ->pluck('total', 'date')
                      ->toArray();
        
        // Get recent notifications
        $notifications = auth()->user()->notifications()->take(5)->get();
        
        return view('admin.dashboard', compact(
            'totalSales', 
            'totalOrders', 
            'totalProducts', 
            'totalCustomers',
            'recentOrders',
            'lowStockProducts',
            'orderStatusCounts',
            'topProducts',
            'salesData',
            'notifications'
        ));
    }

    /**
     * Update admin profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . auth()->user()->user_id . ',user_id',
            'phone_number' => 'nullable|string|max:100',
        ]);
        
        $user = auth()->user();
        $user->user_name = $request->user_name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        
        // Use the DB facade to update directly
        \Illuminate\Support\Facades\DB::table('users')
            ->where('user_id', $user->user_id)
            ->update([
                'user_name' => $request->user_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number
            ]);
        
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Get sales data for the dashboard charts via AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSalesData(Request $request)
    {
        $period = $request->input('period', 30); // Default to 30 days
        
        // Get sales data for the specified period
        $salesData = Order::whereIn('status', ['completed', 'delivered'])
                      ->where('order_date', '>=', now()->subDays($period))
                      ->selectRaw('DATE(order_date) as date, SUM(total_price) as total')
                      ->groupBy('date')
                      ->orderBy('date')
                      ->get()
                      ->pluck('total', 'date')
                      ->toArray();
        
        // Get total sales and orders for the period
        $totals = [
            'sales' => array_sum($salesData),
            'orders' => Order::whereIn('status', ['completed', 'delivered'])
                         ->where('order_date', '>=', now()->subDays($period))
                         ->count(),
            'average' => 0
        ];
        
        // Calculate average order value
        if ($totals['orders'] > 0) {
            $totals['average'] = $totals['sales'] / $totals['orders'];
        }
        
        return response()->json([
            'data' => $salesData,
            'totals' => $totals
        ]);
    }

    /**
     * Get recent orders for dashboard via AJAX.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecentOrders()
    {
        $recentOrders = Order::with('customer.user')
                         ->orderBy('order_date', 'desc')
                         ->take(10)
                         ->get();
                         
        $ordersHtml = view('admin.partials.recent-orders-table', compact('recentOrders'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $ordersHtml
        ]);
    }
}
