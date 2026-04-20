<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportFiles extends Model
{

    protected $fillable = ['filepath', 'support_responses_id', 'support_requests_id','submittedBy'];


}
