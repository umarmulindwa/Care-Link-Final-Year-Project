<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupportResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('support_responses');

        Schema::create('support_responses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('support_request_id');
            $table->string('name');
            $table->string('email');
            $table->string('title');
            $table->longText('body');
            $table->string('filename')->nullable();
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
        Schema::dropIfExists('support_responses');
    }
}
