<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentRequest extends Model
{
    protected $connection = 'staff_Onbording_connection';

    public function files()
    {
        return $this->hasMany(Equipment_Requests_files::class, 'equipment_requests_id');
    }

    public function timeline()
    {
        return $this->hasMany(EquipmentRequestTimeline::class, 'equipment_request_id');
    }

    public function items()
    {
        return $this->hasMany(EquipmentRequestItems::class, 'equipment_request_id');
    }

}
