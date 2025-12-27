<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
//un_id:"",
//un_id_expiry:"",
//diplomatic_passport:"",
//diplomatic_passport_expiry:"",
//national_passport:"",
//national_passport_expiry:"",
//visa:"",
//visa_expiry:"",
//visa_type:"",
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('staff_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_profile_id')->constrained()->cascadeOnDelete();
            $table->string('un_id')->nullable();
            $table->timestamp('un_id_expiry')->nullable();
            $table->string('diplomatic_passport')->nullable();
            $table->timestamp('diplomatic_passport_expiry')->nullable();
            $table->string('national_passport')->nullable();
            $table->timestamp('national_passport_expiry')->nullable();
            $table->string('visa')->nullable();
            $table->timestamp('visa_expiry')->nullable();
            $table->string('visa_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_identities');
    }
};
