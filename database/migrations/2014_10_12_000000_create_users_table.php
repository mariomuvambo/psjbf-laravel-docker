<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('apelido')->nullable();
            $table->string('email')->unique();
            $table->string('telefone')->nullable();
            $table->string('endereco')->nullable();
            $table->enum('genero', ['Masculino', 'Feminino'])->nullable();
            $table->date('data_nascimento')->nullable();
            $table->enum('tipo_usuario', ['fiel', 'voluntario', 'sacerdote']);
            $table->string('foto')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
