<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoView extends Model
{

    protected $fillable = [
        "video_id",
        "user_id"
    ];
}
