<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SupportFiles;

class SupportResponse extends Model
{
    protected $connection = 'support_db_connection';

    protected $fillable = ['support_request_id', 'name', 'email', 'title', 'body', 'filename', 'response_by'];

    protected $appends = ['ReponseFiles'];

    public function getReponseFilesAttribute(){
        $files = SupportFiles::where('support_responses_id', $this->id)->get();
        return $files;
    }
}
