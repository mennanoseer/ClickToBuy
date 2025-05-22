<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'is_active',
        'category_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class, 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }
    
    /**
     * Get the average rating for this product
     * 
     * @return float
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?: 0;
    }
    
    /**
     * Get the total number of reviews for this product
     * 
     * @return int
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * Check if the product is in the user's wishlist
     * 
     * @return bool
     */
    public function isInWishlist()
    {
        if (!auth()->check() || !auth()->user()->customer) {
            return false;
        }
        
        $wishlist = auth()->user()->customer->wishlist;
        
        if (!$wishlist) {
            return false;
        }
        
        return $wishlist->wishlistItems()
            ->where('product_id', $this->product_id)
            ->exists();
    }
    
    /**
     * Get the wishlist item if the product is in the user's wishlist
     * 
     * @return \App\Models\WishlistItem|null
     */
    public function getWishlistItem()
    {
        if (!auth()->check() || !auth()->user()->customer) {
            return null;
        }
        
        $wishlist = auth()->user()->customer->wishlist;
        
        if (!$wishlist) {
            return null;
        }
        
        return $wishlist->wishlistItems()
            ->where('product_id', $this->product_id)
            ->first();
    }
}
