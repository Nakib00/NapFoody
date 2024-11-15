<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'nid',
        'staff_image',
        'status',
        'branch_id',
        'admin_id',
        'address',
        'phone'
    ];

}
