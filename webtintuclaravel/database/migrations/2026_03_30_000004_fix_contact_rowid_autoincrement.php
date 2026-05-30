<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip - contact table uses 'id' column, not 'RowID'
    }

    public function down(): void
    {
        if (! Schema::hasTable('contact')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement('ALTER TABLE `contact` MODIFY `RowID` int(11) NOT NULL');
    }
};
