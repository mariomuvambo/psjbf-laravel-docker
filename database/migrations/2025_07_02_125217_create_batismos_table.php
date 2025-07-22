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
        Schema::create('batismos', function (Blueprint $table) {
            $table->id();
            $table->string('nome_batizando');
            $table->date('data_nascimento');
            $table->string('local_nascimento');
            $table->string('nome_pai');
            $table->string('nome_mae');
            $table->string('nome_padrinho');
            $table->string('nome_madrinha');

            $table->string('documento_identificacao'); // caminho BI ou certidão

            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // quem fez o pedido
            $table->foreignId('sacerdote_id')->nullable()->constrained('users')->onDelete('set null'); // quem aprova

            $table->date('data_batismo')->nullable(); // definido após aprovação
            $table->string('livro_registo')->nullable();
            $table->string('pagina_registo')->nullable();
            $table->string('codigo_certidao')->nullable()->unique();

            $table->enum('estado', ['pendente', 'em_analise', 'aprovado', 'rejeitado'])->default('pendente');

            $table->boolean('confirmado')->default(false);

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
        Schema::dropIfExists('batismos');
    }
};
