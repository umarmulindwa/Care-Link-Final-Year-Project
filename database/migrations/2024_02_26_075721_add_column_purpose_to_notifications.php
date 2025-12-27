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
        Schema::table('notifications', function (Blueprint $table) {
            //
            $table->string("purpose", 80)->nullable()->after('title');
            $table->index('purpose', 'purpose_index');
            $table->index('title', 'title_index');
            $table->index('status', 'status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            //

            $table->dropIndex('purpose_index');
            $table->dropIndex('title_index');
            $table->dropIndex('status_index');
            $table->dropColumn('purpose');
        });
    }
};
