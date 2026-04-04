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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

         

            $table->foreignId('appointment_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Record details
            $table->enum('record_type', [
                'general',
                'lab',
                'prescription',
                'treatment'
            ])->default('general');

            $table->text('description')->nullable();

            // Store as JSON arrays
            $table->json('lab_results')->nullable();
            $table->json('medications_prescribed')->nullable();

            $table->timestamp('record_date')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
