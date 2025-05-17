<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $primaryKey = 'wishlist_id';

    protected $fillable = [
        'customer_id',
        'created_date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class, 'wishlist_id');
    }
}
