<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayPalPayment extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_id';
    public $incrementing = false;

    protected $fillable = [
        'payment_id',
        'paypal_email',
        'transaction_id',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
