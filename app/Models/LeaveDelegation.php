<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveDelegation extends Model
{
    protected $connection = 'leave_connection';

    protected $fillable = ['delegated_role_to', 'leave_id'];

    protected $appends = ['staff'];

    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }

    public function profile()
    {
        return $this->hasOne(StaffProfile::class, 'email', 'delegated_role_to');
    }

    public function getStaffAttribute()
    {
        return StaffProfile::where('email', $this->leave->personal_id)->first();
    }
}
