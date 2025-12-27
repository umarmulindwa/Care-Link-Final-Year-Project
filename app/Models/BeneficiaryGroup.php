<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Spatie\MediaLibrary\HasMedia;
// use Spatie\MediaLibrary\InteractsWithMedia;

class BeneficiaryGroup extends Model
{
    use HasFactory;

    public function __construct(array $attributes = array())
    {

        parent::__construct($attributes);
            $this->setConnection('finance_connection');

    }

    protected $fillable = [
        "vendor_id",
        "financial_institute_id",
        "reference_code",
        "title",
        "no_of_beneficiaries",
        "no_of_beneficiaries_approved",
        "no_of_beneficiaries_paid",
        "status",
        "date_submitted",
        "approved_date",
        "invoice_date",
        "invoice_closed_date"
    ];

    public function beneficiaries(){
        return $this->hasMany(Beneficiary::class,'group_id','id');
    }

    public function provider(){
        return $this->belongsTo(ServiceProvider::class,'vendor_id','id');
    }

    public function fsp(){
        return $this->belongsTo(ServiceProvider::class,'financial_institute_id','id');
    }



}
