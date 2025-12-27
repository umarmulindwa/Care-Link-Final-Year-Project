<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisationUnit extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        "name"
    ];

    // public function staffProfile(){
    //     return $this->hasMany(StaffProfile::class, 'organisation_unit','id');
    // }
}
