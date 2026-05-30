<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('news_cat')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            return;
        }

        $col = DB::selectOne("SHOW COLUMNS FROM news_cat WHERE Field = 'RowID'");
        if (!$col) {
            return;
        }

        if (stripos((string) $col->Extra, 'auto_increment') !== false) {
            return;
        }

        $pkRows = DB::select(
            "SELECT COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'news_cat'
               AND CONSTRAINT_NAME = 'PRIMARY'
             ORDER BY ORDINAL_POSITION"
        );

        $pkCols = array_map(fn ($r) => $r->COLUMN_NAME, $pkRows);

        if ($pkCols === []) {
            DB::statement('ALTER TABLE news_cat ADD PRIMARY KEY (RowID)');
        } elseif ($pkCols !== ['RowID']) {
            return;
        }

        $type = strtolower((string) $col->Type);
        if (str_contains($type, 'bigint')) {
            DB::statement('ALTER TABLE news_cat MODIFY RowID BIGINT NOT NULL AUTO_INCREMENT');
        } else {
            DB::statement('ALTER TABLE news_cat MODIFY RowID INT(11) NOT NULL AUTO_INCREMENT');
        }
    }

    public function down(): void
    {
        //
    }
};
