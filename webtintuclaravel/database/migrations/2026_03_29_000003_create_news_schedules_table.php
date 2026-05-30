<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('news_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable()->comment('Người duyệt');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'scheduled', 'published'])->default('draft');
            $table->enum('publish_type', ['now', 'schedule'])->default('now');
            $table->timestamp('scheduled_at')->nullable()->comment('Thời gian hẹn giờ xuất bản');
            $table->timestamp('published_at')->nullable()->comment('Thời gian thực tế xuất bản');
            $table->text('reject_reason')->nullable()->comment('Lý do từ chối duyệt');
            $table->timestamps();
            // No FK on news_id — news.RowID may be signed INT (legacy schema)
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_schedules');
    }
};
