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
        Schema::create('medication_deliveries', function (Blueprint $table) {
            $table->id();

            // Link to medication request
            $table->foreignId('request_id')
                ->constrained('medication_requests')
                ->cascadeOnDelete();

            // Delivery tracking
            $table->enum('delivery_status', [
                'pending',
                'dispatched',
                'in_transit',
                'delivered',
                'failed'
            ])->default('pending');

            $table->timestamp('delivery_date')->nullable();

            // Logistics info
            $table->string('delivered_by')->nullable(); // courier name
            $table->string('tracking_number')->nullable();

            // Extra notes (e.g. "patient not home")
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_deliveries');
    }
};
