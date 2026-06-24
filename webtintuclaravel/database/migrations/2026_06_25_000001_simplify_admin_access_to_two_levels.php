<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_roles')) {
            DB::table('user_roles')->delete();
        }

        if (Schema::hasTable('role_permissions')) {
            DB::table('role_permissions')->delete();
        }

        if (Schema::hasTable('roles')) {
            DB::table('roles')->delete();
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_author')) {
            DB::table('users')
                ->whereIn('level', [1, 2])
                ->update(['is_author' => 1]);
        }
    }

    public function down(): void
    {
        // The former role matrix was intentionally removed. Re-run the older
        // RBAC migrations manually only when restoring that legacy model.
    }
};
