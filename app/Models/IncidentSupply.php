<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentSupply extends Model
{
    use HasFactory;

    protected $connection = 'incident_reporting_connection';


    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incidents_id');
    }
}
