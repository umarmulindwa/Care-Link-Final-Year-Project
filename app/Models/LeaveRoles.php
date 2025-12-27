<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRoles extends Model
{
    use HasFactory;

    protected $connection = "leave_connection";

    protected $fillable = [
        "leave_id",
        'role_id',
        'permission_id',
    ];
}
