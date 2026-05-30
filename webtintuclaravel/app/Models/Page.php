<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'page';

    protected $primaryKey = 'RowID';

    // Nếu cần factories, bạn thêm use HasFactory
    use HasFactory;

    /** Các giá trị menu_kind hợp lệ (menu website) */
    public const MENU_HOME = 'home';

    public const MENU_LINK = 'link';

    public const MENU_NEWS_CATEGORIES = 'news_categories';

    public const MENU_ROUTE = 'route';

    public static function menuKindLabels(): array
    {
        return [
            self::MENU_HOME => 'Trang chủ (icon / Alias /)',
            self::MENU_LINK => 'Trang tĩnh (URL /slug)',
            self::MENU_NEWS_CATEGORIES => 'Menu Tin tức (dropdown danh mục)',
            self::MENU_ROUTE => 'Liên kết route có sẵn (vd: tin-moi-nhat)',
        ];
    }
}
