<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('food_transaction_id');
            $table->integer('rating');
            $table->text('review')->nullable();
            $table->timestamps();
            // Define foreign key
            $table->foreign('food_transaction_id')->references('id')->on('food_transactions');
        });
    }
    public function down()
    {
        Schema::dropIfExists('rates');
    }
};
