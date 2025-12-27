<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentDirectory = __DIR__;


        $sqlFileName = 'rooms.sql';
        $sqlFilePath = $currentDirectory . DIRECTORY_SEPARATOR . $sqlFileName;
        $sqlContent = file_get_contents($sqlFilePath);

        DB::connection('mysql')->unprepared($sqlContent);
    }
}
