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
        Schema::create('masses', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Data da missa
            $table->string('liturgical_day');
            $table->string('first_reading')->nullable();
            $table->string('first_reader')->nullable();
            $table->string('psalm')->nullable();
            $table->string('psalm_reader')->nullable();
            $table->string('second_reading')->nullable();
            $table->string('second_reader')->nullable();
            $table->string('gospel')->nullable();
            $table->string('celebrant')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('masses');
    }
};
