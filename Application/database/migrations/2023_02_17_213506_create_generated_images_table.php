<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('generated_images', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('storage_provider_id')->unsigned();
            $table->string('ip_address');
            $table->text('prompt');
            $table->text('negative_prompt')->nullable();
            $table->string('size');
            $table->longText('main');
            $table->longText('thumbnail')->nullable();
            $table->bigInteger('views')->default(0)->unsigned();
            $table->bigInteger('downloads')->default(0)->unsigned();
            $table->boolean('visibility')->default(false);
            $table->boolean('is_viewed')->default(false);
            $table->dateTime('expiry_at')->nullable();
            $table->foreign("user_id")->references("id")->on('users')->onDelete('cascade');
            $table->foreign("storage_provider_id")->references("id")->on('storage_providers')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('generated_images');
    }
};
