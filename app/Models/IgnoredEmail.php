<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IgnoredEmail extends Model
{
    protected $fillable = [
        'user_id', 'email', 'subject', 'is_deactivated', 'staff'
    ];
}
