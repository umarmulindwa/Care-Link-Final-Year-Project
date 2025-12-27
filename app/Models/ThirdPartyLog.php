<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThirdPartyLog extends Model
{
    protected $fillable = [
        'third_party_profile_id',
        'icon',
        'color',
        'title',
        'type',
        'reference_id'
    ];

    public function getCreatedAtAttribute($value)
    {
        $date = $this->asDateTime($value);
        return $date->timezone(env('TIMEZONE'));
    }

    public function getUpdatedAtAttribute($value)
    {
        $date = $this->asDateTime($value);
        return $date->timezone(env('TIMEZONE'));
    }
}
