<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class AccessControlLogicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // roles in the system
        $superAdmin =  Role::updateOrCreate(
            [
                "name" => "Super Admin"
            ],
            [
                "name" => "Super Admin",
                "description" => "Super Admin Role has all privileges in the system"
            ]
        );


        $unicefStaff =  Role::updateOrCreate(
            [
                "name" => "Unicef Staff"
            ],
            [
                "name" => "Unicef Staff",
                "description" => "This is the Role given to a UNICEF Staff"
            ]
        );




        $intern =  Role::updateOrCreate(
            [
                "name" => "Intern"
            ],
            [
                "name" => "Intern",
                "description" => "This is the role given to persons undergoing internship"
            ]
        );

        $volunteer =  Role::updateOrCreate(
            [
                "name" => "Volunteer"
            ],
            [
                "name" => "Volunteer",
                "description" => "This is the role given to persons volunteering"
            ]
        );

        $serviceProvider =  Role::updateOrCreate(
            [
                "name" => "Service Provider"
            ],
            [
                "name" => "Service Provider",
                "description" => "This is the role that is given to the service provider"
            ]
        );

        $implementPartner =  Role::updateOrCreate(
            [
                "name" => "Implementing Partner"
            ],
            [
                "name" => "Implementing Partner",
                "description" => "This is a role given to Implementing partners"
            ]
        );


        $unicefFinanceManager =  Role::updateOrCreate(
            [
                "name" => "Finance Manager"
            ],
            [
                "name" => "Finance Manager",
                "description" => "This is the Role given to a Finance Manager"
            ]
        );

        $unicefFinanceManager->syncPermissions(['c_bsc', 'bsc']);

        $unicefFinance =  Role::updateOrCreate(
            [
                "name" => "Finance"
            ],
            [
                "name" => "Finance",
                "description" => "This is the Role given to a Finance Staff"
            ]
        );
        $unicefFinance->syncPermissions(['bsc']);



        $unicefSupply =  Role::updateOrCreate(
            [
                "name" => "Supply"
            ],
            [
                "name" => "Supply",
                "description" => "Role Handling Supply and Logistic tasks"
            ]
        );

        $unicefSupply->syncPermissions(['supply']);


        $unicefSupply =  Role::updateOrCreate(
            [
                "name" => "Administration"
            ],
            [
                "name" => "Administration",
                "description" => "Role Handling Administration tasks"
            ]
        );

        $unicefSupply->syncPermissions(['admin']);




        // give super account all system permissions
        $allPermissions  = Permission::get()->pluck('name')->toArray();
        $superAdmin->syncPermissions($allPermissions);
    }
}
