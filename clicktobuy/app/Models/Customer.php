<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'customer_id';
    public $incrementing = false;

    protected $fillable = [
        'customer_id',
        'registration_date',
        'loyalty_points',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id', 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'customer_id');
    }

    public function wishlist()
    {
        return $this->hasOne(Wishlist::class, 'customer_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'customer_id');
    }
}
