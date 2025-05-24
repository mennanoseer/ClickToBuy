<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $primaryKey = 'admin_id';
    public $incrementing = false;

    protected $fillable = [
        'admin_id',
        'role',
        'last_login',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'admin_id', 'user_id');
    }
}
