<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterDescriptionColumnInNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            //
            DB::statement('ALTER TABLE `notifications` CHANGE `description` `description` TEXT;');


            DB::statement("ALTER TABLE `notifications` CHANGE `status` `status` ENUM('PENDING', 'READ') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING';");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            //
            DB::statement('ALTER TABLE `notifications` CHANGE `description` `description` varchar(191);');
            DB::statement("ALTER TABLE `notifications` CHANGE `status` `status` ENUM('PENDING', 'READ') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING';");

        });
    }
}
