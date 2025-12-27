<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentComment extends Model
{
    protected $connection = "incident_reporting_connection";

    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incidents_id');
    }
}
