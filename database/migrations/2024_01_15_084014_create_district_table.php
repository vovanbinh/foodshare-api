<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('district', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('province_id');
            $table->timestamps();
            $table->foreign('province_id')
                ->references('id')
                ->on('province')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('district');
    }
};
