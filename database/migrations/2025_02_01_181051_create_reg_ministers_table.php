<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reg_ministers', function (Blueprint $table) {
             $table->id();
            $table->string('new_minister');
            $table->text('description')->nullable();
            $table->string('response_minister');
            $table->string('response_adjunto');
            $table->string('sector_geral');
            $table->string('sector_minister');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reg_ministers');
    }
};
 