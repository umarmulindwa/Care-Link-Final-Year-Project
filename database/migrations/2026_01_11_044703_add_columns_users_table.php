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
        Schema::table('users', function (Blueprint $table) {
            $table->string('patientGender')->nullable();
            $table->string('patientLocation')->nullable();
            $table->string('patientContact')->nullable();
            $table->string('patientDOB')->nullable();
            $table->string('hospital')->nullable();
            $table->string('hospitalLocation')->nullable();
            $table->string('department')->nullable();
            $table->string('user_type')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['patientGender', 'patientLocation', 'patientContact', 'patientDOB', 'hospital', 'hospitalLocation', 'department','user_type']);
        });
    }
};
