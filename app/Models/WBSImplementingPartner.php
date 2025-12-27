<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WBSImplementingPartner extends Model
{
    protected $fillable = [
        "name", "email", "wbs_id"
    ];
}
