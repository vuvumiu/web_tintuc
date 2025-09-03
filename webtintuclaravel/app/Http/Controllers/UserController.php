<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(){
       
    }

    public function getLogin(){
        return view('auth.login');
    }

    public function postLogin(Request $request){
        if ($request->username == '' || $request->password == '') {
            return redirect('/login')->with('notice', 'Tài khoản hoặc mật khẩu không được để trống.');
        }

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])){
            return redirect('/admin/home');
        }else{
            return redirect('/login')->with('notice', 'Tài khoản và mật khẩu không chính xác');
        }
    }
    public function getLogout(){
        Auth::logout();
        return redirect('/login');
    }
    
}
