<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Hiển thị form đăng nhập (nếu có)
    public function showLoginForm()
    {
        if(Auth::check()){
            return redirect()->route('admin.home');
        }
        return view('auths.login');
    }

    // Đăng nhập Admin
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors(['error' => 'Sai thông tin đăng nhập'])->withInput();
        }
        /**
         * @var User $user
         */
        $user = Auth::user();

        if (!$user->hasRole('admin')) { // 1 là admin
            Auth::logout();
            return back()->withErrors(['error' => 'Bạn không có quyền truy cập']);
        }

        // Tạo session khi đăng nhập thành công
        $request->session()->regenerate();

        return redirect()->route('admin.home');
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('message', 'Đăng xuất thành công');
    }
}
