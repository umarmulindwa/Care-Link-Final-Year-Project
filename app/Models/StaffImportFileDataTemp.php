<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffImportFileDataTemp extends Model
{
    protected $fillable = [
        "personal_id", "index_number", "name", "email", "position_text", "position_id", "section", "organisation_unit", "grade", "reports_to", "category", "gender",
        "appointment_type", "date_began_working_with_unicef", "date_began_working_with_unicef_country_level", "date_contract_end"
    ];
}
