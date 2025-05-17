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
    public function index()
    {
        $reviews = Review::with(['customer.user', 'product'])->orderBy('review_date', 'desc')->paginate(15);
        return view('admin.reviews.index', compact('reviews'));
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
}
