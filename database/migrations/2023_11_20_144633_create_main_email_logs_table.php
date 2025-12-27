<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('main_email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('to', 100)->nullable();
            $table->string('subject', 300)->nullable();
            $table->text('body')->nullable();
            $table->string('reference_code', 100)->nullable();
            $table->string('submitted_by', 100)->nullable();
            $table->dateTime('read_at')->nullable();
            $table->boolean('is_sent');
            $table->string('carbon_copy')->nullable();
            $table->boolean('is_reminder');
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_email_logs');
    }
};
