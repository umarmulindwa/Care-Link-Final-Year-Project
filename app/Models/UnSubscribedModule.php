<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnSubscribedModule extends Model
{
    protected $fillable = [
        "user_id", "system_id"
    ];
}
