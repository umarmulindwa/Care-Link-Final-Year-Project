<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrientationStatusToStaffProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            //Added a column to treck orientation schedule status

            $table->boolean('orientation_status')->nullable()->default(false);
            $table->integer('orientation_stage')->nullable()->default(100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->dropColumn('orientation_status');
            $table->dropColumn('orientation_stage');
        });
    }
}
