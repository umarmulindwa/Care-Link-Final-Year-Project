<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryPayment extends Model
{
    use HasFactory;

    public function __construct(array $attributes = array())
    {

        parent::__construct($attributes);
            $this->setConnection('finance_connection');

    }

    protected $fillable = [
        "group_id",
        "bank_id",
        "vendor_id",
        "no_of_beneficiaries_approved",
        "no_of_beneficiaries_verified",
        "no_of_beneficiaries_overall",
        "name_of_the_staff",
        "start",
        "end",
        "State_AA",
        "County",
        "Health_Facility",
        "Phone",
        "staff_id",
        "amount_to_be_paid",
        "amount_date_to_be_paid",
        "actual_amount_paid",
        "actual_amount_date_paid",
        "actual_amount_paid_verified",
        "actual_amount_date_paid_verified",
        "payment_reference",
        "payment_status",
        "is_sampled",
    ];


    public function group(){
        return $this->belongsTo(BeneficiaryGroup::class,'group_id','id');
    }

    // public function bank(){
    //     return $this->belongsTo(Bank::class,'bank_id','id');
    // }

    public function beneficiaries(){
        return $this->hasMany(Beneficiary::class,'bank_id','bank_id');
    }
}
