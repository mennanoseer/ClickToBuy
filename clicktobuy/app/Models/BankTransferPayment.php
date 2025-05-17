<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransferPayment extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_id';
    public $incrementing = false;

    protected $fillable = [
        'payment_id',
        'bank_name',
        'account_number',
        'routing_number',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
