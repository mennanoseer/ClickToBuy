<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Customer;
use App\Models\Cart;
use App\Models\Wishlist;

class EnsureCustomerExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Check if user has a customer record, create if not
            if (!$user->customer) {
                \Log::info('Creating customer record for user', ['user_id' => $user->user_id]);
                
                // Create customer record
                $customer = Customer::create([
                    'customer_id' => $user->user_id,
                    'registration_date' => now(),
                    'loyalty_points' => 0,
                ]);
                
                // Create cart
                Cart::create([
                    'customer_id' => $customer->customer_id,
                    'created_date' => now(),
                ]);
                
                // Create wishlist
                Wishlist::create([
                    'customer_id' => $customer->customer_id,
                    'created_date' => now(),
                ]);
                
                \Log::info('Customer record created successfully', ['customer_id' => $customer->customer_id]);
            }
        }
        
        return $next($request);
    }
}
