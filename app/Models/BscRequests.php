<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BscRequests extends Model
{
    use HasFactory;
    protected $connection = "bsc_connection";

}
