<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganisationUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $org = [
            "Off of the UNICEF Rep, Lilongwe", "Operations Section, Lilongwe", "Programme Section, Lilongwe"
        ];

        \App\Models\OrganisationUnit::truncate();
        foreach ($org as $val) {
            \App\Models\OrganisationUnit::create([
                "name" => $val
            ]);
        }
    }
}
