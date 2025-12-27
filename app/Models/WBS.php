<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WBS extends Model
{
    protected $fillable = [
        "code", "pillar_id", "section_id", "output", "activity"
    ];

    public function pillar()
    {
        return $this->belongsTo(Pillar::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function staff()
    {
        return $this->hasMany(WBSStaff::class, 'wbs_id');
    }

    public function implementingPartners()
    {
        return $this->hasMany(WBSImplementingPartner::class, 'wbs_id');
    }
}
