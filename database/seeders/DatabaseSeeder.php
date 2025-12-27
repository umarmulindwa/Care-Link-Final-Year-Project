<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        //presets
        $this->call(SectionDataSeeder::class);
        $this->call(PillarSeeder::class);
        $this->call(OrganisationUnitSeeder::class);
        $this->call(GradeSeeder::class);
        $this->call(StaffCategorySeeder::class);
        $this->call(AppointmentTypeSeeder::class);
        // permission and roles
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(AccessControlLogicSeeder::class);
        // default user account
        $this->call(UserSeeder::class);
        $this->call(SectionChiefsPermissionsSeeder::class);
        // UnicefStaffSeeder
        // $this->call(UnicefStaffSeeder::class);
        // ServiceProviderSeeder
        // $this->call(ServiceProviderSeeder::class);
        $this->call(ConfigurationSeeder::class);
    }
}
