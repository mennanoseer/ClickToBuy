<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the user's orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::where('customer_id', auth()->user()->customer->customer_id)
                      ->orderBy('order_date', 'desc')
                      ->paginate(10);
                      
        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        
        // Ensure the order belongs to the authenticated user
        if ($order->customer_id != auth()->user()->customer->customer_id) {
            return redirect()->route('orders.index')->with('error', 'Unauthorized access.');
        }
        
        return view('orders.show', compact('order'));
    }

    /**
     * Display order confirmation page.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function confirmation($id)
    {
        $order = Order::findOrFail($id);
        
        // Ensure the order belongs to the authenticated user
        if ($order->customer_id != auth()->user()->customer->customer_id) {
            return redirect()->route('orders.index')->with('error', 'Unauthorized access.');
        }
        
        return view('orders.confirmation', compact('order'));
    }
}
