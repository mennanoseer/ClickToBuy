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
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->whereHas('user', function($q) use ($searchTerm) {
                $q->where('user_name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
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
        
        $customers = $query->paginate(15);
        
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
            }
        ])->findOrFail($id);
        
        return view('admin.customers.show', compact('customer'));
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
