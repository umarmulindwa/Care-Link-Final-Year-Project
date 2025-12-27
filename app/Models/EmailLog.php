<?php

namespace App\Models;

use App\Http\Traits\Utils;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class EmailLog extends Model
{
    use Utils;

    protected $fillable = [
        "to", "description", "read_at", "body", "reference_number", "reference_code","is_email_sent","carbon_copy"
        ,"auto_sent_on","sent_check"
    ];

    protected $appends = ['email_link',"copy_mails","email_reciepients"];

    public function getEmailLinkAttribute() {
        return route('shared-email', $this->reference_code);
    }

    public function getEmailReciepientsAttribute(){
       try {
            $text =  $this->to;
            preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $text, $matches);
            if (count($matches[0])>0) {
                return $matches[0];
            }else{
                return [];
            }

       } catch (\Throwable $th) {
            //throw $th;
       }
    }

    public function getCopyMailsAttribute(){
        try {
            $text =  $this->to;
            preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $text, $matches);
            $other_mails= $matches[0];
            if(count($other_mails)>1){
                unset($other_mails[0]);
                if ($this->carbon_copy) {
                    $cc= explode(",",$this->carbon_copy);
                    $cc = collect([$other_mails,$cc])->collapse();
                    return $cc;
                }else{
                     return [];
                }
            }else{
                if ($this->carbon_copy) {
                    return explode(",",$this->carbon_copy);
                }else{
                     return [];
                }
            }

        } catch (\Throwable $th) {
            //throw $th;
        }

    }

    public function updateCode(string $type = 'ST') {
        // TODO Confirm whether this check is necessary in any other code and remove if not (this a necessary check)
        // TODO @Julia Kazibwe
        if($this->reference_number == NULL || $this->reference_code == NULL) {
            $this->update([
                "reference_number" => $this->generateRefNumber(),
                "reference_code" => $this->generateRefCode(),
                "is_email_sent" => "NO"
            ]);
        }

        return $this;
    }

    private function generateRefNumber() {
        $today = date('Ymd', strtotime($this->created_at));
        if(strlen($this->id) >= 4) {
            return "UN-EML-" . $today . "-" . $this->id;
        }

        $len = strlen($this->id);
        $randomNumberLen = 4 - $len;
        $extras = ['', '0', '00', '000', '0000'];

        return "UN-EML-" . $today . "-" . $extras[$randomNumberLen] . $this->id;
    }

    private function generateRefCode() {
        return Uuid::uuid4();
        // return implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 4));
    }

    static function reports($dt) {

        $emails = [];

        switch($dt){
            case '30':
                $dates = array_reverse(Utils::datesPast(30));
                foreach($dates as $date){
                    $emails[] = count(self::where(DB::raw('DATE(updated_at)'), $date)->get());
                }

                return array(
                    'data' => $emails,
                    'labels' => collect($dates)->map(function($d){
                        return date('d/M', strtotime($d));
                    })->all(),
                    'message' => [
                        'in the last 30 days',
                        'from ' . date("d/M", strtotime($dates[0])) . ' - ' . date("d/M", strtotime($dates[29]))
                    ]
                );
            case '6':
                $months = array_reverse(Utils::monthsPast(6));
                foreach($months as $month){
                    $emails[] = count(self::where(DB::raw('MONTH(updated_at)'), date("m", strtotime($month)))->where(DB::raw('YEAR(updated_at)'), date("Y", strtotime($month)))->get());
                }
                return array(
                    'data' => $emails,
                    'labels' => collect($months)->map(function($m){
                        return date('F', strtotime($m));
                    })->all(),
                    'message' => [
                        'in the last 6 months',
                        'from ' . date("M/y", strtotime($month)) . ' - ' . date("M/y", strtotime($month))
                    ]
                );
            default:
                $months = array_reverse(Utils::monthsPast(12));
                foreach($months as $month){
                    $emails[] = count(self::where(DB::raw('MONTH(updated_at)'), date("m", strtotime($month)))->where(DB::raw('YEAR(updated_at)'), date("Y", strtotime($month)))->get());
                }
                return array(
                    'data' => $emails,
                    'labels' => collect($months)->map(function($m){
                        return date('M/y', strtotime($m));
                    })->all(),
                    'message' => [
                        'in the last 12 months',
                        'from ' . date("M/y", strtotime($month)) . ' - ' . date("M/y", strtotime($month))
                    ]
                );
        }
    }

    public function getCreatedAtAttribute($value)
    {
        $date = $this->asDateTime($value);
        return $date->timezone(env('TIMEZONE'));
    }

    public function getUpdatedAtAttribute($value)
    {
        $date = $this->asDateTime($value);
        return $date->timezone(env('TIMEZONE'));
    }
}
