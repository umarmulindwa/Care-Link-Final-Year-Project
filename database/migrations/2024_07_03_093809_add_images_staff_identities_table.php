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
        Schema::table('staff_identities', function (Blueprint $table) {
            $table->string('un_id_proof')->nullable();
            $table->string('diplomatic_passport_proof')->nullable();
            $table->string('national_passport_proof')->nullable();
            $table->string('visa_proof')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_identities', function (Blueprint $table) {
            $table->dropColumn('un_id_proof');
            $table->dropColumn('diplomatic_passport_proof');
            $table->dropColumn('national_passport_proof');
            $table->dropColumn('visa_proof');
        });
    }
};
