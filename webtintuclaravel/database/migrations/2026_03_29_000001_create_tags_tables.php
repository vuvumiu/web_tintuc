<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 120)->unique();
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->unsignedTinyInteger('popular_count')->default(0)->comment('Số lần tag được sử dụng');
            $table->unsignedTinyInteger('status')->default(1)->comment('1=hiển thị, 0=ẩn');
            $table->timestamps();
        });

        Schema::create('news_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('news_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();
            $table->unique(['news_id', 'tag_id']);
            // No FK on news_id — news.RowID may be signed INT (legacy schema)
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_tags');
        Schema::dropIfExists('tags');
    }
};
