<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class posorder extends Model
{
    use HasFactory;

    protected $fillable = [
        'orders_id',
        'admin_id',
        'stuff_id',
        'branch_id',
        'total_price',
        'payment_method',
        'discount',
    ];
}
