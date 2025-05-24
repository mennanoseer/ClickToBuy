<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the cart contents.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->customer) {
            return redirect()->route('home')->with('error', 'Customer profile not found.');
        }
        
        $cart = auth()->user()->customer->cart;
        
        if (!$cart) {
            // Create cart if it doesn't exist
            $cart = Cart::create([
                'customer_id' => auth()->user()->customer->customer_id,
                'created_date' => now(),
            ]);
        }
        
        return view('cart.index', compact('cart'));
    }

    /**
     * Add a product to the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return redirect()->back()->with('error', 'Not enough stock available.');
        }

        $cart = auth()->user()->customer->cart;

        // Check if the product already exists in the cart
        $cartItem = $cart->cartItems()->where('product_id', $product->product_id)->first();

        if ($cartItem) {
            // Update quantity if product already in cart
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Add new item to cart
            CartItem::create([
                'cart_id' => $cart->cart_id,
                'product_id' => $product->product_id,
                'quantity' => $request->quantity
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    /**
     * Update the cart item quantity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateQuantity(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::findOrFail($id);
        
        // Ensure the cart item belongs to the authenticated user
        if ($cartItem->cart->customer_id != auth()->user()->customer->customer_id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Ensure requested quantity is available
        if ($cartItem->product->stock < $request->quantity) {
            return redirect()->back()->with('error', 'Not enough stock available.');
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return redirect()->back()->with('success', 'Cart updated successfully!');
    }

    /**
     * Remove the specified cart item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeItem($id)
    {
        $cartItem = CartItem::findOrFail($id);
        
        // Ensure the cart item belongs to the authenticated user
        if ($cartItem->cart->customer_id != auth()->user()->customer->customer_id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $cartItem->delete();

        return redirect()->back()->with('success', 'Item removed from cart!');
    }

    /**
     * Clear all items from the cart.
     *
     * @return \Illuminate\Http\Response
     */
    public function clearCart()
    {
        $cart = auth()->user()->customer->cart;
        $cart->cartItems()->delete();

        return redirect()->back()->with('success', 'Cart cleared!');
    }

    /**
     * Add a product to the cart via AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToCartAjax(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available.'
            ], 400);
        }

        $cart = auth()->user()->customer->cart;

        // Check if the product already exists in the cart
        $cartItem = $cart->cartItems()->where('product_id', $product->product_id)->first();

        if ($cartItem) {
            // Update quantity if product already in cart
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($product->stock < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available.'
                ], 400);
            }
            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            // Add new item to cart
            CartItem::create([
                'cart_id' => $cart->cart_id,
                'product_id' => $product->product_id,
                'quantity' => $request->quantity
            ]);
        }

        // Get updated cart count
        $cartCount = $cart->cartItems->count();
        $cartTotal = $cart->cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart!',
            'cart_count' => $cartCount,
            'cart_total' => number_format($cartTotal, 2)
        ]);
    }

    /**
     * Update the cart item quantity via AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateQuantityAjax(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::with('product')->findOrFail($id);
        
        // Ensure the cart item belongs to the authenticated user
        if ($cartItem->cart->customer_id != auth()->user()->customer->customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        // Ensure requested quantity is available
        if ($cartItem->product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available.',
                'max_quantity' => $cartItem->product->stock
            ], 400);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        // Get updated cart totals
        $cart = $cartItem->cart;
        $cartCount = $cart->cartItems->count();
        $cartSubtotal = $cart->cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
        $cartTax = $cartSubtotal * 0.1;
        $cartTotal = $cartSubtotal + $cartTax;
        $itemTotal = $cartItem->product->price * $cartItem->quantity;

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully!',
            'cart_count' => $cartCount,
            'cart_subtotal' => number_format($cartSubtotal, 2),
            'cart_tax' => number_format($cartTax, 2),
            'cart_total' => number_format($cartTotal, 2),
            'item_total' => number_format($itemTotal, 2),
            'quantity' => $cartItem->quantity
        ]);
    }

    /**
     * Remove the specified cart item via AJAX.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeItemAjax($id)
    {
        $cartItem = CartItem::findOrFail($id);
        
        // Ensure the cart item belongs to the authenticated user
        if ($cartItem->cart->customer_id != auth()->user()->customer->customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $cart = $cartItem->cart;
        $cartItem->delete();

        // Get updated cart totals
        $cartCount = $cart->cartItems->count();
        $cartSubtotal = $cart->cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
        $cartTax = $cartSubtotal * 0.1;
        $cartTotal = $cartSubtotal + $cartTax;

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart!',
            'cart_count' => $cartCount,
            'cart_subtotal' => number_format($cartSubtotal, 2),
            'cart_tax' => number_format($cartTax, 2),
            'cart_total' => number_format($cartTotal, 2)
        ]);
    }
}
