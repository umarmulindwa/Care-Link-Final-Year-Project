<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DependantDetail extends Model
{

    protected $fillable = [
        "name", "sex", "date_of_birth", "staff_id"
    ];

    public function documents(): MorphMany
    {
        return $this->morphMany(StaffFile::class,'storeable');
    }
}
