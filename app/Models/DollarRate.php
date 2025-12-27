<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DollarRate extends Model
{
    use SoftDeletes;

    public  const MANUAL = "Manual";
    public  const UN_TREASURY = "UN Treasury";

    protected $fillable = ["rate", "currency", "source"];

    /**
     * @param $baseCode
     * @return DollarRate[]
     */
    public static function baseCode($baseCode)
    {
        $dollarRates = (new static)::orderBy('created_at', 'DESC')->get();
        // ->filter(function ($value) use ($baseCode) {
        //     return $value["currency"] != $baseCode;
        // });
        return $dollarRates->all();
    }
}
