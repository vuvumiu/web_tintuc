<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Ad extends Model
{
    protected $table = 'ads';
    
    protected $fillable = [
        'name',
        'image',
        'link',
        'type',
        'location',
        'popup_position',
        'show_once_per_session',
        'auto_close_seconds',
        'show_close_button',
        'impression_limit',
        'cooldown_minutes',
        'show_delay_seconds',
        'banner_width',
        'banner_height',
        'banner_align',
        'status',
        'sort',
        'priority',
        'view_count',
        'click_count',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'status' => 'boolean',
        'show_once_per_session' => 'boolean',
        'show_close_button' => 'boolean',
        'auto_close_seconds' => 'integer',
        'impression_limit' => 'integer',
        'cooldown_minutes' => 'integer',
        'show_delay_seconds' => 'integer',
        'sort' => 'integer',
        'priority' => 'integer',
        'view_count' => 'integer',
        'click_count' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Loại quảng cáo
     */
    public const TYPE_POPUP = 'popup';
    public const TYPE_BANNER = 'banner';
    public const TYPE_SIDEBAR = 'sidebar';
    public const TYPE_IN_ARTICLE = 'in_article';

    public const TYPES = [
        self::TYPE_POPUP,
        self::TYPE_BANNER,
        self::TYPE_SIDEBAR,
        self::TYPE_IN_ARTICLE,
    ];

    /**
     * Vị trí hiển thị trang
     */
    public const LOC_HOME = 'homepage';
    public const LOC_ARTICLE = 'article';
    public const LOC_ALL = 'all';

    public const LOCATIONS = [
        self::LOC_HOME,
        self::LOC_ARTICLE,
        self::LOC_ALL,
    ];

    /**
     * Vị trí popup
     */
    public const POPUP_CENTER = 'center';
    public const POPUP_BOTTOM_RIGHT = 'bottom_right';
    public const POPUP_BOTTOM_LEFT = 'bottom_left';
    public const POPUP_TOP_RIGHT = 'top_right';
    public const POPUP_TOP_LEFT = 'top_left';

    public const POPUP_POSITIONS = [
        self::POPUP_CENTER,
        self::POPUP_BOTTOM_RIGHT,
        self::POPUP_BOTTOM_LEFT,
        self::POPUP_TOP_RIGHT,
        self::POPUP_TOP_LEFT,
    ];

    /**
     * Label cho loại quảng cáo
     */
    public static function typeLabel(string $type): string
    {
        return match ($type) {
            self::TYPE_POPUP => 'Popup',
            self::TYPE_BANNER => 'Banner',
            self::TYPE_SIDEBAR => 'Sidebar',
            self::TYPE_IN_ARTICLE => 'Trong bài viết',
            default => ucfirst($type),
        };
    }

    /**
     * Label cho vị trí hiển thị
     */
    public static function locationLabel(string $location): string
    {
        return match ($location) {
            self::LOC_HOME => 'Trang chủ',
            self::LOC_ARTICLE => 'Trang bài viết',
            self::LOC_ALL => 'Tất cả trang',
            default => ucfirst($location),
        };
    }

    /**
     * Label cho vị trí popup
     */
    public static function popupPositionLabel(string $position): string
    {
        return match ($position) {
            self::POPUP_CENTER => 'Giữa màn hình',
            self::POPUP_BOTTOM_RIGHT => 'Góc dưới phải',
            self::POPUP_BOTTOM_LEFT => 'Góc dưới trái',
            self::POPUP_TOP_RIGHT => 'Góc trên phải',
            self::POPUP_TOP_LEFT => 'Góc trên trái',
            default => ucfirst($position),
        };
    }

    /**
     * Scope: Chỉ lấy quảng cáo đang bật
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Scope: Lọc theo loại quảng cáo
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Lọc theo vị trí hiển thị
     */
    public function scopeForLocation(Builder $query, string $location): Builder
    {
        return $query->where(function ($q) use ($location) {
            $q->where('location', $location)
              ->orWhere('location', self::LOC_ALL);
        });
    }

    /**
     * Scope: Lọc theo thời gian hiệu lực
     */
    public function scopeWithinDateRange(Builder $query): Builder
    {
        $now = Carbon::now();
        
        return $query->where(function ($q) use ($now) {
            $q->whereNull('start_date')
              ->orWhere('start_date', '<=', $now);
        })
        ->where(function ($q) use ($now) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', $now);
        });
    }

    /**
     * Scope: Sắp xếp theo độ ưu tiên và thứ tự
     */
    public function scopeOrderByPriority(Builder $query): Builder
    {
        return $query->orderByDesc('priority')
                     ->orderBy('sort');
    }

    public function scopePopup(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_POPUP);
    }

    /**
     * Lấy một quảng cáo ngẫu nhiên dựa trên loại và vị trí
     */
    public static function getRandomFor(string $type, string $location): ?self
    {
        return self::active()
            ->ofType($type)
            ->forLocation($location)
            ->withinDateRange()
            ->orderByPriority()
            ->inRandomOrder()
            ->first();
    }

    public static function getRandomPopup(string $location = self::LOC_ALL): ?self
    {
        return self::active()
            ->popup()
            ->forLocation($location)
            ->withinDateRange()
            ->orderByPriority()
            ->inRandomOrder()
            ->first();
    }

    /**
     * Lấy danh sách quảng cáo cho vị trí
     */
    public static function getFor(string $type, string $location, int $limit = 1): \Illuminate\Database\Eloquent\Collection
    {
        return self::active()
            ->ofType($type)
            ->forLocation($location)
            ->withinDateRange()
            ->orderByPriority()
            ->limit($limit)
            ->get();
    }

    /**
     * Tăng số lượt hiển thị
     */
    public function incrementView(): void
    {
        $this->increment('view_count');
    }

    /**
     * Tăng số lượt click
     */
    public function incrementClick(): void
    {
        $this->increment('click_count');
    }

    /**
     * Lấy URL ảnh đầy đủ
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }
        
        return url('images/ads/' . $this->image);
    }

    /**
     * Kiểm tra quảng cáo có đang trong thời gian hiệu lực không
     */
    public function isCurrentlyActive(): bool
    {
        $now = Carbon::now();
        
        if ($this->start_date && $this->start_date->gt($now)) {
            return false;
        }
        
        if ($this->end_date && $this->end_date->lt($now)) {
            return false;
        }
        
        return true;
    }
}
