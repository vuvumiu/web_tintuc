<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Loại mục trên menu website:
     * - home: Trang chủ (Alias thường là /)
     * - link: Trang tĩnh, URL = /{Alias}
     * - news_categories: Dropdown danh mục tin (lấy từ bảng danh mục)
     * - route: Trang có route cố định (Alias = tin-moi-nhat, tin-noi-bat, tim-kiem, ...)
     */
    public function up(): void
    {
        if (! Schema::hasTable('page')) {
            return;
        }

        if (! Schema::hasColumn('page', 'menu_kind')) {
            Schema::table('page', function (Blueprint $table) {
                $table->string('menu_kind', 32)->default('link')->after('Alias');
            });
        }

        DB::table('page')->where('Alias', '/')->update(['menu_kind' => 'home']);

        if (DB::table('page')->where('menu_kind', 'news_categories')->exists()) {
            return;
        }

        // Đẩy các trang Sort >= 2 xuống để chèn 3 mục menu động (Tin tức / Tin mới / Tin nổi bật)
        DB::table('page')->where('Sort', '>=', 2)->increment('Sort', 3);

        $row = [
            'Images' => '',
            'Font' => '',
            'Status' => 1,
            'MetaTitle' => '',
            'MetaDescription' => '',
            'MetaKeyword' => '',
            'Description' => '',
        ];

        DB::table('page')->insert([
            array_merge($row, [
                'Name' => 'Tin tức',
                'Alias' => '#',
                'Sort' => 2,
                'menu_kind' => 'news_categories',
            ]),
            array_merge($row, [
                'Name' => 'Tin mới nhất',
                'Alias' => 'tin-moi-nhat',
                'Sort' => 3,
                'menu_kind' => 'route',
            ]),
            array_merge($row, [
                'Name' => 'Tin nổi bật',
                'Alias' => 'tin-noi-bat',
                'Sort' => 4,
                'menu_kind' => 'route',
            ]),
        ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('page', 'menu_kind')) {
            Schema::table('page', function (Blueprint $table) {
                $table->dropColumn('menu_kind');
            });
        }
    }
};
