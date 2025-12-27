<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIPLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('i_p_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('ip_profile_id');
            $table->string('icon');
            $table->string('color');
            $table->string('title');
            $table->string('type');
            $table->unsignedInteger('reference_id')->nullable();
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
        Schema::dropIfExists('i_p_logs');
    }
}
