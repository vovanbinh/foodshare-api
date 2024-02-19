<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('description');
            $table->integer('quantity');
            $table->date('expiry_date');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('address_id');
            $table->decimal('price', 10, 2);
            $table->string('status');
            $table->decimal('delivery_fee', 10, 2);
            $table->string('collect_type');
            $table->string('slug');
            $table->string('food_type');
            $table->string('operating_hours');
            $table->string('payment_methods');
            $table->integer('remaining_time_to_accept');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('address_id')->references('id')->on('addresses');
        });
    }
    public function down()
    {
        Schema::dropIfExists('foods');
    }
};
