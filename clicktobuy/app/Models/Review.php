<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $primaryKey = 'review_id';

    protected $fillable = [
        'customer_id',
        'product_id',
        'rating',
        'comment',
        'review_date',
        'requires_moderation',
    ];
    
    protected $casts = [
        'review_date' => 'datetime',
        'rating' => 'integer',
        'requires_moderation' => 'boolean',
    ];
    
    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['customer'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
