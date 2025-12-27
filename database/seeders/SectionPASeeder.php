<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\StaffProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use mysql_xdevapi\Exception;
use Spatie\Permission\Models\Permission;;

class SectionPASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $sections = Section::query()->get();

        foreach ($sections as $section) {
            $section_as_array = explode(' ', $section->name);

            if ($section_as_array) {
                $section_prefix = strtolower($section_as_array[0]);
                $permission = "pa_$section_prefix";
                $description = "PA Of $section->name";


                if (!Permission::where('name', $permission)->exists()) {
                    Permission::create(['name' => $permission, 'description' => $description]);
                }
            }
        }
    }
}
