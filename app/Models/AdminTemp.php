<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminTemp extends Model
{
    protected $fillable = [
        'email', 'username', 'password'
    ];

    protected $appends = ['password_time'];

    public function getPasswordTimeAttribute()
    {
        return now()->diffInMinutes($this->updated_at);
    }
}
