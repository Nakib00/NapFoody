<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class branch extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'branch_phone',
        'branch_address',
        'admin_id',
        'branch_code'
    ];
}
