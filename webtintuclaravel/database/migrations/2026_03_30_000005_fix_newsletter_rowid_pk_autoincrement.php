<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip - newsletter table uses 'id' column, not 'RowID'
    }

    public function down(): void
    {
        if (!Schema::hasTable('newsletter')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement('ALTER TABLE `newsletter` MODIFY `RowID` INT(11) NOT NULL');
    }
};
