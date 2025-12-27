<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $connection = 'mysql';
    protected $fillable = [
        'unicef_tin_number',
        'office_location',
        'office_name',
        'platform_name',
        'management_email',
        'bsc_email',
        'reminder_days', //3PP BSC
        'leave_reminder_days',
        'bsc_cit_approval_email',
        'bsc_task_assigner_email',
        'bsc_petty_cash_custodian',
        'vat_percentage',
        'parking_booking_max_duration',
        'identify_call_max_time',
        'unicef_bank_accounts',
        'payroll_day',
        'dashboard_interval',
        'target_response_time',
        'maximum_records',
        'travel_deadline',
        'all_staff_group_email',
        'consultants_group_email',
        'transport_associate',
        'quaterly_asset_day',
        'update_details_day',
        'call_tree_status_check_day',
        'low_value_theshold',
        'maximum_pettycash',
        'mtn_AirtimeDate_date',
        'zain_AirtimeData_date',
        'passwords_to_check',
        'password_expiry_days',
    ];
}
