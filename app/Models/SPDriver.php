<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SPDriver extends Model
{
    protected $connection = 'admin_transport_connection';

    protected $fillable = [
        'service_provider_id',
        'name',
        'email',
        'phone_number_1',
        'phone_number_2',
        'image'
    ];

    public function serviceProvider()
    {
        return $this->hasOne(User::class, 'id', 'service_provider_id');
    }

    public function offers()
    {
        return $this->hasMany(VehicelHireRequestOffer::class, 's_p_driver_id', 'id');
    }
}
