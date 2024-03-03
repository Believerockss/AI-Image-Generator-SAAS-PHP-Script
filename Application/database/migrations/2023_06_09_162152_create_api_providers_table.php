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
        Schema::create('api_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('alias');
            $table->string('logo');
            $table->text('generator');
            $table->longText('credentials');
            $table->text('instructions')->nullable();
            $table->boolean('support_negative_prompt')->default(false);
            $table->boolean('has_models')->default(false);
            $table->longText('models')->nullable();
            $table->longText('styles')->nullable();
            $table->longText('filters')->nullable();
            $table->integer('max')->default(1);
            $table->boolean('is_default')->default(false);
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('api_providers');
    }
};
