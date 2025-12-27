<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyEmailLog extends Model
{
    use HasFactory;

    protected $connection = 'supplyPlanning_connection';
    protected $table = 'supply_email_logs';
}
