<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    public function __construct(array $attributes = array())
    {

        parent::__construct($attributes);

            $this->setConnection('finance_connection');

    }

    protected $fillable = [
        "group_id",
        "uniq_id",
        "national_id",
        "work_id",
        "qualification",
        "state",
        "county",
        "date_of_birth",
        "health_facilityboma",
        "surname",
        "firstname",
        "othername",
        "gender",
        "title",
        "cadre",
        "grade",
        "phone_number",
        "amount_to_pay",
        "chd_officer",
        "date_submission_by_chd_officer",
        "ip_staff",
        "date_verification_by_ip_staff",
        "unicef_fp_for_verification",
        "date_verification_by_unicef_fp",
        "fsp_receiving_the_data",
        "date_of_data_reception_by_fsp",
        "planned_payment_date",
        "payment_status",
        "payment_date",
        "amount_paid",
        "date_of_reconciliation_by_unicef_fp",
        "unicef_fp_doing_the_reconcialiation",
        "comments_on_the_reconciliation",
        "date_of_reimbursement_by_unicef",
        "picture_of_national_id_of_the_staff",
    ];
}
