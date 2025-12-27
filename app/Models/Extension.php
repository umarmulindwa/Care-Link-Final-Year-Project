<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    protected $fillable = [
        'email', 'ext', 'direct_line', 'mobile', 'whatsapp_no', 'name', 'home'
    ];
}
