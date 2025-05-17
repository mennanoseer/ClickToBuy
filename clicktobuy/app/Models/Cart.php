<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $primaryKey = 'cart_id';

    protected $fillable = [
        'customer_id',
        'created_date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    public function getTotalAttribute()
    {
        return $this->cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
    }
}
