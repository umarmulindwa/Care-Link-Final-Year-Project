<?php

namespace Database\Seeders;

use App\Models\FieldOffice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FieldOfficesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $field_offices = [
            "Bentiu",
            "Malakal",
            "Rumbek",
            "Wau",
            "Yambio",
            "Pibor",
            "Bor",
            "Renk"
        ];

        foreach($field_offices as $office){
            FieldOffice::query()->updateOrCreate(['name' => $office]);
        }
    }
}
