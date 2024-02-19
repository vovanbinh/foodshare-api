<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('food_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('food_id');
            $table->unsignedBigInteger('receiver_id');
            $table->integer('quantity_received');
            $table->timestamp('pickup_time');
            $table->string('status');
            $table->boolean('anonymous')->default(0); // 0 is default, 1 is anonymous
            $table->string('receiver_status');
            $table->boolean('is_error_notification');
            $table->string('donor_status');
            $table->timestamps();
            $table->foreign('food_id')->references('id')->on('foods');
            $table->foreign('receiver_id')->references('id')->on('users');
        });
    }
    public function down()
    {
        Schema::dropIfExists('food_transactions');
    }
};
