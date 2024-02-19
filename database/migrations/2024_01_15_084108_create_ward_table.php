<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('ward', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('district_id');
            $table->timestamps();

            $table->foreign('district_id')
                ->references('id')
                ->on('district')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ward');
    }
};
