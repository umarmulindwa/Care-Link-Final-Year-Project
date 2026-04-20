<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;


class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          /**
         * Make a User a patient
         * Note: A Patient will have all system permissions and they will be able to add and assign roles.
         */

        $userEmail = "patient@gmail.com";

        $user = User::where('email', $userEmail)->first();

        $user->givePermissionTo('patient');
    }
}
