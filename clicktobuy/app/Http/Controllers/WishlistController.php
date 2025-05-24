<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the wishlist.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->customer) {
            return redirect()->route('home')->with('error', 'Customer profile not found.');
        }
        
        $wishlist = auth()->user()->customer->wishlist;
        
        if (!$wishlist) {
            // Create wishlist if it doesn't exist
            $wishlist = Wishlist::create([
                'customer_id' => auth()->user()->customer->customer_id,
                'created_date' => now(),
            ]);
        }
        
        return view('wishlist.index', compact('wishlist'));
    }

    /**
     * Add a product to the wishlist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addToWishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $wishlist = auth()->user()->customer->wishlist;

        // Check if the product already exists in the wishlist
        $existingItem = $wishlist->wishlistItems()
            ->where('product_id', $product->product_id)
            ->first();

        if (!$existingItem) {
            WishlistItem::create([
                'wishlist_id' => $wishlist->wishlist_id,
                'product_id' => $product->product_id,
                'added_date' => now()
            ]);
            $message = 'Product added to wishlist!';
        } else {
            $message = 'Product is already in your wishlist.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified item from wishlist.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeItem($id)
    {
        $wishlistItem = WishlistItem::findOrFail($id);
        
        // Ensure the wishlist item belongs to the authenticated user
        if ($wishlistItem->wishlist->customer_id != auth()->user()->customer->customer_id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $wishlistItem->delete();

        return redirect()->back()->with('success', 'Item removed from wishlist!');
    }

    /**
     * Move wishlist item to cart.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function moveToCart($id)
    {
        $wishlistItem = WishlistItem::findOrFail($id);
        
        // Ensure the wishlist item belongs to the authenticated user
        if ($wishlistItem->wishlist->customer_id != auth()->user()->customer->customer_id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Add to cart
        $cart = auth()->user()->customer->cart;
        $cartController = new CartController();
        
        $request = new Request([
            'product_id' => $wishlistItem->product_id,
            'quantity' => 1
        ]);
        
        $cartController->addToCart($request);
        
        // Remove from wishlist
        $wishlistItem->delete();

        return redirect()->back()->with('success', 'Item moved to cart!');
    }
}
