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
        $totalSales = Order::where('status', 'completed')->sum('total_price');
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
        
        return view('admin.dashboard', compact(
            'totalSales', 
            'totalOrders', 
            'totalProducts', 
            'totalCustomers',
            'recentOrders',
            'lowStockProducts'
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
}
