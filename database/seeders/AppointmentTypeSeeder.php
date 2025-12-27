<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            "Continuing", "Fixed Term", "Permanent"
        ];

        \App\Models\AppointmentType::truncate();
        foreach ($types as $type) {
            \App\Models\AppointmentType::create(["name" => $type]);
        }
    }
}
