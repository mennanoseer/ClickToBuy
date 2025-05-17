<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\CreditCardPayment;
use App\Models\PayPalPayment;
use App\Models\BankTransferPayment;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the checkout page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cart = auth()->user()->customer->cart;
        
        if ($cart->cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('checkout.index', compact('cart'));
    }

    /**
     * Process the checkout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function processCheckout(Request $request)
    {
        // Validate request data
        $validationRules = [
            'payment_method' => 'required|in:credit_card,paypal,bank_transfer',
            'shipping_address' => 'required|string|max:100',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:20',
            'shipping_country' => 'required|string|max:50',
            'shipping_zip_code' => 'required|string|max:10',
        ];

        // Add payment method-specific validation rules
        if ($request->payment_method == 'credit_card') {
            $validationRules['card_number'] = 'required|string|size:16';
            $validationRules['card_holder'] = 'required|string|max:100';
            $validationRules['expiry_date'] = 'required|string|size:5';
            $validationRules['cvv'] = 'required|string|size:3';
        } elseif ($request->payment_method == 'paypal') {
            $validationRules['paypal_email'] = 'required|email|max:100';
            $validationRules['transaction_id'] = 'required|string|max:100';
        } elseif ($request->payment_method == 'bank_transfer') {
            $validationRules['bank_name'] = 'required|string|max:100';
            $validationRules['account_number'] = 'required|string|max:50';
            $validationRules['routing_number'] = 'required|string|max:50';
        }

        $request->validate($validationRules);

        // Get cart items
        $cart = auth()->user()->customer->cart;
        
        if ($cart->cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Calculate total
        $totalPrice = $cart->cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        // Start transaction
        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'order_date' => now(),
                'total_price' => $totalPrice,
                'status' => 'pending',
                'customer_id' => auth()->user()->customer->customer_id,
            ]);

            // Create order items
            foreach ($cart->cartItems as $cartItem) {
                OrderItem::create([
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                    'order_id' => $order->order_id,
                    'product_id' => $cartItem->product_id,
                ]);

                // Update product stock
                $product = $cartItem->product;
                $product->stock -= $cartItem->quantity;
                $product->save();
            }

            // Create payment record
            $payment = Payment::create([
                'payment_date' => now(),
                'amount' => $totalPrice,
                'status' => 'pending',
                'order_id' => $order->order_id,
                'payment_type' => $request->payment_method,
            ]);

            // Create specific payment type record
            if ($request->payment_method == 'credit_card') {
                CreditCardPayment::create([
                    'payment_id' => $payment->payment_id,
                    'card_number' => $request->card_number,
                    'card_holder' => $request->card_holder,
                    'expiry_date' => $request->expiry_date,
                    'cvv' => $request->cvv,
                ]);
            } elseif ($request->payment_method == 'paypal') {
                PayPalPayment::create([
                    'payment_id' => $payment->payment_id,
                    'paypal_email' => $request->paypal_email,
                    'transaction_id' => $request->transaction_id,
                ]);
            } elseif ($request->payment_method == 'bank_transfer') {
                BankTransferPayment::create([
                    'payment_id' => $payment->payment_id,
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                    'routing_number' => $request->routing_number,
                ]);
            }

            // Create shipment
            Shipment::create([
                'address' => $request->shipping_address,
                'city' => $request->shipping_city,
                'state' => $request->shipping_state,
                'country' => $request->shipping_country,
                'zip_code' => $request->shipping_zip_code,
                'status' => 'processing',
                'order_id' => $order->order_id,
            ]);

            // Clear cart after successful order
            $cart->cartItems()->delete();

            // Add loyalty points for the customer
            $customer = auth()->user()->customer;
            $customer->loyalty_points += floor($totalPrice / 10); // 1 point for every $10 spent
            $customer->save();

            DB::commit();

            return redirect()->route('orders.confirmation', $order->order_id);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred during checkout: ' . $e->getMessage());
        }
    }
}
