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
        Schema::create('hospitals', function (Blueprint $table) {
                $table->id();

            // Basic details
            $table->string('name');
            $table->string('location')->nullable(); // e.g. Kampala
            $table->text('address')->nullable();

            // Contact info
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // Type of hospital
            $table->enum('type', [
                'public',
                'private',
                'clinic',
                'specialized'
            ])->default('clinic');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};
