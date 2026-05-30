<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('migrations')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        $col = DB::selectOne("SHOW COLUMNS FROM migrations WHERE Field = 'id'");
        if (!$col) {
            return;
        }

        if (stripos((string) $col->Extra, 'auto_increment') !== false) {
            return;
        }

        $hasPk = DB::selectOne(
            "SELECT 1 AS ok FROM information_schema.table_constraints
             WHERE table_schema = DATABASE()
               AND table_name = 'migrations'
               AND constraint_type = 'PRIMARY KEY'
             LIMIT 1"
        );

        if (!$hasPk) {
            DB::statement('ALTER TABLE migrations ADD PRIMARY KEY (id)');
        }

        DB::statement('ALTER TABLE migrations MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    public function down(): void
    {
        if (!Schema::hasTable('migrations')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement('ALTER TABLE migrations MODIFY id INT UNSIGNED NOT NULL');
    }
};
