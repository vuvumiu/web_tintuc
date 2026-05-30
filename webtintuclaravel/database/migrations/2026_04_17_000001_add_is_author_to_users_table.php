<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'is_author')) {
            Schema::table('users', function (Blueprint $table) {
                $table->tinyInteger('is_author')->default(0)->after('is_active');
            });
        }

        $internalAuthorIds = DB::table('news')
            ->join('users', 'news.author_id', '=', 'users.id')
            ->whereNotNull('news.author_id')
            ->when(
                Schema::hasColumn('users', 'is_admin_account'),
                fn ($query) => $query->where('users.is_admin_account', 1),
                fn ($query) => $query->whereIn('users.level', [1, 2])
            )
            ->distinct()
            ->pluck('users.id');

        if ($internalAuthorIds->isNotEmpty()) {
            DB::table('users')
                ->whereIn('id', $internalAuthorIds->all())
                ->update(['is_author' => 1]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_author')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_author');
            });
        }
    }
};
