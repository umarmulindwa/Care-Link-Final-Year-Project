<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        Config::updateOrCreate(['id' => 1], [
            'bsc_email' => 'financegroup@unicef.org',
            'office_location' => 'financegroup@unicef.org',
            'office_name' => 'UNICEF Sudan',
            "platform_name" => "Projects Operations Platform",
            'management_email' => 'adminssd@unicef.org',
            'reminder_days' => 3,
            'leave_reminder_days' => 15,
            'vat_percentage' => 18,
            'parking_booking_max_duration' => 48,
            'identify_call_max_time' => 7,
            'payroll_day' => 10,
            'dashboard_interval' => 23,
            'target_response_time' => 18,
            'all_staff_group_email' => 'info@trailanalytics',
            'consultants_group_email' => 'admin@testingtjkd.org',
            'transport_associate' => 18,
            'quaterly_asset_day' => 18,
            'unicef_tin_number' => '1000216760'
        ]);
    }
}
