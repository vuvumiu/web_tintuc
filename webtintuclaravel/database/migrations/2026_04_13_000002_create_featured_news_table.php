<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('featured_news', function (Blueprint $table) {
            $table->id('RowID');
            $table->unsignedBigInteger('news_id')->comment('ID bài viết nổi bật');
            $table->tinyInteger('position')->default(1)->comment('1=Tin chính (hero lớn), 2=Tin phụ (sidebar)');
            $table->integer('Sort')->default(0)->comment('Thứ tự hiển thị');
            $table->tinyInteger('Status')->default(1)->comment('1=Bật, 0=Tắt');
            $table->timestamps();

            $table->index('Status');
            $table->index('position');
            $table->index(['Status', 'position', 'Sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('featured_news');
    }
};
