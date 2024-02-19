<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('detail_notifice_sub', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notifice_sub_id');
            $table->unsignedBigInteger('sub_id');
            $table->unsignedBigInteger('food_id');
            $table->text('message');
            $table->boolean('is_read');
            $table->string('type');
            $table->timestamps();

            // Define foreign keys
            $table->foreign('notifice_sub_id')->references('id')->on('notifice_sub');
            $table->foreign('food_id')->references('id')->on('foods');
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_notifice_sub');
    }
};
