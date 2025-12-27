<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('personal_id')->nullable();
            $table->string('index_number')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('position_text')->nullable();
            $table->string('position_id')->nullable();
            $table->string('section')->nullable();
            $table->string('pillar')->nullable();
            $table->string('organisation_unit')->nullable();
            $table->string('grade')->nullable();
            $table->string('reports_to')->nullable();
            $table->string('category')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_whatsapp')->nullable();
            $table->string('address')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('appointment_type')->nullable();
            $table->string('date_began_working_with_unicef')->nullable();
            $table->string('date_began_working_with_unicef_country_level')->nullable();
            $table->string('date_contract_end')->nullable();
            $table->boolean('is_imported')->default(false);
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
        Schema::dropIfExists('staff_profiles');
    }
}
