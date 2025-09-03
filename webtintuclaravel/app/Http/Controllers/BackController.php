<?php

namespace App\Http\Controllers;

use Intervention\Image\Facades\Image;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\System;
use App\Models\Page;
use App\Models\Social;
use App\Models\Newsletter;
use App\Models\Contact;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Slider;


class BackController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function home()
    {
        $user = Auth::user();
        return view('back.home.home');
    }

    public function staff_profile()
    {
        return view('back.staff.profile');
    }

    public function staff_profile_post(Request $request)
    {
        if ($request->fullname == '' || $request->email == '' || $request->phone == '') {
            return redirect('admin/staff/profile')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Vui lòng điền vào các trường có dấu *'
            ]);
        }

        $User = User::find($request->id);
        $User->fullname = $request->fullname;
        $User->address = $request->address;
        $User->email = $request->email;
        $User->phone = $request->phone;

        if (isset($request->password) && $request->password != '') {
            $User->password = bcrypt($request->password);
        }

        $Flag = $User->save();

        if ($Flag == true) {
            return redirect('admin/staff/profile')->with([
                'flash_level' => 'success',
                'flash_message' => 'Cập nhật thông tin tài khoản thành công.'
            ]);
        } else {
            return redirect('admin/staff/profile')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Chỉnh sửa tài khoản không thành công.'
            ]);
        }
    }

    public function staff_list()
    {
        $user = DB::table('users as a')
            ->join('users_level as b', 'a.level', '=', 'b.id')
            ->selectRaw('a.id, a.fullname, a.address, a.email, a.phone, b.name')
            ->get();

        return view('back.staff.list', compact('user'));
    }

    public function staff_add()
    {
        $UserLevel = UserLevel::where('status', 1)->get();
        return view('back.staff.add', compact('UserLevel'));
    }

    public function staff_add_post(Request $request)
    {
        if ($request->fullname == '' || $request->email == '' || $request->phone == '' || $request->username == '' || $request->password == '') {
            return redirect('admin/staff/add')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Vui lòng điền vào các trường có dấu *'
            ]);
        }

        $User = new User;
        $User->level = $request->level;
        $User->status = 1;
        $User->username = $request->username;
        $User->password = bcrypt($request->password);
        $User->fullname = $request->fullname;
        $User->address = $request->address;
        $User->email = $request->email;
        $User->phone = $request->phone;

        $Flag = $User->save();

        if ($Flag) {
            return redirect('admin/staff/list')->with([
                'flash_level' => 'success',
                'flash_message' => 'Thêm nhân viên thành công'
            ]);
        } else {
            return redirect('admin/staff/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Lỗi thêm nhân viên'
            ]);
        }
    }
    public function staff_edit(Request $request, $id)
    {
        $User = User::find($id);
        $UserLevel = UserLevel::where('status', 1)->get();
        return view('back.staff.edit', compact('User', 'UserLevel'));
    }

    public function staff_edit_post(Request $request, $id)
    {
        if ($request->fullname == '' || $request->email == '' || $request->phone == '') {
            return redirect('admin/staff/add')->with(['flash_level' => 'danger', 'flash_message' => 'Vui lòng điền vào các trường có dấu *']);
        }
        $User = User::find($id);
        $User->level = $request->level;
        $User->status = $request->status;

        if (isset($request->password) && $request->password != '') {
            $User->password = bcrypt($request->password);
        }

        $User->fullname = $request->fullname;
        $User->address = $request->address;
        $User->email = $request->email;
        $User->phone = $request->phone;
        $Flag = $User->save();

        if ($Flag == true) {
            return redirect('admin/staff/edit/' . $id)->with(['flash_level' => 'success', 'flash_message' => 'Chỉnh sửa nhân viên thành công']);
        } else {
            return redirect('admin/staff/edit/' . $id)->with(['flash_level' => 'danger', 'flash_message' => 'Lỗi chỉnh sửa nhân viên']);
        }
    }
    public function staff_delete(Request $request, $id)
    {
        $User = User::find($id);
        $Flag = $User->delete();

        if ($Flag == true) {
            return redirect('admin/staff/list')->with(['flash_level' => 'success', 'flash_message' => 'Xóa nhân viên thành công']);
        } else {
            return redirect('admin/staff/list')->with(['flash_level' => 'danger', 'flash_message' => 'Lỗi xóa nhân viên']);
        }
    }
    public function system()
    {
        $name = System::where('Status', 1)->where('Code', 'name')->first();
        $email = System::where('Status', 1)->where('Code', 'email')->first();
        $phone = System::where('Status', 1)->where('Code', 'phone')->first();
        $address = System::where('Status', 1)->where('Code', 'address')->first();
        $copyright = System::where('Status', 1)->where('Code', 'copyright')->first();
        $logo = System::where('Status', 1)->where('Code', 'logo')->first();
        $favicon = System::where('Status', 1)->where('Code', 'favicon')->first();
        $map = System::where('Status', 1)->where('Code', 'map')->first();

        return view('back.system.system', compact(
            'name',
            'logo',
            'email',
            'favicon',
            'address',
            'copyright',
            'phone',
            'map'
        ));
    }


    public function system_post(Request $request)
    {
        if ($request->name == '' || $request->email == '' || $request->phone == '') {
            return redirect('admin/system')->with(['flash_level' => 'danger', 'flash_message' => 'Vui lòng điền vào các trường có dấu *']);
        }

        // update ten cong ty
        System::where('Status', 1)
            ->where('Code', 'name')
            ->update(['Description' => $request->name]);

        System::where('Status', 1)
            ->where('Code', 'email')
            ->update(['Description' => $request->email]);

        System::where('Status', 1)
            ->where('Code', 'phone')
            ->update(['Description' => $request->phone]);

        System::where('Status', 1)
            ->where('Code', 'address')
            ->update(['Description' => $request->address]);

        System::where('Status', 1)
            ->where('Code', 'copyright')
            ->update(['Description' => $request->copyright]);

        System::where('Status', 1)
            ->where('Code', 'map')
            ->update(['Description' => $request->map]);

        //logo
        if (!empty($request->file('logo'))) {
            $logo = System::where('Status', 1)->where('Code', 'logo')->first();
            $path = 'images/logo/' . $logo->Description;
            if (File::exists($path)) {
                File::delete($path);
            }

            //upload images
            $name = $request->file('logo')->getClientOriginalName();
            $request->file('logo')->move('images/logo/', $name);

            $logo->Description = $name;
            $logo->save();
        }

        //favicon
        if (!empty($request->file('favicon'))) {
            $favicon = System::where('Status', 1)->where('Code', 'favicon')->first();
            $path = 'images/favicon/' . $favicon->Description;
            if (File::exists($path)) {
                File::delete($path);
            }
            //upload images
            $name = $request->file('favicon')->getClientOriginalName();
            $request->file('favicon')->move('images/favicon/', $name);

            $favicon->Description = $name;
            $favicon->save();
        }

        return redirect('admin/system')->with(['flash_level' => 'success', 'flash_message' => 'Chỉnh sửa thành công']);
    }


    // quản lý danh mục tin tức  
    public function news_cat_list()
    {
        $NewsCategory = NewsCategory::where('Status', 1)->get();
        return view('back.news.cat_list', compact('NewsCategory'));
    }

    public function news_cat_getedit($RowID)
    {
        $NewsCategory = NewsCategory::find($RowID);
        return view('back.news.cat_edit', compact('NewsCategory'));
    }

    public function news_cat_edit(Request $request, $RowID)
    {
        if ($request->Name == '') {
            return redirect('admin/news_cat/edit/' . $RowID)->with([
                'flash_level' => 'danger',
                'flash_message' => 'Vui lòng điền vào các trường có dấu *'
            ]);
        }

        $NewsCategory = NewsCategory::find($RowID);
        $NewsCategory->Status = $request->Status;
        $NewsCategory->Name = $request->Name;
        $NewsCategory->Alias = $request->Alias;
        $Flag = $NewsCategory->save();

        if ($Flag == true) {
            return redirect('admin/news_cat/edit/' . $RowID)->with([
                'flash_level' => 'success',
                'flash_message' => 'Chỉnh sửa danh mục tin tức thành công'
            ]);
        } else {
            return redirect('admin/news_cat/edit/' . $RowID)->with([
                'flash_level' => 'danger',
                'flash_message' => 'Lỗi chỉnh sửa danh mục tin tức'
            ]);
        }
    }
    // quản lý danh mục tin tức    

    // news management
    public function news_list()
    {
        $News = DB::table('news as a')
            ->join('news_cat as b', 'a.RowIDCat', '=', 'b.RowID')
            ->selectRaw('a.*, b.Name as CategoryName')
            ->orderBy('a.RowID', 'DESC')
            ->get();
        return view('back.news.list', compact('News'));
    }

    public function news_getAdd()
    {
        $NewsCategory = NewsCategory::get();
        return view('back.news.add', compact('NewsCategory'));
    }

    public function news_add(Request $request)
    {
        // Kiểm tra các trường bắt buộc
        if ($request->Name == '' || $request->Description == '') {
            return redirect('admin/news/add')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Vui lòng điền vào các trường có dấu *'
            ]);
        }

        $News = new News;
        $News->RowIDCat = $request->RowIDCat;
        $News->Status = $request->Status;
        $News->Name = $request->Name;
        $News->Alias = $request->Alias;
        $News->Views = $request->Views;
        $News->MetaTitle = $request->MetaTitle;
        $News->MetaDescription = $request->MetaDescription;
        $News->MetaKeyword = $request->MetaKeyword;
        $News->SmallDescription = $request->SmallDescription;
        $News->Description = $request->Description;

        // Xử lý ảnh nếu có
        if ($request->hasFile('Images')) {
            $file = $request->file('Images');
            if (!$file->isValid()) {
                return back()->with([
                    'flash_level' => 'danger',
                    'flash_message' => 'Lỗi tải lên file'
                ]);
            }

            $random_digit = rand(000000000, 999999999);
            $name = $random_digit . '-' . $file->getClientOriginalName();
            $duoi = strtolower($file->getClientOriginalExtension());

            // Kiểm tra định dạng ảnh
            if (!in_array($duoi, ['png', 'jpg', 'jpeg', 'svg'])) {
                return back()->with([
                    'flash_level' => 'danger',
                    'flash_message' => 'Phần mở rộng ảnh không được hỗ trợ'
                ]);
            }

            // Di chuyển file tạm thời
            $tempPath = 'images/news/temp';
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            $file->move($tempPath, $name);

            // Xử lý ảnh bằng Intervention Image
            $img = Image::make($tempPath . '/' . $name);
            $filePath = 'images/news/' . date('Ymd');
            if (!file_exists($filePath)) {
                mkdir($filePath, 0755, true);
            }
            $img->fit(208, 141);
            $img->save($filePath . '/' . $name);

            // Xóa file tạm sau khi xử lý
            if (file_exists($tempPath . '/' . $name)) {
                unlink($tempPath . '/' . $name);
            }

            $News->Images = date('Ymd') . '/' . $name;
        }

        $Flag = $News->save();

        if ($Flag) {
            return redirect('admin/news/list')->with([
                'flash_level' => 'success',
                'flash_message' => 'Thêm tin tức thành công'
            ]);
        } else {
            return redirect('admin/news/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Lỗi thêm tin tức'
            ]);
        }
    }
    public function news_delete(Request $request, $RowID)
    {
        $News = News::find($RowID);

        if ($News->Images != '') {
            if (file_exists('images/news/' . $News->Images)) {
                unlink('images/news/' . $News->Images);
            }
        }

        $Flag = $News->delete();

        if ($Flag == true) {
            return redirect('admin/news/list')->with(['flash_level' => 'success', 'flash_message' => 'Xóa tin tức thành công']);
        } else {
            return redirect('admin/news/list')->with(['flash_level' => 'danger', 'flash_message' => 'Lỗi xóa tin tức']);
        }
    }

    public function news_getedit(Request $request, $RowID)
    {
        $NewsCategory = NewsCategory::get();
        $News = News::find($RowID);
        return view('back.news.edit', compact('News', 'NewsCategory'));
    }

    public function news_edit(Request $request, $RowID)
    {
        if ($request->Name == '' || $request->Description == '') {
            return redirect('admin/news/edit/' . $RowID)->with([
                'flash_level' => 'danger',
                'flash_message' => 'Vui lòng điền vào các trường có dấu *'
            ]);
        }

        $News = News::find($RowID);
        $News->RowIDCat = $request->RowIDCat;
        $News->Status = $request->Status;
        $News->Name = $request->Name;
        $News->Alias = $request->Alias;
        $News->MetaTitle = $request->MetaTitle;
        $News->MetaDescription = $request->MetaDescription;
        $News->MetaKeyword = $request->MetaKeyword;
        $News->SmallDescription = $request->SmallDescription;
        $News->Description = $request->Description;
        $News->Views = $request->Views;

        if ($request->hasFile('Images')) {
            $file = $request->file('Images');
            $random_digit = rand(000000000, 999999999);
            $name = $random_digit . '.' . $file->getClientOriginalName();
            $duoi = strtolower($file->getClientOriginalExtension());

            // Kiểm tra định dạng ảnh
            if (!in_array($duoi, ['png', 'jpg', 'jpeg', 'svg'])) {
                return back()->with([
                    'flash_level' => 'danger',
                    'flash_message' => 'Phần mở rộng ảnh không được hỗ trợ'
                ]);
            }

            if ($News->Images != '') {
                if (file_exists('images/news/' . $News->Images)) {
                    unlink('images/news/' . $News->Images);
                }
            }

            $file->move('images/news', $name);
            $img = Image::make('images/news/' . $name);

            // kiểm tra, nếu không tồn tại thì tạo folder
            $filePath = "images/news/" . date("Ymd");
            if (!file_exists($filePath)) {
                mkdir("images/news/" . date("Ymd"), 0777, true);
            }

            $img->fit(208, 141);
            $img->save('images/news/' . date("Ymd") . '/' . $name);

            // delete images upload
            if (file_exists('images/news/' . $name)) {
                unlink('images/news/' . $name);
            }
            $News->Images = date('Ymd') . '/' . $name;
        }


        $Flag = $News->save();

        if ($Flag == true) {
            return redirect('admin/news/edit/' . $RowID)->with([
                'flash_level' => 'success',
                'flash_message' => 'Chỉnh sửa tin tức thành công'
            ]);
        } else {
            return redirect('admin/staff/list/' . $RowID)->with([
                'flash_level' => 'danger',
                'flash_message' => 'Lỗi chỉnh sửa tin tức'
            ]);
        }
    }


    /* slider -----------------------------------------------------------------*/
    public function slider_list()
    {
        $Slider = Slider::selectRaw('*')
            ->orderBy('RowID', 'DESC')
            ->get();

        return view('back.slider.list', compact('Slider'));
    }

    public function slider_getAdd()
    {
        return view('back.slider.add');
    }


    public function slider_add(Request $request)
    {
        if ($request->Name == '' || $request->Alias == '') {
            return redirect('admin/slider/add/')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Vui lòng điền vào các trường có dấu *'
            ]);
        }

        $Slider = new Slider;
        $Slider->Status = $request->Status;
        $Slider->Name = $request->Name;
        $Slider->Alias = $request->Alias;
        $Slider->Sort = $request->Sort;

        // Xử lý ảnh nếu có
        if ($request->hasFile('Images')) {
            $file = $request->file('Images');
            if (!$file->isValid()) {
                return back()->with([
                    'flash_level' => 'danger',
                    'flash_message' => 'Lỗi tải lên file'
                ]);
            }

            $random_digit = rand(000000000, 999999999);
            $name = $random_digit . '-' . $file->getClientOriginalName();
            $duoi = strtolower($file->getClientOriginalExtension());

            // Kiểm tra định dạng ảnh
            if (!in_array($duoi, ['png', 'jpg', 'jpeg', 'svg'])) {
                return back()->with([
                    'flash_level' => 'danger',
                    'flash_message' => 'Phần mở rộng ảnh không được hỗ trợ'
                ]);
            }

            // Di chuyển file tạm thời
            $tempPath = 'images/slider/temp';
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            $file->move($tempPath, $name);

            // Xử lý ảnh bằng Intervention Image
            $img = Image::make($tempPath . '/' . $name);
            $filePath = 'images/slider/' . date('Ymd');
            if (!file_exists($filePath)) {
                mkdir($filePath, 0755, true);
            }
            $img->fit(1920, 760);
            $img->save($filePath . '/' . $name);

            // Xóa file tạm sau khi xử lý
            if (file_exists($tempPath . '/' . $name)) {
                unlink($tempPath . '/' . $name);
            }

            $Slider->Images = date('Ymd') . '/' . $name;
        }

        $Flag = $Slider->save();

        if ($Flag) {
            return redirect('admin/slider/list')->with([
                'flash_level' => 'success',
                'flash_message' => 'Thêm slideshow thành công'
            ]);
        } else {
            return redirect('admin/slider/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Lỗi thêm slideshow'
            ]);
        }
    }
    public function slider_delete(Request $request, $RowID)
    {
        $Slider = Slider::find($RowID);

        if ($Slider->Images != '') {
            if (file_exists('images/slider/' . $Slider->Images)) {
                unlink('images/slider/' . $Slider->Images);
            }
        }

        $Flag = $Slider->delete();

        if ($Flag == true) {
            return redirect('admin/slider/list')->with(['flash_level' => 'success', 'flash_message' => 'Xóa slideshow thành công']);
        } else {
            return redirect('admin/slider/list')->with(['flash_level' => 'danger', 'flash_message' => 'Lỗi xóa slideshow']);
        }
    }

    public function slider_getedit(Request $request, $RowID)
    {
        $Slider = Slider::find($RowID);
        return view('back.slider.edit', compact('Slider'));
    }

    public function slider_edit(Request $request, $RowID)
    {
        if ($request->Name == '' || $request->Alias == '') {
            return redirect('admin/slider/edit/' . $RowID)->with([
                'flash_level' => 'danger',
                'flash_message' => 'Vui lòng điền vào các trường có dấu *'
            ]);
        }

        $Slider = Slider::find($RowID);
        $Slider->Status = $request->Status;
        $Slider->Name = $request->Name;
        $Slider->Alias = $request->Alias;
        $Slider->Sort = $request->Sort;

        if ($request->hasFile('Images')) {
            $file = $request->file('Images');
            $random_digit = rand(000000000, 999999999);
            $name = $random_digit . '.' . $file->getClientOriginalName();
            $duoi = strtolower($file->getClientOriginalExtension());

            // Kiểm tra định dạng ảnh
            if (!in_array($duoi, ['png', 'jpg', 'jpeg', 'svg'])) {
                return back()->with([
                    'flash_level' => 'danger',
                    'flash_message' => 'Phần mở rộng ảnh không được hỗ trợ'
                ]);
            }

            if ($Slider->Images != '') {
                if (file_exists('images/slider/' . $Slider->Images)) {
                    unlink('images/slider/' . $Slider->Images);
                }
            }

            $file->move('images/slider', $name);
            $img = Image::make('images/slider/' . $name);

            // kiểm tra, nếu không tồn tại thì tạo folder
            $filePath = "images/slider/" . date("Ymd");
            if (!file_exists($filePath)) {
                mkdir("images/slider/" . date("Ymd"), 0777, true);
            }

            $img->fit(1920, 760);
            $img->save('images/slider/' . date("Ymd") . '/' . $name);

            // delete images upload
            if (file_exists('images/slider/' . $name)) {
                unlink('images/slider/' . $name);
            }
            $Slider->Images = date('Ymd') . '/' . $name;
        }


        $Flag = $Slider->save();

        if ($Flag == true) {
            return redirect('admin/slider/edit/' . $RowID)->with([
                'flash_level' => 'success',
                'flash_message' => 'Chỉnh sửa slidershow thành công'
            ]);
        } else {
            return redirect('admin/slider/list/' . $RowID)->with([
                'flash_level' => 'danger',
                'flash_message' => 'Lỗi chỉnh sửa slideshow'
            ]);
        }
    }

    /* slider -----------------------------------------------------------------*/





    // Quản lý trang----------------------------
    public function page_list()
    {
        $page = Page::get();
        return view('back.page.list', compact('page')); // Sửa 'Page' thành 'page'
    }

    public function page_edit(Request $request, $id)
    {
        $page = Page::find($id);
        return view('back.page.edit', compact('page'));
    }
    public function page_edit_post(Request $request, $id)
    {
        if ($request->Name == '') {
            return redirect('admin/page/edit/' . $id)->with(['flash_level' => 'danger', 'flash_message' => 'Vui lòng điền vào các trường có dấu *']);
        }

        $Page = Page::find($id);
        $Page->Name = $request->Name;
        $Page->Alias = $request->Alias;
        $Page->Status = $request->Status;
        $Page->Font = $request->Font;
        $Page->Sort = $request->Sort;

        $Page->MetaTitle = $request->MetaTitle;
        $Page->MetaDescription = $request->MetaDescription;
        $Page->MetaKeyword = $request->MetaKeyword;
        $Page->Description = $request->Description;

        // the images
        if ($request->hasFile('Images')) {
            $file = $request->file('Images');
            $random_digit = rand(00000000, 99999999);
            $name = $random_digit . '_' . $file->getClientOriginalName();
            $duoi = strtolower($file->getClientOriginalExtension());

            if ($duoi != 'png' && $duoi != 'jpg' && $duoi != 'jpeg' && $duoi != 'svg') {
                return back()->with(['flash_level' => 'danger', 'flash_message' => 'Phần mở rộng ảnh không được hỗ trợ.']);
            }

            // xóa ảnh đại diện cũ
            if ($Page->Images != '') {
                if (file_exists('images/page/' . $Page->Images)) {
                    unlink('images/page/' . $Page->Images);
                }
            }


            $file->move('images/page', $name);

            $img = Image::make('images/page/' . $name);
            // check folder images exist
            $filePath = "images/page/" . date('Ymd');
            if (!file_exists($filePath)) {
                mkdir("images/page/" . date('Ymd'), 0777, true);
            }
            $img->fit(300, 250);
            $img->save('images/page/' . date('Ymd') . '/' . $name);

            // delete images upload
            if (file_exists('images/page/' . $name)) {
                unlink('images/page/' . $name);
            }

            $Page->Images = date('Ymd') . '/' . $name;
        }


        $Flag = $Page->save();

        if ($Flag == true) {
            return redirect('admin/page/edit/' . $id)->with(['flash_level' => 'success', 'flash_message' => 'Chỉnh sửa trang thành công']);
        } else {
            return redirect('admin/page/edit/' . $id)->with(['flash_level' => 'danger', 'flash_message' => 'Lỗi chỉnh sửa trang']);
        }
    }

    // Quản lý trang----------------------------

    // quản lý nhận tin khuyến mại-----------
    public function newsletter_list()
    {
        $Newsletter = NewsLetter::get();
        return view('back.newsletter.list', compact('Newsletter'));
    }

    public function newsletter_edit(Request $request, $id)
    {
        $Newsletter = NewsLetter::find($id);
        return view('back.newsletter.edit', compact('Newsletter'));
    }

    public function newsletter_edit_post(Request $request, $id)
    {
        if ($request->Email == '') {
            return redirect('admin/newsletter/edit/' . $id)->with([
                'flash_level' => 'danger',
                'flash_message' => 'Vui lòng điền vào các trường có dấu *'
            ]);
        }

        $Newsletter = NewsLetter::find($id);
        $Newsletter->Email = $request->Email;
        $Newsletter->IsViews = $request->IsViews;
        $Flag = $Newsletter->save();

        if ($Flag == true) {
            return redirect('admin/newsletter/edit/' . $id)->with([
                'flash_level' => 'success',
                'flash_message' => 'Chỉnh sửa email khuyến mại thành công'
            ]);
        } else {
            return redirect('admin/newsletter/edit/' . $id)->with([
                'flash_level' => 'danger',
                'flash_message' => 'Lỗi chỉnh sửa email khuyến mại'
            ]);
        }
    }
    public function newsletter_delete(Request $request, $id)
    {
        $Newsletter = Newsletter::find($id);
        $Flag = $Newsletter->delete();

        if ($Flag == true) {
            return redirect('admin/newsletter/list')->with([
                'flash_level' => 'success',
                'flash_message' => 'Xóa email khuyến mại thành công'
            ]);
        } else {
            return redirect('admin/newsletter/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Lỗi xóa email khuyến mại'
            ]);
        }
    }
    // quản lý nhận tin khuyến mại-----------


    // Contact management
    public function contact_list()
    {
        $Contact = Contact::orderBy('RowID', 'DESC')->get();
        return view('back.contact.list', compact('Contact'));
    }

    public function contact_edit(Request $request, $id)
    {
        $Contact = Contact::find($id);
        return view('back.contact.edit', compact('Contact'));
    }

    public function contact_edit_post(Request $request, $id)
    {
        if ($request->Name1 == '' || $request->Email == '' || $request->txtPhone == '' || $request->Message == '') {
            return redirect('admin/contact/edit/' . $id)->with([
                'flash_level' => 'danger',
                'flash_message' => 'Vui lòng điền vào các trường có dấu *'
            ]);
        }

        $Contact = Contact::find($id);
        $Contact->Name = $request->Name1;
        $Contact->Email = $request->Email;
        $Contact->Phone = $request->txtPhone;
        $Contact->IsViews = $request->IsViews;
        $Flag = $Contact->save();


        if ($Flag == true) {
            return redirect('admin/contact/edit/' . $id)->with([
                'flash_level' => 'success',
                'flash_message' => 'Chỉnh sửa liên hệ thành công'
            ]);
        } else {
            return redirect('admin/contact/edit/' . $id)->with([
                'flash_level' => 'danger',
                'flash_message' => 'Lỗi chỉnh sửa liên hệ'
            ]);
        }
    }

    public function contact_delete(Request $request, $id)
    {
        $Contact = Contact::find($id);
        $Flag = $Contact->delete();

        if ($Flag == true) {
            return redirect('admin/contact/list')->with([
                'flash_level' => 'success',
                'flash_message' => 'Xóa liên hệ thành công'
            ]);
        } else {
            return redirect('admin/contact/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Lỗi xóa liên hệ'
            ]);
        }
    }
    #Quản lý liên hệ-------------        







    // social management
    public function social_list()
    {
        $Social = Social::get();

        return view('back.social.list', compact('Social'));
    }

    public function social_edit(Request $request, $id)
    {
        $Social = Social::find($id);
        return view('back.social.edit', compact('Social'));
    }
    public function social_edit_post(Request $request, $id)
    {
        if ($request->Name == '' || $request->Font == '') {
            return redirect('admin/social/edit/' . $id)->with([
                'flash_level' => 'danger',
                'flash_message' => 'Vui lòng điền vào các trường có dấu *'
            ]);
        }

        $Social = Social::find($id);
        $Social->Name = $request->Name;
        $Social->Alias = $request->Alias;
        $Social->Status = $request->Status;
        $Social->Font = $request->Font;
        $Social->Sort = $request->Sort;
        $Flag = $Social->save();

        if ($Flag == true) {
            return redirect('admin/social/edit/' . $id)->with([
                'flash_level' => 'success',
                'flash_message' => 'Chỉnh sửa mạng xã hội thành công'
            ]);
        } else {
            return redirect('admin/social/edit/' . $id)->with([
                'flash_level' => 'danger',
                'flash_message' => 'Lỗi chỉnh sửa mạng xã hội'
            ]);
        }
    }
}
