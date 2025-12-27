<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // admin accounts
        $section =  Section::updateOrCreate(['name' => "Technical Support"], ['name' => "Technical Support"],);

        $adminOne = \App\Models\User::updateOrCreate(
            ["id" => 1],
            [
                "name" => "ADMIN",
                "email" => "info@trailanalytics.com",
                "user_type" => "unicef",
                "active_directory" => "info",
                'email_verified_at' => date("Y-m-d H:i:s"),
                "password" => bcrypt("@admin#123@"),
                'api_token' => Str::random(60),
            ]
        );

        $adminRoles = Role::whereName("Super Admin")->get()->pluck('name')->toArray();

        $adminOne->syncRoles($adminRoles);

        $adminTwoStaff =  \App\Models\StaffProfile::updateOrCreate(
            ['email' => $adminOne->email],
            [
                "personal_id" => 1021109,
                "index_number" => 1021109,
                "name" => $adminOne->name,
                "email" => $adminOne->email,
                "staff_type" => 'official',
                "position_text" => 'Technical Support',
                "position_id" => 110022,
                "section" => $section->id,
                "organisation_unit" => 2,
                "grade" => null,
                "reports_to" => 42105,
                "category" => 1,
                "gender" => 'Male',
                "appointment_type" => 3,
                "date_began_working_with_unicef" => '2023-04-01',
                "date_began_working_with_unicef_country_level" => '2023-04-01',
                "date_contract_end" => '2025-04-01',
                "is_imported" => 1,
                "marital_status" => 'Other',
                "phone" => '0772426242',
                "phone_whatsapp" => null,
                "address" => null,
                "lat" => null,
                "lng" => null,
                "pillar" => null,
                "extension_id" => null,
                "orientation_status" => 1,
                "orientation_stage" => 0,
                "role" => null,
                'exited_unicef' => 0,
            ]
        );


        $adminTwo = \App\Models\User::updateOrCreate(
            ["id" => 2],
            [
                "name" => "Tech Trail Analytics LTD",
                "email" => "tech@trailanalytics.com",
                "user_type" => "unicef",
                "active_directory" => "tech",
                'email_verified_at' => date("Y-m-d H:i:s"),
                "password" => bcrypt("unicef#8291"),
                'api_token' => Str::random(60),
            ]
        );

        $adminTwo->syncRoles($adminRoles);


        $adminTwoStaff =  \App\Models\StaffProfile::updateOrCreate(
            ['email' => $adminTwo->email],
            [
                "personal_id" => 1021109,
                "index_number" => 1021109,
                "name" => 'Trail Analytics Technical Support',
                "email" => $adminTwo->email,
                "staff_type" => 'official',
                "position_text" => 'Technical Support',
                "position_id" => 110022,
                "section" =>  $section->id,
                "organisation_unit" => 2,
                "grade" => null,
                "reports_to" => 42105,
                "category" => 1,
                "gender" => 'Male',
                "appointment_type" => 3,
                "date_began_working_with_unicef" => '2023-04-01',
                "date_began_working_with_unicef_country_level" => '2023-04-01',
                "date_contract_end" => '2025-04-01',
                "is_imported" => 1,
                "marital_status" => 'Other',
                "phone" => '0772426242',
                "phone_whatsapp" => null,
                "address" => null,
                "lat" => null,
                "lng" => null,
                "pillar" => null,
                "extension_id" => null,
                "orientation_status" => 1,
                "orientation_stage" => 0,
                "role" => null,
                'exited_unicef' => 0,
            ]
        );
    }
}
