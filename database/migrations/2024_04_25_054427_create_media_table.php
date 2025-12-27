<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::connection(env('DB_CONNECTION', 'mysql'))->hasTable("media")) {
            Schema::connection(env('DB_CONNECTION', 'mysql'))->dropIfExists("media");
        }
        Schema::connection(env('DB_CONNECTION', 'mysql'))->dropIfExists('media');
        Schema::connection(env('DB_CONNECTION', 'mysql'))->create('media', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->uuid('uuid')->nullable()->unique()->index();
            $table->string('collection_name')->index();
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk')->index();
            $table->string('conversions_disk')->nullable()->index();
            $table->unsignedBigInteger('size')->index();
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable()->index();

            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(env('DB_CONNECTION', 'mysql'))->dropIfExists('media');
    }
}
