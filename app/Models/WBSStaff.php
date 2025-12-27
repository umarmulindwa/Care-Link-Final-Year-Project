<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WBSStaff extends Model
{
    protected $fillable = [
        'programme_officer', 'programme_assistant', 'wbs_id'
    ];
}
