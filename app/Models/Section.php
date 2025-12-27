<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
    protected $connection = "mysql";
    protected $fillable = [
        "name", "staff_chief_id"
    ];

    public function staff()
    {
        return $this->hasMany(StaffProfile::class, 'section')->where('staff_profiles.exited_unicef', "=", 0);
    }
}

