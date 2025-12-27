<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffImportFileTemp extends Model
{
    protected $fillable = [
        "user_id", "headers", "data", "primary_field", "matched_fields", "using_recommended_template"
    ];

    public function getHeadersAttribute($value)
    {
        return json_decode($value, true);
    }
    public function getDataAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getMatchedFieldsAttribute($value)
    {
        return json_decode($value, true);
    }
}
