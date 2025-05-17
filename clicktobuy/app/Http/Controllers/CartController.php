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
        $cart = auth()->user()->customer->cart;
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
}
