<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwoFactorCode extends Model
{
    //
    protected $fillable = [
        "user_id",
        "user_table",
        "code",
        "expiry",
        "active",
        "redirect"
    ];
}
