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
        Schema::create('documento_casamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('casamento_id');

            // 🔒 Aceita apenas BI ou Certidão de Batismo
            $table->enum('tipo_documento', ['BI', 'Certidão de Batismo']);

            $table->string('arquivo');
            $table->timestamps();

            $table->foreign('casamento_id')
                  ->references('id')
                  ->on('casamentos')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documento_casamentos');
    }
};
