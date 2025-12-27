<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordChangeHistory extends Model
{
    use HasFactory;

    protected $table = 'password_change_history';

    protected $fillable = [
        'user_id',
        'previous_password',
    ];
}
