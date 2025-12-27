<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStaffTypeToStaffProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $comment = ' official,\n volunteer,\n intern';
            $table->string("staff_type",20)->default("official")->comment($comment)->index()->after('position_text');
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
            $table->dropColumn('staff_type');
        });
    }
}
