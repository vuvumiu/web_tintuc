<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sau khi thêm cột is_admin_account (mặc định = 0), mọi user cũ đều bị gán 0.
     * Gán lại is_admin_account = 1 cho tài khoản admin/nhân viên (level 1, 2) đã có sẵn.
     */
    public function up(): void
    {
        // Skip - fresh database, no existing staff to backfill
    }

    /**
     * Không revert tự động (tránh ghi đè dữ liệu đã chỉnh sau này).
     */
    public function down(): void
    {
        // intentionally empty
    }
};
