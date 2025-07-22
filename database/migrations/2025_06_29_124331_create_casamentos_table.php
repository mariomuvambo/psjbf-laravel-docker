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
        Schema::create('casamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Adiciona a referência ao usuário 

            $table->string('nome_noivo');
            $table->string('nome_noiva');
            $table->date('data_casamento');
            $table->string('local_casamento')->nullable();
            $table->enum('estado', ['pendente', 'em_analise', 'aprovado', 'rejeitado'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->timestamps();

            // Define a foreign key para a tabela users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('casamentos');
    }
};
