<?php

namespace Database\Seeders;

use App\Actions\Fortify\CreateNewUser;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ServiceProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
}
