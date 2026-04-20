<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PrivateChat extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'to_user_id',
        'title',
        'message',
        'sent_by_name',
        'sent_by_email',
        'to_name',
        'to_email',
    ];
    protected $appends = ['files'];

    public function getFilesAttribute()
    {
        return $this->getMedia('chat_files');
    }
}
