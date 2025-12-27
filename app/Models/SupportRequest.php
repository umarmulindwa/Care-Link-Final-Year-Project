<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Utils;
use Illuminate\Support\Facades\DB;


class SupportRequest extends Model
{
    use Utils;
    protected $connection = 'support_db_connection';

    protected $table = 'support_requests';

    protected $fillable = ['name', 'email', 'issue_category', 'issue', 'description', 'filename', 'closed', 'created_at', 'updated_at'];


    public function responses()
    {
        return $this->hasMany('App\Models\SupportResponse');
    }
    public function files()
    {
        return $this->hasMany(SupportFiles::class, 'support_requests_id');
    }


    static function reports($dt)
    {
        $emails = [];

        switch ($dt) {
            case '30':
                $dates = array_reverse(Utils::datesPast(30));
                foreach ($dates as $date) {
                    $emails[] = count(self::where(DB::raw('DATE(updated_at)'), $date)->get());
                }

                return array(
                    'data' => $emails,
                    'labels' => collect($dates)->map(function ($d) {
                        return date('d/M', strtotime($d));
                    })->all(),
                    'message' => [
                        'in the last 30 days',
                        'from ' . date("d/M", strtotime($dates[0])) . ' - ' . date("d/M", strtotime($dates[29]))
                    ]
                );

            case '6':
                $months = array_reverse(Utils::monthsPast(6));
                foreach ($months as $month) {
                    $emails[] = count(self::where(DB::raw('MONTH(updated_at)'), date("m", strtotime($month)))->where(DB::raw('YEAR(updated_at)'), date("Y", strtotime($month)))->get());
                }
                return array(
                    'data' => $emails,
                    'labels' => collect($months)->map(function ($m) {
                        return date('F', strtotime($m));
                    })->all(),
                    'message' => [
                        'in the last 6 months',
                        'from ' . date("M/y", strtotime($month)) . ' - ' . date("M/y", strtotime($month))
                    ]
                );

            default:
                $months = array_reverse(Utils::monthsPast(12));
                foreach ($months as $month) {
                    $emails[] = count(self::where(DB::raw('MONTH(updated_at)'), date("m", strtotime($month)))->where(DB::raw('YEAR(updated_at)'), date("Y", strtotime($month)))->get());
                }
                return array(
                    'data' => $emails,
                    'labels' => collect($months)->map(function ($m) {
                        return date('M/y', strtotime($m));
                    })->all(),
                    'message' => [
                        'in the last 12 months',
                        'from ' . date("M/y", strtotime($month)) . ' - ' . date("M/y", strtotime($month))
                    ]
                );
        }
    }

}
