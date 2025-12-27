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
        Schema::dropIfExists('support_files');
        Schema::create('support_files', function (Blueprint $table) {
            $table->id();
            $table->string('filepath',150);
            $table->bigInteger('support_responses_id');
            $table->bigInteger('support_requests_id');
            $table->string('submittedBy',100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_files');
    }
};
