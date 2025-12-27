<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdministrationEmailLogs extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'to',
        'subject',
        'body',
        'reference_code',
        'submitted_by',
        'read_at',
        'is_sent',
        'carbon_copy',
        'is_reminder'
    ];

    public function updateCode(string $type = 'ST')
    {
        if ($this->reference_code == NULL) {
            $this->update([
                "reference_code" => $this->generateRefCode(),
                "is_sent" => 0
            ]);
        }

        return $this;
    }

    private function generateRefNumber()
    {
        $today = date('Ymd', strtotime($this->created_at));
        if (strlen($this->id) >= 4) {
            return "UN-EML-" . $today . "-" . $this->id;
        }

        $len = strlen($this->id);
        $randomNumberLen = 4 - $len;
        $extras = ['', '0', '00', '000', '0000'];

        return "UN-EML-" . $today . "-" . $extras[$randomNumberLen] . $this->id;
    }

    private function generateRefCode()
    {
        return implode('-', str_split(substr(strtolower(md5(microtime() . rand(1000, 9999))), 0, 30), 4));
    }
}
