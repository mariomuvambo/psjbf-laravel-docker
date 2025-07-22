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
       Schema::create('user_ministers', function (Blueprint $table) {
             $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('reg_minister_id')->constrained('reg_ministers')->onDelete('cascade');

            $table->string('name', 100);
            $table->string('surname', 100)->nullable();
            $table->string('contacto', 20)->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'reg_minister_id']); 

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_ministers');
    }
};
