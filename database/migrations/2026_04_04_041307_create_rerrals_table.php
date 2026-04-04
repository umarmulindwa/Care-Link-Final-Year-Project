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
        Schema::create('rerrals', function (Blueprint $table) {
              $table->id();

            // Relationships
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('from_doctor_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('to_hospital_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Referral details
            $table->text('reason')->nullable();

            $table->enum('status', [
                'pending',
                'accepted',
                'completed',
                'rejected'
            ])->default('pending');

            $table->date('referral_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rerrals');
    }
};
