<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IPLog extends Model
{
    protected $fillable = [
        'ip_profile_id',
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
