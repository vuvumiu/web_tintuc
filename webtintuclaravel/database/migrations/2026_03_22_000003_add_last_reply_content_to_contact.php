<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact', function (Blueprint $table) {
            if (!Schema::hasColumn('contact', 'last_reply_content')) {
                $table->text('last_reply_content')->nullable()->after('replied_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contact', function (Blueprint $table) {
            if (Schema::hasColumn('contact', 'last_reply_content')) {
                $table->dropColumn('last_reply_content');
            }
        });
    }
};
