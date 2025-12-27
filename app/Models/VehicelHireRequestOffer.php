<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;  

class VehicelHireRequestOffer extends Model
{
    use HasFactory;
    protected $connection = 'admin_transport_connection';

    protected $fillable = [
        's_p_vehicle_id',
        's_p_driver_id',
        'service_provider_id',
        'vehicle_hire_s_p_request_id',
        'description',
        'currency',
        'cost',
    ];

    protected $appends = ['formated_created_at'];
    public function vehicle()
    {
        return $this->hasOne(SPVehicle::class, 'id', 's_p_vehicle_id');
    }

    public function driver()
    {
        return $this->hasOne(SPDriver::class, 'id', 's_p_driver_id');
    }

    public function provider()
    {
        // service provider
        return $this->hasOne(ServiceProvider::class, 'user_id', 'service_provider_id');
    }

    public function getFormatedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)->format("F jS y");
    }
}
