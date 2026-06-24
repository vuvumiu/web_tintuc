<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('news_comments')) {
            return;
        }

        Schema::table('news_comments', function (Blueprint $table) {
            if (!Schema::hasColumn('news_comments', 'moderation_status')) {
                $table->string('moderation_status', 20)->default('approved')->after('is_active');
            }
            if (!Schema::hasColumn('news_comments', 'moderation_reason')) {
                $table->string('moderation_reason', 500)->nullable()->after('moderation_status');
            }
            if (!Schema::hasColumn('news_comments', 'spam_score')) {
                $table->unsignedTinyInteger('spam_score')->default(0)->after('moderation_reason');
            }
            if (!Schema::hasColumn('news_comments', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('spam_score');
            }
            if (!Schema::hasColumn('news_comments', 'user_agent')) {
                $table->string('user_agent', 500)->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('news_comments', 'content_hash')) {
                $table->char('content_hash', 64)->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('news_comments', 'moderated_by')) {
                $table->unsignedBigInteger('moderated_by')->nullable()->after('content_hash');
            }
            if (!Schema::hasColumn('news_comments', 'moderated_at')) {
                $table->timestamp('moderated_at')->nullable()->after('moderated_by');
            }
        });

        DB::table('news_comments')->update([
            'moderation_status' => DB::raw("CASE WHEN is_active = 1 THEN 'approved' ELSE 'pending' END"),
        ]);

        Schema::table('news_comments', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'news_comments_user_created_idx');
            $table->index(['ip_address', 'created_at'], 'news_comments_ip_created_idx');
            $table->index(['content_hash', 'created_at'], 'news_comments_hash_created_idx');
            $table->index(['moderation_status', 'created_at'], 'news_comments_moderation_created_idx');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('news_comments')) {
            return;
        }

        Schema::table('news_comments', function (Blueprint $table) {
            $table->dropIndex('news_comments_user_created_idx');
            $table->dropIndex('news_comments_ip_created_idx');
            $table->dropIndex('news_comments_hash_created_idx');
            $table->dropIndex('news_comments_moderation_created_idx');
            $table->dropColumn([
                'moderation_status',
                'moderation_reason',
                'spam_score',
                'ip_address',
                'user_agent',
                'content_hash',
                'moderated_by',
                'moderated_at',
            ]);
        });
    }
};
