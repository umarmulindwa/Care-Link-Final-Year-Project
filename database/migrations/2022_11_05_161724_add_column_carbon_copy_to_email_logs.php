<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddColumnCarbonCopyToEmailLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('email_logs', function (Blueprint $table) {
            $table->text("carbon_copy")->nullable();
        });
        DB::update("update email_logs set is_email_sent ='YES'");
        DB::update("ALTER TABLE email_logs ALTER is_email_sent SET DEFAULT 'YES'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropColumn("carbon_copy");
        });
    }
}
