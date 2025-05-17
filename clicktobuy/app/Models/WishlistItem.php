<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishlistItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'wishlist_item_id';

    protected $fillable = [
        'wishlist_id',
        'product_id',
        'added_date',
    ];

    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class, 'wishlist_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
