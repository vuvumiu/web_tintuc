<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('news_tickers')) {
            return;
        }

        Schema::create('news_tickers', function (Blueprint $table) {
            $table->id('RowID');
            $table->unsignedBigInteger('news_id')->nullable()->comment('Bài viết được chọn (tùy chọn)');
            $table->string('title', 255)->nullable()->comment('Hoặc tiêu đề tùy chỉnh nếu không chọn bài viết');
            $table->string('alias', 255)->nullable();
            $table->tinyInteger('Status')->default(1)->comment('1=Bật, 0=Tắt');
            $table->integer('Sort')->default(0)->comment('Thứ tự hiển thị');
            $table->timestamps();

            $table->index('news_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_tickers');
    }
};
