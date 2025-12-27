<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $connection = "leave_connection";

    protected $fillable = [
        'leaving_staff', 'start', 'end', 'days', 'delegated_to', 'status', 'name', 'reason_for_leave', 'delegated_to_pid', 'delegated_to_name'
    ];

    public function staffDelegated()
    {
        return $this->belongsTo(StaffProfile::class,  'delegated_to', 'email');
    }
}
