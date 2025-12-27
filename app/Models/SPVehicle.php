<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SPVehicle extends Model
{
    protected $connection = 'admin_transport_connection';

    protected $fillable = [
        'service_provider_id',
        'license_plate',
        'name',
        'description',
        'model',
        'image'
    ];

    public function serviceProvider()
    {
        return $this->hasOne(User::class, 'id', 'service_provider_id');
    }
}
