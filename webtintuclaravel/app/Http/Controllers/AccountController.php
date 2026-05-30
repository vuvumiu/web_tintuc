<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\NewsComment;
use App\Models\NewsRating;
use App\Support\UsersTableSchema;
use Illuminate\Database\Eloquent\Builder;

/**
 * AccountController - Quản lý tài khoản người dùng đã đăng ký trên website
 *
 * Khác với Staff Management (quản lý nhân viên):
 * - Staff: nhân viên nội bộ vận hành website, được tạo bởi admin, có thể truy cập admin panel
 * - Account: tài khoản người dùng thường đăng ký trên website, chỉ dùng bình luận/đánh giá
 *
 * Chỉ admin (level=1) mới có quyền truy cập Account Management.
 */
class AccountController extends Controller
{
    public function __construct()
    {
        // Chỉ admin (level=1) được quản lý tài khoản người dùng
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Query cơ sở: tài khoản người dùng thường (không phải admin/staff).
     */
    protected function regularUsersQuery(): Builder
    {
        $q = User::query();
        if (UsersTableSchema::hasIsAdminAccountColumn()) {
            $q->where('is_admin_account', 0);
        } else {
            $q->where(function ($query) {
                $query->whereNotIn('level', [1, 2])->orWhereNull('level');
            });
        }

        return $q;
    }

    /**
     * Danh sách tài khoản người dùng
     * Chỉ hiển thị tài khoản is_admin_account = 0 (tài khoản người dùng thường)
     */
    public function account_list(Request $request)
    {
        $query = $this->regularUsersQuery()->orderBy('created_at', 'DESC');

        // Lọc theo từ khóa
        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('username', 'like', '%' . $keyword . '%')
                  ->orWhere('fullname', 'like', '%' . $keyword . '%')
                  ->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }

        // Lọc theo trạng thái
        if ($request->has('is_active') && $request->is_active !== '' && UsersTableSchema::hasIsActiveColumn()) {
            $query->where('is_active', $request->is_active);
        }

        $accounts = $query->paginate(15);

        return view('back.account.list', compact('accounts'));
    }

    /**
     * Xem chi tiết tài khoản
     */
    public function account_view($id)
    {
        $account = $this->regularUsersQuery()->where('id', $id)->first();

        if (!$account) {
            return redirect('admin/member/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Tài khoản không tồn tại.'
            ]);
        }

        // Lấy thống kê hoạt động của tài khoản
        $commentCount = NewsComment::where('user_id', $id)->count();
        $ratingCount = NewsRating::where('user_id', $id)->count();

        // Lấy bình luận gần nhất
        $recentComments = NewsComment::where('user_id', $id)
            ->with('news')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get();

        return view('back.account.view', compact('account', 'commentCount', 'ratingCount', 'recentComments'));
    }

    /**
     * Form chỉnh sửa tài khoản
     */
    public function account_edit($id)
    {
        $account = $this->regularUsersQuery()->where('id', $id)->first();

        if (!$account) {
            return redirect('admin/member/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Tài khoản không tồn tại.'
            ]);
        }

        return view('back.account.edit', compact('account'));
    }

    /**
     * Lưu chỉnh sửa tài khoản
     */
    public function account_edit_post(Request $request, $id)
    {
        $account = $this->regularUsersQuery()->where('id', $id)->first();

        if (!$account) {
            return redirect('admin/member/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Tài khoản không tồn tại.'
            ]);
        }

        $fullname = trim((string) $request->input('fullname', ''));
        $email = trim((string) $request->input('email', ''));

        if ($fullname === '' || $email === '') {
            return redirect('admin/member/edit/' . $id)
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->with([
                    'flash_level' => 'danger',
                    'flash_message' => 'Họ tên và email là bắt buộc.'
                ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect('admin/member/edit/' . $id)
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->with([
                    'flash_level' => 'danger',
                    'flash_message' => 'Địa chỉ email không hợp lệ.'
                ]);
        }

        // Kiểm tra email trùng (trừ chính tài khoản này)
        $emailExists = User::where('email', $email)
            ->where('id', '!=', $id)
            ->exists();
        if ($emailExists) {
            return redirect('admin/member/edit/' . $id)
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->with([
                    'flash_level' => 'danger',
                    'flash_message' => 'Email này đã được sử dụng bởi tài khoản khác.'
                ]);
        }

        $account->fullname = $fullname;
        $account->email = $email;
        $account->phone = trim((string) $request->input('phone', ''));
        $account->address = trim((string) $request->input('address', ''));
        if (UsersTableSchema::hasIsActiveColumn()) {
            $account->is_active = $request->has('is_active') ? 1 : 0;
        }

        // Đổi mật khẩu (nếu có)
        $password = $request->input('password', '');
        $passwordConfirmation = $request->input('password_confirmation', '');
        if ($password !== '') {
            if (strlen($password) < 6) {
                return redirect('admin/member/edit/' . $id)
                    ->withInput($request->except('_token', 'password', 'password_confirmation'))
                    ->with([
                        'flash_level' => 'danger',
                        'flash_message' => 'Mật khẩu phải có ít nhất 6 ký tự.'
                    ]);
            }
            if ($password !== $passwordConfirmation) {
                return redirect('admin/member/edit/' . $id)
                    ->withInput($request->except('_token', 'password', 'password_confirmation'))
                    ->with([
                        'flash_level' => 'danger',
                        'flash_message' => 'Mật khẩu xác nhận không khớp.'
                    ]);
            }
            $account->password = bcrypt($password);
        }

        try {
            $saved = $account->save();
        } catch (\Throwable $e) {
            Log::error('account_edit_post save error: ' . $e->getMessage());
            $saved = false;
        }

        if ($saved) {
            return redirect('admin/member/edit/' . $id)->with([
                'flash_level' => 'success',
                'flash_message' => 'Cập nhật tài khoản thành công.'
            ]);
        } else {
            return redirect('admin/member/edit/' . $id)
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->with([
                    'flash_level' => 'danger',
                    'flash_message' => 'Lỗi cập nhật tài khoản. Vui lòng thử lại.'
                ]);
        }
    }

    /**
     * Xóa tài khoản
     */
    public function account_delete(Request $request, $id)
    {
        $account = $this->regularUsersQuery()->where('id', $id)->first();

        if (!$account) {
            return redirect('admin/member/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Tài khoản không tồn tại.'
            ]);
        }

        // Kiểm tra nếu đang đăng nhập
        if (Auth::id() == $id) {
            return redirect('admin/member/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Bạn không thể xóa tài khoản đang đăng nhập.'
            ]);
        }

        $deleted = $account->delete();

        if ($deleted) {
            return redirect('admin/member/list')->with([
                'flash_level' => 'success',
                'flash_message' => 'Xóa tài khoản thành công.'
            ]);
        } else {
            return redirect('admin/member/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Lỗi xóa tài khoản.'
            ]);
        }
    }

    /**
     * Bật/tắt trạng thái tài khoản (AJAX-friendly)
     */
    public function account_toggle($id)
    {
        if (!UsersTableSchema::hasIsActiveColumn()) {
            return response()->json([
                'success' => false,
                'message' => 'Cần chạy php artisan migrate để bật chức năng khóa/mở tài khoản.',
            ], 400);
        }

        $account = $this->regularUsersQuery()->where('id', $id)->first();

        if (!$account) {
            return response()->json(['success' => false, 'message' => 'Tài khoản không tồn tại.'], 404);
        }

        // Không cho tự khóa chính mình
        if (Auth::id() == $id) {
            return response()->json(['success' => false, 'message' => 'Không thể tự khóa tài khoản của mình.'], 403);
        }

        $account->is_active = $account->is_active == 1 ? 0 : 1;
        $account->save();

        $statusLabel = $account->is_active == 1 ? 'kích hoạt' : 'vô hiệu hóa';
        return response()->json([
            'success' => true,
            'message' => "Tài khoản đã được {$statusLabel}.",
            'new_status' => $account->is_active
        ]);
    }

    /**
     * Khóa tài khoản (GET - fallback)
     */
    public function account_lock($id)
    {
        $account = $this->regularUsersQuery()->where('id', $id)->first();

        if (!$account) {
            return redirect('admin/member/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Tài khoản không tồn tại.'
            ]);
        }

        if (Auth::id() == $id) {
            return redirect('admin/member/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Bạn không thể tự khóa tài khoản của mình.'
            ]);
        }

        if (UsersTableSchema::hasIsActiveColumn()) {
            $account->is_active = 0;
            $account->save();
        }

        return redirect('admin/member/list')->with([
            'flash_level' => 'success',
            'flash_message' => "Tài khoản \"{$account->username}\" đã bị khóa."
        ]);
    }

    /**
     * Mở khóa tài khoản (GET - fallback)
     */
    public function account_unlock($id)
    {
        $account = $this->regularUsersQuery()->where('id', $id)->first();

        if (!$account) {
            return redirect('admin/member/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Tài khoản không tồn tại.'
            ]);
        }

        if (UsersTableSchema::hasIsActiveColumn()) {
            $account->is_active = 1;
            $account->save();
        }

        return redirect('admin/member/list')->with([
            'flash_level' => 'success',
            'flash_message' => "Tài khoản \"{$account->username}\" đã được mở khóa."
        ]);
    }
}
