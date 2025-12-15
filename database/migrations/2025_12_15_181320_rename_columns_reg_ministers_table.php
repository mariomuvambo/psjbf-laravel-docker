<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE reg_ministers RENAME COLUMN "newMinister" TO new_minister');
            DB::statement('ALTER TABLE reg_ministers RENAME COLUMN "responseMinister" TO response_minister');
            DB::statement('ALTER TABLE reg_ministers RENAME COLUMN "responseAdjunto" TO response_adjunto');
            DB::statement('ALTER TABLE reg_ministers RENAME COLUMN "SectorGeral" TO sector_geral');
            DB::statement('ALTER TABLE reg_ministers RENAME COLUMN "SectorMinister" TO sector_minister');
        } else {
            DB::statement('
                ALTER TABLE reg_ministers
                CHANGE newMinister new_minister VARCHAR(255),
                CHANGE responseMinister response_minister VARCHAR(255),
                CHANGE responseAdjunto response_adjunto VARCHAR(255),
                CHANGE SectorGeral sector_geral VARCHAR(255),
                CHANGE SectorMinister sector_minister VARCHAR(255)
            ');
        }
    }

    public function down(): void
    {
        // rollback opcional
    }
};
