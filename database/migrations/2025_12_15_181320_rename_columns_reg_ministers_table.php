<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE reg_ministers
            CHANGE newMinister new_minister VARCHAR(255),
            CHANGE responseMinister response_minister VARCHAR(255),
            CHANGE responseAdjunto response_adjunto VARCHAR(255),
            CHANGE SectorGeral sector_geral VARCHAR(255),
            CHANGE SectorMinister sector_minister VARCHAR(255)
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE reg_ministers
            CHANGE new_minister newMinister VARCHAR(255),
            CHANGE response_minister responseMinister VARCHAR(255),
            CHANGE response_adjunto responseAdjunto VARCHAR(255),
            CHANGE sector_geral SectorGeral VARCHAR(255),
            CHANGE sector_minister SectorMinister VARCHAR(255)
        ");
    }
};
