<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $primaryKey = 'shipment_id';

    protected $fillable = [
        'shipment_date',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'tracking_number',
        'carrier',
        'status',
        'order_id',
    ];
    
    protected $casts = [
        'shipment_date' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
