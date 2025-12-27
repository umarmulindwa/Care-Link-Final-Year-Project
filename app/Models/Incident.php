<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $connection = 'incident_reporting_connection';

    public function logs()
    {
        return $this->hasMany(IncidentLog::class, 'incident_id');
    }
    public function IncidentSupply()
    {
        return $this->hasMany(IncidentSupply::class, 'incidents_id');
    }
    public function comments()
    {
        return $this->hasMany(IncidentComment::class, 'incidents_id');
    }
    public function IncidentTimeline()
    {
        return $this->hasMany(IncidentTimeline::class, 'incidents_id');
    }
    public function AffectedEntity()
    {
        return $this->hasMany(AffectedEntity::class, 'incident_id');
    }

}
