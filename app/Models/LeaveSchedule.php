<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveSchedule extends Model
{
    protected $connection = 'leave_connection';
    
    
    protected $fillable = [
        'leaving_staff',
        'name',
        'start',
        'end',
        'type_Vacation',
        'leave_id'

    ];
}
