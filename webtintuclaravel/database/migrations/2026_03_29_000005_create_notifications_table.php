<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Người nhận thông báo');
            $table->string('type', 50)->comment('comment_new, comment_reply, news_approved, news_rejected, system ...');
            $table->string('title', 255);
            $table->text('content')->nullable();
            $table->string('link', 500)->nullable()->comment('Đường dẫn khi click');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('ID tham chiếu: comment_id, news_id ...');
            $table->string('reference_type', 50)->nullable()->comment('Loại: news_comment, news ...');
            $table->tinyInteger('is_read')->default(0)->comment('0=chưa đọc, 1=đã đọc');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'is_read']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
