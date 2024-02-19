<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('contact_information');
            $table->unsignedBigInteger('province_id');
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('ward_id');
            $table->string('home_number');
            $table->string('lat');
            $table->string('lon');
            $table->text('display_name');
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default('non-default');
            $table->timestamps();
            $table->foreign('province_id')->references('id')->on('province');
            $table->foreign('district_id')->references('id')->on('district');
            $table->foreign('ward_id')->references('id')->on('ward');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
};
