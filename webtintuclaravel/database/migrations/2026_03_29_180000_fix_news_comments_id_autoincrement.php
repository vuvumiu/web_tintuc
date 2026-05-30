<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng news_comments: cột id không AUTO_INCREMENT → INSERT bình luận lỗi 1364.
     */
    public function up(): void
    {
        if (!Schema::hasTable('news_comments')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            return;
        }

        $col = DB::selectOne("SHOW COLUMNS FROM news_comments WHERE Field = 'id'");
        if (!$col) {
            return;
        }

        if (stripos((string) $col->Extra, 'auto_increment') !== false) {
            return;
        }

        $hasPk = DB::selectOne(
            "SELECT 1 AS ok FROM information_schema.table_constraints
             WHERE table_schema = DATABASE()
               AND table_name = 'news_comments'
               AND constraint_type = 'PRIMARY KEY'
             LIMIT 1"
        );

        if (!$hasPk) {
            DB::statement('ALTER TABLE news_comments ADD PRIMARY KEY (id)');
        }

        $type = strtolower((string) $col->Type);
        if (str_contains($type, 'bigint')) {
            DB::statement('ALTER TABLE news_comments MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        } else {
            DB::statement('ALTER TABLE news_comments MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT');
        }
    }

    public function down(): void
    {
        //
    }
};
