<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class posorderList extends Model
{
    use HasFactory;

    protected $fillable = [
        'posorder_id',
        'product_id',
        'size_id',
        'quantity',
        'price'
    ];
}
