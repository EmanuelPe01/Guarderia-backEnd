<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingestions', function (Blueprint $table) {
            $table->id();
            $table->string('gratification');
            $table->unsignedBigInteger('id_child')->nullable();
            $table->foreign('id_child')->references('id')->on('children');
            $table->unsignedBigInteger('id_food')->nullable();
            $table->foreign('id_food')->references('id')->on('food');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingestions');
    }
};
