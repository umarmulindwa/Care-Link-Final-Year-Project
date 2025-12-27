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
        Schema::create('searchlogs', function (Blueprint $table) {
            $table->id();
            $table->string('search_text')->index('searchTextIndex');
            $table->string('search_within')->index('searchWithinIndex');
            $table->string('search_by_name')->index('searchByIndex');
            $table->string('search_by_email')->index('searchByEmailIndex');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('searchlogs');
    }
};
