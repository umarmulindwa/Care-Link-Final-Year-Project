<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWBSStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('w_b_s_staff', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('wbs_id');
            $table->string('programme_officer')->nullable();
            $table->string('programme_assistant')->nullable();
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
        Schema::dropIfExists('w_b_s_staff');
    }
}
