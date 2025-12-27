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
        if (!Schema::hasColumn('support_responses', 'response_by')) {
            Schema::table('support_responses', function (Blueprint $table) {
                $table->string('response_by', 150)->nullable();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_responses', function (Blueprint $table) {
            $table->dropColumn('response_by');
        });
    }
};
