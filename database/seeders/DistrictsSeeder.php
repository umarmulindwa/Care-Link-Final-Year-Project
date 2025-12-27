<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistrictsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $countyOffice = env("VITE_COUNTRY_OFFICE");
            if (is_null($countyOffice)) {
                $this->command->error("No Country Office Identified!");
            }
            $csvFile = fopen("database/seeders/districts.$countyOffice.csv", "r");
            if ($csvFile) {
                $header = fgetcsv($csvFile);
                while (($row = fgetcsv($csvFile)) !== false) {
                    $data = array_combine($header, $row);
                    $city = $data['city'];
                    $lat = $data['lat'];
                    $lng = $data['lng'];
                    District::updateOrCreate(
                        [
                            'name' => $city,
                        ],
                        [
                            'name' => $city,
                            'latitude' => $lat,
                            'longitude' => $lng
                        ]
                    );
                }

                fclose($csvFile);
            } else {
                $this->command->error("Failed to open CSV file");
            }
        } catch (\Throwable $th) {
            $this->command->error($th->getMessage(), $th->getTraceAsString());
        }
    }
}
