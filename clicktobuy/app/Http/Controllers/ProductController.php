<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Search by name or description
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Sorting
        $sortField = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        $products = $query->paginate(12);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Display the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        $relatedProducts = Product::where('category_id', $product->category_id)
                                ->where('product_id', '!=', $id)
                                ->where('is_active', true)
                                ->take(4)
                                ->get();
        $reviews = $product->reviews()->orderBy('review_date', 'desc')->get();

        return view('products.show', compact('product', 'relatedProducts', 'reviews'));
    }

    /**
     * Store a product review.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeReview(Request $request, $id)
    {
        $this->middleware('auth');

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string'
        ]);

        $product = Product::findOrFail($id);

        Review::create([
            'customer_id' => auth()->user()->customer->customer_id,
            'product_id' => $product->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'review_date' => now()
        ]);

        return redirect()->back()->with('success', 'Review submitted successfully!');
    }
}
