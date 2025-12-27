<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MobileDownload extends Model
{
    protected $fillable = [
        'user_id', 'file_id'
    ];

    protected $appends = [
        "date_formatted"
    ];

    public function getDateFormattedAttribute()
    {
        return Carbon::parse($this->created_at)->format("d/M/Y , H:i");
    }
}
