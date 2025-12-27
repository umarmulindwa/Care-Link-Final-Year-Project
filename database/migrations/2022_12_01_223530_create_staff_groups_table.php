<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_number');
            $table->integer('staff_id')->unqiue;
            $table->integer('group_leader');
            $table->string('call_sign');
            $table->integer('link');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_groups');
    }
}