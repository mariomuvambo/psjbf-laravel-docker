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
        Schema::create('avisos', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->date("date_notify");
            $table->string("address");
            $table->date("date_realize");
            $table->time("hora");
            $table->string("description")->nullable();
            $table->timestamps();
        });

          // Criando tabela intermediária
          Schema::create('aviso_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('aviso_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('aviso_user');
        Schema::dropIfExists('avisos');
    }
};
