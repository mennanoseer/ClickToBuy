<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Cart;
use App\Models\Wishlist;

class EnsureCartWishlistExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->customer) {
            $customer = auth()->user()->customer;
            
            // Check if cart exists, create if not
            if (!$customer->cart) {
                Cart::create([
                    'customer_id' => $customer->customer_id,
                    'created_date' => now(),
                ]);
            }
            
            // Check if wishlist exists, create if not
            if (!$customer->wishlist) {
                Wishlist::create([
                    'customer_id' => $customer->customer_id,
                    'created_date' => now(),
                ]);
            }
        }
        
        return $next($request);
    }
}
