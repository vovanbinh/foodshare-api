<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('images_food', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('food_id');
            $table->string('image_url');
            $table->timestamps();
            $table->foreign('food_id')->references('id')->on('foods');
        });
    }
    public function down()
    {
        Schema::dropIfExists('images_food');
    }
};
