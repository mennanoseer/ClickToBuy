<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCardPayment extends Model
{
    use HasFactory;

    protected $table = 'credit_card_payments';
    protected $primaryKey = 'payment_id';
    public $incrementing = false;

    protected $fillable = [
        'payment_id',
        'card_number',
        'card_holder',
        'expiry_date',
        'cvv',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
