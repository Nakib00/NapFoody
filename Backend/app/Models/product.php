<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'product_image',
        'status',
        'admin_id',
    ];

    public function sizeRegular()
    {
        return $this->hasOne(size::class)->where('name', 'Regular');
    }

    public function sizes()
    {
        return $this->hasMany(size::class);
    }

    public function extras()
    {
        return $this->hasMany(extra::class);
    }
}
