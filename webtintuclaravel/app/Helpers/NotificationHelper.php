<?php

namespace App\Helpers;

class NotificationHelper
{
    public static function flash(string $level, string $message): array
    {
        return ['flash_level' => $level, 'flash_message' => $message];
    }

    /* ---------- News CRUD ---------- */

    public static function newsAdded(string $name): array
    {
        return self::flash('success', "Đã thêm bài viết '{$name}' thành công.");
    }

    public static function newsUpdated(string $name): array
    {
        return self::flash('success', "Đã cập nhật bài viết '{$name}' thành công.");
    }

    public static function newsDeleted(string $name): array
    {
        return self::flash('success', "Đã xóa bài viết '{$name}' thành công.");
    }

    public static function newsNotFound(): array
    {
        return self::flash('warning', 'Không tìm thấy bài viết cần xóa.');
    }

    public static function newsNotFoundEdit(): array
    {
        return self::flash('warning', 'Bài viết không tồn tại hoặc đã bị xóa.');
    }

    public static function newsDuplicated(string $name): array
    {
        return self::flash('success', "Đã sao chép bài viết '{$name}' thành bản nháp mới.");
    }

    public static function newsDuplicateNotFound(): array
    {
        return self::flash('danger', 'Bài viết không tồn tại.');
    }

    /* ---------- Bulk Actions ---------- */

    public static function bulkAction(string $action, int $count): array
    {
        $labels = [
            'delete'        => 'xóa',
            'show'          => 'hiển thị',
            'hide'          => 'ẩn',
            'submit_review' => 'gửi để phê duyệt',
        ];
        $verb = $labels[$action] ?? $action;

        if ($count <= 0) {
            return self::flash('warning', 'Vui lòng chọn ít nhất một bài viết.');
        }

        return self::flash('success', "Đã {$verb} {$count} bài viết được chọn.");
    }

    public static function bulkActionApi(int $count): array
    {
        if ($count <= 0) {
            return ['success' => false, 'message' => 'Vui lòng chọn ít nhất một bài viết.'];
        }
        return ['success' => true, 'message' => "Đã thực hiện trên {$count} bài viết."];
    }

    /* ---------- Validation ---------- */

    public static function validationFailed(string $context = 'Thao tác'): array
    {
        return self::flash('danger', "{$context} thất bại. Vui lòng kiểm tra lại dữ liệu.");
    }

    public static function authorRequired(): array
    {
        return self::flash('danger', 'Vui lòng chọn một tác giả hợp lệ cho bài viết.');
    }

    /* ---------- Category ---------- */

    public static function categoryAdded(string $name): array
    {
        return self::flash('success', "Đã thêm danh mục '{$name}' thành công.");
    }

    public static function categoryUpdated(string $name): array
    {
        return self::flash('success', "Đã cập nhật danh mục '{$name}' thành công.");
    }

    public static function categoryDeleted(string $name): array
    {
        return self::flash('success', "Đã xóa danh mục '{$name}' thành công.");
    }

    public static function categoryNotFound(): array
    {
        return self::flash('warning', 'Không tìm thấy danh mục cần xóa.');
    }

    public static function categoryAliasExists(): array
    {
        return self::flash('danger', 'Đường dẫn (slug) đã tồn tại.');
    }
}
