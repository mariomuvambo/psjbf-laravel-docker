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
            $table->string('newMinister');
            $table->text('description')->nullable(); // ✅ renomeado de "finally"
            $table->string('responseMinister');
            $table->string('responseAdjunto');
            $table->string('SectorGeral');
            $table->string('SectorMinister');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reg_ministers');
    }
};
