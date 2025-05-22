<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use HasFactory;
    
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'history_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'status',
        'previous_status',
        'note',
        'updated_by'
    ];
    
    /**
     * Get the order that owns the status history.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
    
    /**
     * Get the user who updated the status.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }
}