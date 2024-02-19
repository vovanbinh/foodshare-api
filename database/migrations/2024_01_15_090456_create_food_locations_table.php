<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('food_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->timestamp('time');
            $table->string('image');
            $table->text('address');
            $table->string('contact_person');
            $table->string('status');
            $table->string('contact_number');
            $table->unsignedBigInteger('province_id');
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('ward_id');
            $table->timestamps();
            // Define foreign keys
            $table->foreign('province_id')->references('id')->on('province');
            $table->foreign('district_id')->references('id')->on('district');
            $table->foreign('ward_id')->references('id')->on('ward');
        });
    }
    public function down()
    {
        Schema::dropIfExists('food_locations');
    }
};
