<?php

namespace App\Models\AirtimeData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffCategory extends Model
{
    use HasFactory;

    protected $connection = 'airtime_data_connection';
}
