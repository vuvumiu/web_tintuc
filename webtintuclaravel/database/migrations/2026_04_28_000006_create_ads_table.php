<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Bảng ads: quản lý quảng cáo - hỗ trợ nhiều loại: popup, banner, sidebar...
     */
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            
            // Thông tin cơ bản
            $table->string('name', 255)->comment('Tên quảng cáo');
            $table->string('image', 500)->nullable()->comment('Đường dẫn ảnh');
            $table->string('link', 500)->nullable()->comment('URL khi click');
            
            // Loại quảng cáo
            $table->enum('type', ['popup', 'banner', 'sidebar', 'in_article'])->default('popup')
                ->comment('popup=popup quảng cáo, banner=banner ngang, sidebar=quảng cáo cột bên, in_article=quảng cáo trong bài viết');
            
            // Vị trí hiển thị
            $table->enum('location', ['homepage', 'article', 'all'])->default('all')
                ->comment('homepage=trang chủ, article=trang bài viết, all=mọi trang');
            
            // Cài đặt hiển thị popup
            $table->enum('popup_position', ['center', 'bottom_right', 'bottom_left', 'top_right', 'top_left'])->default('center')
                ->comment('Vị trí popup: center=giữa màn hình, bottom_right=góc dưới phải...');
            $table->boolean('show_once_per_session')->default(true)->comment('Chỉ hiển thị 1 lần mỗi phiên');
            $table->integer('auto_close_seconds')->default(0)->comment('Tự đóng sau X giây, 0=không tự đóng');
            $table->boolean('show_close_button')->default(true)->comment('Có nút đóng không');
            
            // Cài đặt banner
            $table->string('banner_width', 50)->nullable()->comment('Chiều rộng banner (CSS)');
            $table->string('banner_height', 50)->nullable()->comment('Chiều cao banner (CSS)');
            $table->string('banner_align', 20)->default('center')->comment('Căn chỉnh: left, center, right');
            
            // Kiểm soát hiển thị
            $table->boolean('status')->default(true)->comment('1=bật, 0=tắt');
            $table->integer('sort')->default(0)->comment('Thứ tự sắp xếp');
            $table->integer('priority')->default(0)->comment('Độ ưu tiên (số cao hơn = ưu tiên hơn)');
            
            // Thống kê
            $table->unsignedBigInteger('view_count')->default(0)->comment('Số lượt hiển thị');
            $table->unsignedBigInteger('click_count')->default(0)->comment('Số lượt click');
            
            // Thời gian
            $table->timestamp('start_date')->nullable()->comment('Ngày bắt đầu hiển thị');
            $table->timestamp('end_date')->nullable()->comment('Ngày kết thúc hiển thị');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['type', 'status']);
            $table->index(['location', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
