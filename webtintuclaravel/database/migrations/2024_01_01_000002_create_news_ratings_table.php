<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('news_ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('news_id');
            $table->foreign('news_id')->references('RowID')->on('news')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('score'); // 1 → 5 sao
            $table->timestamps();

            // Mỗi user chỉ đánh giá 1 lần cho 1 bài viết
            $table->unique(['news_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('news_ratings');
    }
};
