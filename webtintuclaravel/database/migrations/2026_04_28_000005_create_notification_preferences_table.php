<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->boolean('notify_comment_new')->default(true);
            $table->boolean('notify_comment_reply')->default(true);
            $table->boolean('notify_comment_upvote')->default(true);
            $table->boolean('notify_comment_downvote')->default(true);
            $table->boolean('notify_news_rated')->default(true);
            $table->boolean('notify_news_favorited')->default(true);
            $table->boolean('notify_news_approved')->default(true);
            $table->boolean('notify_news_rejected')->default(true);
            $table->boolean('notify_system')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
