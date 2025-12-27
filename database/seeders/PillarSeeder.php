<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PillarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $pillars = [
            "Child-friendly, inclusive and resilient communities", "Early Childhood", "Operational Effectiveness", "Programme Effectiveness", "School-age children"
        ];

        \App\Models\Pillar::truncate();
        foreach ($pillars as $pillar) {
            \App\Models\Pillar::create([
                "name" => $pillar
            ]);
        }
    }
}
