<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            "Administration Unit",
            "Finance", // Uganda and South Sudan
            // "Business Support Centre", Malawi
            "Child Protection",
            "Communications",
            "Community Development/Resilience",
            "Deputy Representative",
            "Education/Adolescents",
            "Emergency",
            "HACT",
            "Health",
            "Human Resource",
            "ICT Unit",
            "Innovations",
            "Nutrition",
            "Operations Section",
            "Programme Planning & Monitoring",
            "Representative",
            "Research, Evaluation and KM",
            "Social Policy",
            "Supply Unit",
            "WASH"
        ];

        \App\Models\Section::truncate();
        foreach ($sections as $section) {
            \App\Models\Section::create([
                "name" => $section
            ]);
        }
    }
}
