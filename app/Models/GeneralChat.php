<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class GeneralChat extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'sent_by_name',
        'sent_by_email',
    ];
    protected $appends = ['files'];

    public function getFilesAttribute()
    {
        return $this->getMedia('chat_files');
    }
}
