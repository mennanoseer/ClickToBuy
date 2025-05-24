<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'payment_date',
        'amount',
        'status',
        'order_id',
        'payment_type',
    ];
    
    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function creditCardPayment()
    {
        return $this->hasOne(CreditCardPayment::class, 'payment_id');
    }

    public function paypalPayment()
    {
        return $this->hasOne(PayPalPayment::class, 'payment_id');
    }

    public function bankTransferPayment()
    {
        return $this->hasOne(BankTransferPayment::class, 'payment_id');
    }
}
