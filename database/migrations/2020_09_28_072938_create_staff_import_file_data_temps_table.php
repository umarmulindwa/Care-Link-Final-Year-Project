<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffImportFileDataTempsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_import_file_data_temps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("user_id");
            $table->string('personal_id')->nullable();
            $table->string('index_number')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('position')->nullable();
            $table->string('position_id')->nullable();
            $table->string('section')->nullable();
            $table->string('organisation_unit')->nullable();
            $table->string('grade')->nullable();
            $table->string('reports_to')->nullable();
            $table->string('category')->nullable();
            $table->string('gender')->nullable();
            $table->string('appointment_type')->nullable();
            $table->date('date_began_working_with_unicef')->nullable();
            $table->date('date_began_working_with_unicef_country_level')->nullable();
            $table->date('date_contract_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_import_file_data_temps');
    }
}
