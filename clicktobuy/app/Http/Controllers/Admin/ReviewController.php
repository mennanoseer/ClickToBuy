<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display a listing of the reviews.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Review::with(['customer.user', 'product']);
        
        // Filter by product
        if ($request->has('product_id') && $request->product_id) {
            $query->where('product_id', $request->product_id);
        }
        
        // Filter by rating
        if ($request->has('rating') && $request->rating) {
            $query->where('rating', $request->rating);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('review_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('review_date', '<=', $request->date_to);
        }
        
        // Filter by moderation status
        if ($request->has('moderation_status')) {
            if ($request->moderation_status === 'needs_review') {
                $query->where('requires_moderation', true);
            } elseif ($request->moderation_status === 'approved') {
                $query->where('requires_moderation', false);
            }
        }
        
        $reviews = $query->orderBy('review_date', 'desc')->paginate(15);
        $products = \App\Models\Product::orderBy('name')->get();
        
        return view('admin.reviews.index', compact('reviews', 'products'));
    }

    /**
     * Show the specified review.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $review = Review::with(['customer.user', 'product'])->findOrFail($id);
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Remove the specified review from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully!');
    }

    /**
     * Approve a review that was flagged for moderation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        $review = Review::findOrFail($id);
        $review->requires_moderation = false;
        $review->save();

        return redirect()->back()->with('success', 'Review has been approved.');
    }
}
