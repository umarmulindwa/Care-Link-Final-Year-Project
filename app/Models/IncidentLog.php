<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class IncidentLog extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;

    protected $connection = 'incident_reporting_connection';
  
    protected $table = 'incident_logs';

    protected $appends = ['log_files'];
    // protected $with = ['incident'];

    public function getLogFilesAttribute()
    {
        // $imagesMedia = $this->getMedia('images');
        // foreach ($imagesMedia as $media) {
        //     $m = $media;
        //     $m['url'] = $media->getUrl();
        //     $imagesMediaUrl[] = $m;
        // }

        return $this->getMedia('files');


    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('files');
    }

    public function incident(){
        return $this->belongsTo(Incident::class, 'incident_id');
    }
}
