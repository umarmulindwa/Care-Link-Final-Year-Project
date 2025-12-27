<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MobileUpload extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        "id", "version", "comment", "downloads", "file_path", "size"
    ];

    protected $appends = [
        "date_formatted",
        "apk_url",
    ];



    public function getApKUrlAttribute()
    {

        return $this->getMedia('mobileuploads')->last() === null ? null : $this->getMedia('mobileuploads')->last()->getFullUrl();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('mobileuploads');
    }

    public function getDateFormattedAttribute()
    {
        return Carbon::parse($this->created_at)->format("d/M/Y , H:i");
    }
}
