<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contact')) {
            return;
        }

        Schema::table('contact', function (Blueprint $table) {
            if (!Schema::hasColumn('contact', 'Phone')) {
                $table->string('Phone', 50)->nullable()->after('Email');
            }

            if (!Schema::hasColumn('contact', 'last_reply_content')) {
                $table->text('last_reply_content')->nullable()->after('replied_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('contact')) {
            return;
        }

        Schema::table('contact', function (Blueprint $table) {
            if (Schema::hasColumn('contact', 'Phone')) {
                $table->dropColumn('Phone');
            }

            if (Schema::hasColumn('contact', 'last_reply_content')) {
                $table->dropColumn('last_reply_content');
            }
        });
    }
};
