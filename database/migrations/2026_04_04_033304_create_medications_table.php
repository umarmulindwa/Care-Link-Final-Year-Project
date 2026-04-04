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
        Schema::create('medications', function (Blueprint $table) {
                 $table->id();

            // Basic info
            $table->string('name');
            $table->string('type')->nullable(); // e.g. ARV, Antibiotic
            $table->text('description')->nullable();

            // Medical details
            $table->string('dosage')->nullable(); // e.g. "300mg"
            $table->string('manufacturer')->nullable();

            // Inventory (important for your system)
            $table->integer('stock_quantity')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
