<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique(); // Tên đăng nhập, duy nhất
            $table->string('email')->unique();
            $table->string('password');
            $table->string('full_name');
            $table->string('image');
            $table->string('address')->nullable();
            $table->string('phone_number')->nullable();
            $table->date('birthdate')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('bio')->nullable();
            $table->integer('role')->nullable();
            $table->string('verification_code')->nullable();
            $table->string('forgot_password_code')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('users');
    }
};
