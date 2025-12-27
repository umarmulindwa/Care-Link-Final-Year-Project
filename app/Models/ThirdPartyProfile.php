<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThirdPartyProfile extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'password',
        'logo',
        'token',
        'address',
        'latitude',
        'longitude',
        'is_current',
        'created_by'
    ];

    protected $hidden = ['password'];
}
