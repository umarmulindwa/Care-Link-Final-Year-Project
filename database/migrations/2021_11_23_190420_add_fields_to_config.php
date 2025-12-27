<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->string('bsc_petty_cash_custodian')->nullable();
            $table->double('vat_percentage')->default(16.5);
            $table->integer('parking_booking_max_duration')->default(18);
            $table->integer('identify_call_max_time')->default(7);
            $table->json('unicef_bank_accounts')->nullable();
            $table->integer('payroll_day')->default(25);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->dropColumn('bsc_petty_cash_custodian');
            $table->dropColumn('vat_percentage');
            $table->dropColumn('parking_booking_max_duration');
            $table->dropColumn('identify_call_max_time');
            $table->dropColumn('unicef_bank_accounts');
            $table->dropColumn('payroll_day');
        });
    }
}
