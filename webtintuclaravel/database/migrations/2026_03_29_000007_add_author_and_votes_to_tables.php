<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            if (!Schema::hasColumn('news', 'author_id')) {
                $table->unsignedBigInteger('author_id')->nullable()->comment('Tác giả bài viết');
                $table->foreign('author_id')->references('id')->on('users')->onDelete('set null');
            }
        });

        Schema::table('news_comments', function (Blueprint $table) {
            if (!Schema::hasColumn('news_comments', 'upvote_count')) {
                $table->unsignedInteger('upvote_count')->default(0);
            }
            if (!Schema::hasColumn('news_comments', 'downvote_count')) {
                $table->unsignedInteger('downvote_count')->default(0);
            }
            if (!Schema::hasColumn('news_comments', 'reply_count')) {
                $table->unsignedInteger('reply_count')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('news_comments', function (Blueprint $table) {
            $table->dropColumn(['upvote_count', 'downvote_count', 'reply_count']);
        });

        Schema::table('news', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropColumn('author_id');
        });
    }
};
