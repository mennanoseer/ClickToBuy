<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display a listing of the customers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Customer::with('user');
        
        // Search by name or email
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->whereHas('user', function($q) use ($searchTerm) {
                $q->where('user_name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }
        
        // Filter by date range with defaults to avoid errors
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereHas('user', function($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->date_from);
            });
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereHas('user', function($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->date_to);
            });
        }
        
        // Sort customers
        $sortBy = $request->sort_by ?? 'registration_date';
        $sortDirection = $request->sort_dir ?? 'desc';
        
        if (in_array($sortBy, ['user_name', 'email'])) {
            $query->join('users', 'customers.customer_id', '=', 'users.user_id')
                  ->orderBy($sortBy, $sortDirection)
                  ->select('customers.*');
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }
        
        // Add order count to each customer
        $query->withCount('orders');
        
        $customers = $query->paginate(15);
        
        // If AJAX request, return only the table content
        if ($request->ajax() || $request->has('ajax')) {
            return view('admin.customers.partials.customers_table', compact('customers'))->render();
        }
        
        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Display the specified customer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = Customer::with([
            'user', 
            'orders' => function($query) {
                $query->orderBy('order_date', 'desc');
            },
            'reviews' => function($query) {
                $query->with('product')->orderBy('created_at', 'desc');
            }
        ])->withCount('orders')->findOrFail($id);
        
        // Get the orders and reviews from the relationships
        $orders = $customer->orders;
        $reviews = $customer->reviews;
        
        // Calculate total spent
        $customer->total_spent = $orders->sum('total_price');
        
        return view('admin.customers.show', compact('customer', 'orders', 'reviews'));
    }

    /**
     * Show the form for editing the specified customer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::with('user')->findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        
        $request->validate([
            'user_name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($customer->user->user_id, 'user_id'),
            ],
            'address' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:100',
            'loyalty_points' => 'nullable|integer|min:0',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = $customer->user;
        $user->user_name = $request->user_name;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->phone_number = $request->phone_number;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        if ($request->filled('loyalty_points')) {
            $customer->loyalty_points = $request->loyalty_points;
            $customer->save();
        }

        return redirect()->route('admin.customers.show', $id)
            ->with('success', 'Customer updated successfully!');
    }

    /**
     * Update customer loyalty points.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateLoyaltyPoints(Request $request, $id)
    {
        $request->validate([
            'loyalty_points' => 'required|integer|min:0'
        ]);

        $customer = Customer::findOrFail($id);
        $customer->loyalty_points = $request->loyalty_points;
        $customer->save();

        return redirect()->route('admin.customers.show', $id)
            ->with('success', 'Loyalty points updated successfully.');
    }
}
