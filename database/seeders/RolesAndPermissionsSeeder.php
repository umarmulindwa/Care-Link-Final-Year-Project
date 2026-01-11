<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        //System Permissions
        /**
         * NOTE:
         * Add new permission at the end of the array
         */
        $permissions = [
            /**
             * NOTE:
             * In order to Avoid duplicate permissions, please check whether the permission you want to add already exists
             * if not, add a comment describing that permission.
             */
            'doctor',
            'patient',
            'admin', // permission for a administration staff
            's_admin', // permission for a super Admin
        ];

        //current permissions
        $activePermissions = Permission::select('name')->get()->toArray();
        $tempPermArray = [];
        foreach ($activePermissions as $activePermission) {
            array_push($tempPermArray, $activePermission['name']);
        }

        //re-create permissions from the array
        foreach ($permissions as $permission) {
            if (!in_array($permission, $tempPermArray)) {
                Permission::create(['name' => $permission], ['name' => $permission]);
            }
        }
    }
}
