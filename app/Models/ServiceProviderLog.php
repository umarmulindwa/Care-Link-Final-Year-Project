<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceProviderLog extends Model
{
    protected $fillable = [
        'service_provider_id',
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
