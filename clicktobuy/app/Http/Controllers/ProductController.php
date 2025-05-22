<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Customer;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\User;
use App\Notifications\Admin\NewReviewNotification;
use App\Services\ContentModeration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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
        try {
            \Log::info('Loading product details for ID: ' . $id);
            
            $product = Product::findOrFail($id);
            
            // Load related products
            $relatedProducts = Product::where('category_id', $product->category_id)
                                    ->where('product_id', '!=', $id)
                                    ->where('is_active', true)
                                    ->take(4)
                                    ->get();
            
            // Get fresh reviews from the database, explicitly joining with customers and users
            $reviews = Review::with(['customer.user'])
                           ->where('product_id', $id)
                           ->orderBy('review_date', 'desc')
                           ->get();
                           
            \Log::info('Found reviews for product', [
                'product_id' => $id, 
                'review_count' => $reviews->count()
            ]);
    
            return view('products.show', compact('product', 'relatedProducts', 'reviews'));
        } catch (\Exception $e) {
            \Log::error('Error loading product: ' . $e->getMessage(), [
                'product_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('products.index')
                ->with('error', 'We couldn\'t load this product. Please try another one.');
        }
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
        // Auth middleware is applied in the routes file
        try {
            \Log::info('Review submission started for product ID: ' . $id, $request->all());
            
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string|min:2|max:1000'
            ]);

            $product = Product::findOrFail($id);
            
            // Check if user has customer profile
            $user = auth()->user();
            \Log::info('User details', [
                'user_id' => $user->user_id,
                'user_name' => $user->user_name,
                'has_customer' => $user->customer ? 'yes' : 'no'
            ]);
            
            if (!$user->customer) {
                \Log::error('User has no customer profile', ['user_id' => $user->user_id]);
                
                // Try to fix by creating a customer profile
                try {
                    $customer = \App\Models\Customer::create([
                        'customer_id' => $user->user_id,
                        'registration_date' => now(),
                        'loyalty_points' => 0,
                    ]);
                    
                    // Create cart for the customer
                    $cart = \App\Models\Cart::create([
                        'customer_id' => $customer->customer_id,
                        'created_date' => now(),
                    ]);
                    
                    // Create wishlist for the customer
                    $wishlist = \App\Models\Wishlist::create([
                        'customer_id' => $customer->customer_id,
                        'created_date' => now(),
                    ]);
                    
                    \Log::info('Created customer profile', ['customer_id' => $customer->customer_id]);
                    // Refresh user to get the new customer relationship
                    $user = $user->fresh();
                } catch (\Exception $e) {
                    \Log::error('Failed to create customer profile', ['error' => $e->getMessage()]);
                    return redirect()->back()->with('error', 'Your account is not properly set up. Please contact support.');
                }
            }

            // Get the customer record safely
            $customer = $user->customer;
            \Log::info('Customer details before review creation', [
                'customer_id' => $customer->customer_id,
                'loyalty_points' => $customer->loyalty_points
            ]);
            
            // Check if the customer has already reviewed this product
            $existingReview = Review::where('customer_id', $customer->customer_id)
                                    ->where('product_id', $product->product_id)
                                    ->first();
                                    
            if ($existingReview) {
                \Log::info('Customer already has a review for this product', [
                    'review_id' => $existingReview->review_id
                ]);
                
                // Update the existing review instead of creating a new one
                $existingReview->rating = $request->rating;
                $existingReview->comment = $request->comment;
                $existingReview->review_date = now();
                $existingReview->save();
                
                $review = $existingReview;
                
                return redirect()->back()->with('success', 'Your review has been updated!');
            }
            
            // Create new review
            $reviewData = [
                'customer_id' => $customer->customer_id,
                'product_id' => $product->product_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'review_date' => now()
            ];
            \Log::info('Creating review with data', $reviewData);
            
            // Using transaction to ensure data consistency
            DB::beginTransaction();
            try {
                $review = Review::create($reviewData);
                
                \Log::info('Review created successfully', ['review_id' => $review->review_id]);
                
                // Check for negative content or low ratings that require admin attention
                $hasNegativeRating = ContentModeration::hasNegativeRating($request->rating);
                $hasNegativeContent = ContentModeration::hasNegativeContent($request->comment);
                
                // Flag reviews with negative content for admin attention
                if ($hasNegativeRating || $hasNegativeContent) {
                    $review->requires_moderation = true;
                    $review->save();
                    
                    // Notify admins about potentially problematic review
                    $adminUsers = User::whereHas('admin')->get();
                    foreach ($adminUsers as $admin) {
                        $admin->notify(new NewReviewNotification($review));
                    }
                    
                    \Log::info('Review flagged for moderation', [
                        'review_id' => $review->review_id,
                        'negative_rating' => $hasNegativeRating,
                        'negative_content' => $hasNegativeContent
                    ]);
                }
                
                // Clear product cache if any to ensure new review appears
                \Cache::forget('product_'.$product->product_id.'_reviews');
                
                // Refresh the product to get updated review count
                $product->refresh();
                
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Database error creating review: ' . $e->getMessage(), [
                    'data' => $reviewData,
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
            
            // Add a small delay to ensure the database transaction is fully complete
            usleep(250000); // 0.25 seconds

            return redirect()->back()->with('success', 'Review submitted successfully!');
        } catch (\Exception $e) {
            \Log::error('Error creating review: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'product_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'There was a problem submitting your review. Please try again.')
                ->withInput();
        }
    }
}
