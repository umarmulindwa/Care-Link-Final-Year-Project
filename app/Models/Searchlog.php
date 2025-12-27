<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Searchlog extends Model
{
    protected $fillable = [
        'search_text',
        'search_within',
        'search_by_name',
        'search_by_email',
    ];
}
