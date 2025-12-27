<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        "video_title",
        "video_path",
        "embed_url",
        "location",
        "about_the_video",
        "uploaded_by"
    ];

    public function views()
    {
        return $this->hasMany(VideoView::class);
    }
}
