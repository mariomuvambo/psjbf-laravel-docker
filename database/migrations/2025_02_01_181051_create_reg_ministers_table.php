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
        Schema::create('reg_ministers', function (Blueprint $table) {
            $table->id();
            $table->string('newMinister');
            $table->text('finally');
            $table->text('responseMinister');
            $table->text('responseAdjunto');
            $table->text('SectorGeral');
            $table->text('SectorMinister');
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
        Schema::dropIfExists('reg_ministers');
    }
};
