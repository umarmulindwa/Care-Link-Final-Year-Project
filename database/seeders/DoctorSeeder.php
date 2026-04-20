<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;


class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          /**
         * Make a User a doctor
         * Note: A Super Admin will have all system permissions and they will be able to add and assign roles.
         */

        $userEmail = "doctor@gmail.com";

        $user = User::where('email', $userEmail)->first();

        $user->givePermissionTo('doctor');
    }
}
