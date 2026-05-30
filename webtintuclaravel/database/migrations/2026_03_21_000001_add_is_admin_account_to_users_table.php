<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Thêm các cột mới vào bảng users:
     * - is_admin_account: 0 = tài khoản người dùng thường (đăng ký trên site),
     *                     1 = tài khoản admin/staff (được tạo bởi admin)
     * - is_active:        1 = hoạt động, 0 = bị vô hiệu hóa
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_admin_account')) {
                $table->tinyInteger('is_admin_account')->default(0)->after('password');
            }

            if (!Schema::hasColumn('users', 'is_active')) {
                $table->tinyInteger('is_active')->default(1)->after('is_admin_account');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('users', 'is_admin_account')) {
                $table->dropColumn('is_admin_account');
            }
        });
    }
};
