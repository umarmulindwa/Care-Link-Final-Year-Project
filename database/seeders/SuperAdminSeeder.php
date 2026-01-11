<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    /**
     * NOTE: Before running this seeder first run the RolesAndPermissionsSeeder seeder 
     * that seeds the s_admin permission
     */
    public function run(): void
    {
        /**
         * Make a User a Super Admin
         * Note: A Super Admin will have all system permissions and they will be able to add and assign roles.
         */

        $userEmail = "umar@gmail.com";

        $user = User::where('email', $userEmail)->first();

        $user->givePermissionTo('s_admin');

    }
}
