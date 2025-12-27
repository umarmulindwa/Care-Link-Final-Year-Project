<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grades = [
            "G-2", "G-3", "G-4", "G-5", "G-6", "G-7", "NO-1", "NO-2", "NO-3", "NO-4", "P-2", "P-3", "P-4", "P-5"
        ];

        \App\Models\Grade::truncate();

        foreach ($grades as $grade) {
            \App\Models\Grade::create(["name" => $grade]);
        }
    }
}
