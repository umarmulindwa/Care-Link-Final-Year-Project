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
        Schema::create('medication_requests', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('medication_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Request details
            $table->enum('request_type', ['refill', 'new']);
            $table->enum('status', ['pending', 'approved', 'rejected', 'delivered'])
                  ->default('pending');

            // Tracking
            $table->timestamp('requested_at')->nullable();

            // Approved by (doctor/admin user)
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Delivery
            $table->text('delivery_address')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_requests');
    }
};
