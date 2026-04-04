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
        Schema::create('appointments', function (Blueprint $table) {
              $table->id();

            // Relationships
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Appointment details
            $table->dateTime('appointment_date');

            $table->enum('status', [
                'scheduled',
                'completed',
                'cancelled',
                'no_show'
            ])->default('scheduled');

            $table->text('reason')->nullable();
            $table->string('clinic_location')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
