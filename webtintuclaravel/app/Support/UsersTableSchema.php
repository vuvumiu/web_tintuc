<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

/**
 * Cache kiểm tra cột bảng users (tránh lỗi khi chưa chạy migrate).
 */
class UsersTableSchema
{
    protected static ?bool $hasIsActive = null;

    protected static ?bool $hasIsAdminAccount = null;

    protected static ?bool $hasIsAuthor = null;

    public static function hasIsActiveColumn(): bool
    {
        if (self::$hasIsActive === null) {
            self::$hasIsActive = Schema::hasColumn('users', 'is_active');
        }

        return self::$hasIsActive;
    }

    public static function hasIsAdminAccountColumn(): bool
    {
        if (self::$hasIsAdminAccount === null) {
            self::$hasIsAdminAccount = Schema::hasColumn('users', 'is_admin_account');
        }

        return self::$hasIsAdminAccount;
    }

    public static function hasIsAuthorColumn(): bool
    {
        if (self::$hasIsAuthor === null) {
            self::$hasIsAuthor = Schema::hasColumn('users', 'is_author');
        }

        return self::$hasIsAuthor;
    }

    public static function resetCache(): void
    {
        self::$hasIsActive = null;
        self::$hasIsAdminAccount = null;
        self::$hasIsAuthor = null;
    }
}
