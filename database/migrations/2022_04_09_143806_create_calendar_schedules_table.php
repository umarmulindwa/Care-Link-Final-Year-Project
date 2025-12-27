<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('staff_profile_id'); //me->id //another->id
            $table->string('platform')->nullable(); //VEHICLE_REQUEST
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('scheduled_date');
            $table->time('scheduled_time_start')->nullable();
            $table->time('scheduled_time_end')->nullable();
            $table->string('schedule_uuid'); //this field will be used to group schedules (know who else has the same schedule)
            $table->unsignedInteger('created_by_id'); //me->id //me->id
            $table->text('metadata')->nullable();
            $table->boolean('is_accepted')->default(false);
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
        Schema::dropIfExists('calendar_schedules');
    }
}
