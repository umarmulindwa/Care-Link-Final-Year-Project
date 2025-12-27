<?php

namespace Database\Seeders;

use App\Actions\Fortify\CreateNewUser;
use App\Jobs\SeedUnicefStaffJob;
use App\Models\Section;
use App\Models\ServiceProvider;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Session;


class UnicefStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $info = NULL;
        try {

            set_time_limit(0);
            $zip = new \ZipArchive;
            Schema::disableForeignKeyConstraints();

            // Your database operations go here..

            DB::statement("truncate table sessions;");
            User::truncate();
            PersonalAccessToken::truncate();
            // Session::truncate();
            StaffProfile::truncate();
            ServiceProvider::truncate();
            Section::truncate();
            try {
                //code...
                $this->loadAdminAccounts();
                $this->loadVendors();
            } catch (\Throwable $th) {
                $this->command->error("Admin Operation Failed: " . $th->getMessage(), ['trace' => $th->getTrace()]);
            }
            $conn = 'finance_connection';

            if (env('FINANCE_CHANNEL') == 2) {
                $conn = 'finance_connection';
            } else {
                $conn = 'bsc_connection';
            }

            // unicefMalawiStaff2022
            $filename = config("app.stafflist");
            $zipFilePath = "./database/seeders/$filename.zip";
            $csvFileName = "$filename.csv";

            if ($zip->open($zipFilePath) === true) {
                // extract zipped file
                $zip->extractTo(storage_path('app/staffprofiles_csv_temp'));
                $zip->close();
                // Read the CSV file
                $csvFile = fopen(storage_path('app/staffprofiles_csv_temp/' . $csvFileName), 'r');
                if ($csvFile) {
                    $header = fgetcsv($csvFile); // Assuming the first row contains headers

                    while (($row = fgetcsv($csvFile)) !== false) {
                        // Populate the database with the data
                        // DB::connection($conn)->table('unicefMalawiStaff2022')->insert(array_combine($header,$row));
                        $data = array_combine($header, $row);
                        $email =  $data['Email'];
                        if (!preg_match("/^\S+@\S+\.\S+$/", $email)) {
                            continue;
                        }
                        $section = null;
                        if (isset($data['Section']) && !($data['Section'] == null || $data['Section'] == "")) {
                            $section =   Section::updateOrCreate([
                                'name' => $data['Section']
                            ], [
                                'name' => $data['Section']
                            ]);
                        }

                        $staff = $data;
                        $newUser = StaffProfile::updateOrCreate(["email" =>  $data['Email'],], [
                            "personal_id" => isset($staff['Personnel ID']) ? $staff['Personnel ID'] : null,
                            "index_number" => isset($staff['Index Number']) ? $staff['Index Number'] : null,
                            "name" =>  isset($staff['Names']) ? iconv('UTF-8', 'UTF-8//IGNORE', $staff['Names']) : null,
                            "email" => isset($staff['Email']) ? $staff['Email'] : null,
                            "staff_type" => isset($staff['Staff Type']) ? $staff['Staff Type'] : null,
                            "position_text" => isset($staff['Position']) ? $staff['Position'] : null,
                            "position_id" => isset($staff['Position ID']) ? $staff['Position ID'] : null,
                            "section" => $section  != null ? $section->id : null,
                            "organisation_unit" => isset($staff['Organization Unit']) ? $staff['Organization Unit'] : null,
                            "reports_to" => isset($staff['Reports to']) ? $staff['Reports to'] : null,
                            "category" => isset($staff['Category']) ? $staff['Category'] : null,
                            "gender" => isset($staff['Gender']) ? $staff['Gender'] : null,
                            "appointment_type" => isset($staff['Appointment Type']) ? $staff['Appointment Type'] : null,
                            "date_began_working_with_unicef" => isset($staff['Date began with UNICEF (as a whole)']) ? $staff['Date began with UNICEF (as a whole)'] : null,
                            "date_began_working_with_unicef_country_level" => isset($staff['Date began with UNICEF Malawi']) ? $staff['Date began with UNICEF Malawi'] : null,
                            "date_contract_end" => isset($staff['Date contract with UNICEF Malawi ends']) ? $staff['Date contract with UNICEF Malawi ends'] : null,
                            "is_imported" => true,
                            "marital_status" => isset($staff['MaritalStatus']) ? $staff['MaritalStatus'] : null,
                            "phone" => isset($staff['Phone #']) ? $staff['Phone #'] : null,
                            "phone_whatsapp" => isset($staff['Whatsapp #']) ? $staff['Whatsapp #'] : null,
                            "address" => isset($staff['Address']) ? $staff['Address'] : null,
                            "pillar" => isset($staff['Pillar']) ? $staff['Pillar'] : null,
                        ]);

                        try {
                            //adding staff to the users' table and generating a random password

                            // $randomPassword = "unicef#" . mt_rand(1000, 9999);
                            $randomPassword = "unicef#93204";

                            $createUserObj = new CreateNewUser;

                            $createUserObj->create([
                                'name' => $newUser->name,
                                'email' => $newUser->email,
                                'password' => $randomPassword,
                                'password_confirmation' => $randomPassword,
                                'terms' => true,
                                'newStaff' => true,
                            ]);
                            $userAccount = User::where('email', $newUser->email)->first();
                            $userAccount->email_verified_at = date("Y-m-d H:i:s");
                            $userAccount->user_type = 'unicef';
                            $userAccount->api_token = Str::random(60);
                            $userAccount->save();
                        } catch (\Exception $ex) {
                            $this->command->error("Failed to request: " . $ex->getMessage(), ['error' => $ex->getTrace()]);
                        }
                    }

                    fclose($csvFile);
                } else {
                    $this->command->error("Failed to open CSV file");
                }
            } else {
                $this->command->error("Failed to open ZIP archive file");
            }
            $this->command->info("Done!");
            Schema::enableForeignKeyConstraints();
        } catch (\Throwable $th) {
            Schema::enableForeignKeyConstraints();

            $this->command->error("Operation Failed: " . $th->getMessage(), ['data' => $info, 'trace' => $th->getTrace()]);
            Log::error("Operation Failed: " . $th->getMessage(), ['data' => $info, 'trace' => $th->getTrace()]);
        }
    }

    private function loadVendors()
    {
        try {
            $zip = new \ZipArchive;

            $conn = 'finance_connection';

            if (env('FINANCE_CHANNEL') == 2) {
                $conn = 'finance_connection';
            } else {
                $conn = 'bsc_connection';
            }

            $zipFilePath = "./database/seeders/service_providers.zip";
            $csvFileName = "service_providers.csv";

            if ($zip->open($zipFilePath) === true) {
                // extract zipped file
                $zip->extractTo(storage_path('app/service_providers'));
                $zip->close();
                // Read the CSV file
                $csvFile = fopen(storage_path('app/service_providers/' . $csvFileName), 'r');
                if ($csvFile) {
                    $header = fgetcsv($csvFile); // Assuming the first row contains headers

                    while (($row = fgetcsv($csvFile)) !== false) {
                        $data = array_combine($header, $row);
                        $email =  $data['Email'];
                        if (!preg_match("/^\S+@\S+\.\S+$/", $email)) {
                            continue;
                        }

                        $randomPassword = "Testing@123";

                        $serviceProvider = ServiceProvider::updateOrCreate([
                            "email" => $data['Email'],
                        ], [
                            "name" => $data['Name'],
                            "email" => $data['Email'],
                            'user_id' => rand(280, 2890),
                            "email" => $data['Email'],
                            "vendor_number" => $data['Vendor Number'],
                        ]);

                        $createUserObj = new CreateNewUser;

                        $createUserObj->create([
                            'name' => $serviceProvider->name,
                            'email' => $serviceProvider->email,
                            'password' => $randomPassword,
                            'password_confirmation' => $randomPassword,
                            'terms' => true,
                            'newStaff' => true,
                        ]);

                        $userAccount = User::where('email', $serviceProvider->email)->first();
                        $userAccount->email_verified_at = date("Y-m-d H:i:s");
                        $userAccount->user_type = 'sp';
                        $userAccount->api_token = Str::random(60);
                        $userAccount->save();
                        $serviceProvider->user_id = $userAccount->id;
                        $serviceProvider->save();
                    }
                } else {
                    $this->command->error("Failed to open CSV file");
                }
            } else {
                $this->command->error("Failed to open ZIP archive file");
            }
        } catch (\Throwable $th) {

            $this->command->error("Operation Failed: " . $th->getMessage(), ['trace' => $th->getTrace()]);
            Log::error("Operation Failed: " . $th->getMessage(), ['trace' => $th->getTrace()]);
        }
    }

    private function loadAdminAccounts()
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
    }
}
