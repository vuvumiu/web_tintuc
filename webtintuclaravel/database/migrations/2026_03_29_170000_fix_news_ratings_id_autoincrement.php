<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('news_ratings')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            return;
        }

        $col = DB::selectOne("SHOW COLUMNS FROM news_ratings WHERE Field = 'id'");
        if (!$col) {
            return;
        }

        if (stripos((string) $col->Extra, 'auto_increment') !== false) {
            return;
        }

        $hasPk = DB::selectOne(
            "SELECT 1 AS ok FROM information_schema.table_constraints
             WHERE table_schema = DATABASE()
               AND table_name = 'news_ratings'
               AND constraint_type = 'PRIMARY KEY'
             LIMIT 1"
        );

        if (!$hasPk) {
            DB::statement('ALTER TABLE news_ratings ADD PRIMARY KEY (id)');
        }

        $type = strtolower((string) $col->Type);
        if (str_contains($type, 'bigint')) {
            DB::statement('ALTER TABLE news_ratings MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        } else {
            DB::statement('ALTER TABLE news_ratings MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT');
        }
    }

    public function down(): void
    {
        //
    }
};
