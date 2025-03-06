<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class GoogleAuthController extends Controller
{
    /**
     * Chuyển hướng người dùng đến trang đăng nhập Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Xử lý phản hồi từ Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Kiểm tra xem user đã tồn tại chưa
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Nếu tài khoản chưa tồn tại, tạo mới
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(16)), // Mật khẩu ngẫu nhiên
                ]);
                $user->roles()->attach(1); // Gán role mặc định
            } else {
                // Nếu tài khoản đã tồn tại nhưng chưa có google_id, cập nhật nó
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                    ]);
                }
            }

            // Tạo access token cho user đã đăng nhập
            $token = $user->createToken('GoogleAuthToken')->plainTextToken;

            return response()->json([
                'user' => $user,
                'access_token' => $token,
                'roles' => $user->roles,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Đăng nhập thất bại!', 'message' => $e->getMessage()], 500);
        }
    }
}
