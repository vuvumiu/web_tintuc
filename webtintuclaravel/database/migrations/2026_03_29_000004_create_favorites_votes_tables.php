<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('news_id');
            $table->timestamps();
            $table->unique(['user_id', 'news_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // No FK on news_id — news.RowID may be signed INT (legacy schema)
        });

        Schema::create('comment_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comment_id');
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('vote_type')->comment('1=upvote, -1=downvote');
            $table->timestamps();
            $table->unique(['comment_id', 'user_id']);
            $table->foreign('comment_id')->references('id')->on('news_comments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_votes');
        Schema::dropIfExists('user_favorites');
    }
};
