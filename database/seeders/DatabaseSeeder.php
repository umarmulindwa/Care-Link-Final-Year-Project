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
        // permission and roles
        $this->call(RolesAndPermissionsSeeder::class);
        // default user account
        $this->call(UserSeeder::class);
    }
}
