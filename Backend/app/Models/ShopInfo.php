<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'admin_id',
        'email',
        'websit',
        'phone',
        'city',
        'state',
        'country',
        'zip',
        'address',
        'logo',
    ];
}
