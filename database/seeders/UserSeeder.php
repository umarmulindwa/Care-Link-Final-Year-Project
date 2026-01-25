<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // admin accounts

        $adminOne = \App\Models\User::updateOrCreate(
            ["id" => 1],
            [
                "name" => "ADMIN",
                "email" => "admin@carelink.com",
                "user_type" => "unicef",
                "active_directory" => "info",
                'email_verified_at' => date("Y-m-d H:i:s"),
                "password" => bcrypt("@admin#123@"),
                'api_token' => Str::random(60),
            ]
        );

        $adminRoles = Role::whereName("Super Admin")->get()->pluck('name')->toArray();

        $adminOne->syncRoles($adminRoles);

        $adminOne->givePermissionTo('s_admin');


    }
}
