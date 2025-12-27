<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ICTEmailLog extends Model
{
    use HasFactory;

    protected $connection = "ict_access_connection";

    protected $table = "access_email_logs";
}
