<?php

namespace App\Models;

use App\Support\UsersTableSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'fullname',
        'email',
        'phone',
        'address',
        'avatar',
        'level',
        'status',
        'is_admin_account',
        'is_active',
        'is_author',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_admin_account' => 'integer',
        'is_active' => 'integer',
        'is_author' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope: Chỉ tài khoản admin/staff
     */
    public function scopeAdminAccounts($query)
    {
        if (UsersTableSchema::hasIsAdminAccountColumn()) {
            return $query->where('is_admin_account', 1);
        }

        return $query->whereIn('level', [1, 2]);
    }

    /**
     * Scope: Chỉ tài khoản người dùng thường
     */
    public function scopeRegularAccounts($query)
    {
        if (UsersTableSchema::hasIsAdminAccountColumn()) {
            return $query->where('is_admin_account', 0);
        }

        return $query->where(function ($q) {
            $q->whereNotIn('level', [1, 2])->orWhereNull('level');
        });
    }

    /**
     * Scope: Tài khoản đang hoạt động
     */
    public function scopeActive($query)
    {
        if (!UsersTableSchema::hasIsActiveColumn()) {
            return $query;
        }

        return $query->where(function ($q) {
            $q->where('is_active', 1)->orWhereNull('is_active');
        });
    }

    public function scopeAuthorCandidates($query)
    {
        $query->adminAccounts()->active();

        if (UsersTableSchema::hasIsAuthorColumn()) {
            $query->where('is_author', 1);
        } else {
            $query->whereExists(function ($subQuery) {
                $subQuery->selectRaw('1')
                    ->from('news')
                    ->whereColumn('news.author_id', 'users.id');
            });
        }

        return $query;
    }

    public function scopeAuthorAccounts($query)
    {
        $query->adminAccounts();

        return $query->where(function ($subQuery) {
            if (UsersTableSchema::hasIsAuthorColumn()) {
                $subQuery->where('is_author', 1)
                    ->orWhereExists(function ($newsQuery) {
                        $newsQuery->selectRaw('1')
                            ->from('news')
                            ->whereColumn('news.author_id', 'users.id');
                    });

                return;
            }

            $subQuery->whereExists(function ($newsQuery) {
                $newsQuery->selectRaw('1')
                    ->from('news')
                    ->whereColumn('news.author_id', 'users.id');
            });
        });
    }

    /**
     * Tài khoản có đang được phép đăng nhập hay không.
     * Lưu ý: trong PHP, null == 0 là true — không dùng so sánh lỏng với is_active.
     * Dữ liệu cũ (chưa có cột hoặc NULL) được coi là đang hoạt động.
     */
    public function isAccountActive(): bool
    {
        $v = $this->getAttribute('is_active');

        if ($v === null) {
            return true;
        }

        return (int) $v === 1;
    }

    /**
     * Kiểm tra có phải admin không (level=1)
     */
    public function isAdmin(): bool
    {
        return $this->level == 1;
    }

    /**
     * Kiểm tra có phải staff không (level=2)
     */
    public function isStaff(): bool
    {
        return $this->level == 2;
    }

    /**
     * Kiểm tra có quyền truy cập admin panel không
     */
    public function canAccessAdmin(): bool
    {
        return in_array((int) $this->level, [1, 2], true) && $this->isAccountActive();
    }

    public function isAuthor(): bool
    {
        if (UsersTableSchema::hasIsAuthorColumn()) {
            return (int) $this->is_author === 1;
        }

        return $this->authoredNews()->exists();
    }

    /**
     * Kiểm tra có phải tài khoản admin/staff không
     */
    public function isAdminStaffAccount(): bool
    {
        if (UsersTableSchema::hasIsAdminAccountColumn()) {
            return (int) $this->is_admin_account === 1;
        }

        return in_array((int) $this->level, [1, 2], true);
    }

    /**
     * Kiểm tra có phải tài khoản người dùng thường không
     */
    public function isRegularAccount(): bool
    {
        if (UsersTableSchema::hasIsAdminAccountColumn()) {
            return (int) $this->is_admin_account === 0;
        }

        return !in_array((int) $this->level, [1, 2], true);
    }

    /**
     * Lấy tên cấp bậc (level)
     */
    public function getLevelNameAttribute(): string
    {
        return match ($this->level) {
            1 => 'Quản trị viên',
            2 => 'Nhân viên',
            default => 'Người dùng',
        };
    }

    /**
     * Lấy badge class theo cấp bậc
     */
    public function getLevelBadgeClassAttribute(): string
    {
        return match ($this->level) {
            1 => 'badge-danger',
            2 => 'badge-warning',
            default => 'badge-secondary',
        };
    }

    public function comments(): HasMany
    {
        return $this->hasMany(NewsComment::class, 'user_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(NewsRating::class, 'user_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class, 'user_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function authoredNews(): HasMany
    {
        return $this->hasMany(News::class, 'author_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    // ── RBAC helpers ──────────────────────────────────────────
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasPermission(string $permissionName): bool
    {
        if (!$this->isAccountActive()) {
            return false;
        }

        if ($this->isAdmin()) {
            return true;
        }

        if (!$this->isAdminStaffAccount()) {
            return false;
        }

        if ($permissionName === 'author.list' && $this->isStaff()) {
            return true;
        }

        return $this->roles()->whereHas('permissions', function ($q) use ($permissionName) {
            $q->where('name', $permissionName);
        })->exists();
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $perm) {
            if ($this->hasPermission($perm)) {
                return true;
            }
        }
        return false;
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', 0)->count();
    }

    public function notificationPreference(): HasOne
    {
        return $this->hasOne(NotificationPreference::class, 'user_id');
    }

    public function hasNotificationEnabled(string $type): bool
    {
        $pref = NotificationPreference::where('user_id', $this->id)->first();
        if (!$pref) {
            return true;
        }
        return $pref->isEnabled($type);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && file_exists(public_path($this->avatar))) {
            return asset($this->avatar);
        }

        $email = strtolower(trim((string) ($this->email ?? '')));
        $hash = md5($email);

        return 'https://www.gravatar.com/avatar/' . $hash . '?d=identicon&s=128';
    }

    public function getInitialsAttribute(): string
    {
        if ($this->fullname) {
            $parts = explode(' ', trim($this->fullname));
            $init = '';
            foreach (array_slice($parts, 0, 2) as $p) {
                $init .= mb_strtoupper(mb_substr($p, 0, 1));
            }
            return $init ?: strtoupper(substr($this->username, 0, 1));
        }
        return strtoupper(substr($this->username, 0, 1));
    }
}
