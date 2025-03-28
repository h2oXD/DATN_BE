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
    /**
     * @OA\Get(
     *     path="/auth/google/redirect",
     *     summary="Chuyển hướng người dùng đến trang đăng nhập Google",
     *     description="API này sẽ chuyển hướng người dùng đến trang xác thực của Google thông qua OAuth2.",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=302,
     *         description="Chuyển hướng đến Google để xác thực"
     *     )
     * )
     */

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * @OA\Get(
     *     path="/auth/google/callback",
     *     summary="Xử lý phản hồi từ Google sau khi xác thực",
     *     description="API này xử lý dữ liệu phản hồi từ Google OAuth và đăng nhập hoặc đăng ký tài khoản người dùng.",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Đăng nhập thành công, chuyển hướng về frontend với token",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="1|xyz123abc456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Tài khoản không có quyền truy cập",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tài khoản không có quyền truy cập"),
     *             @OA\Property(property="type", type="string", example="login"),
     *             @OA\Property(property="status", type="string", example="success")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi trong quá trình xử lý đăng nhập",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Đăng nhập thất bại!"),
     *             @OA\Property(property="message", type="string", example="Chi tiết lỗi từ server")
     *         )
     *     )
     * )
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

            // Tạo access token
            $token = $user->createToken('GoogleAuthToken')->plainTextToken;
            if ($user->roles->contains('id', 3)) {
                return redirect("http://localhost:5173?message=Tài khoản không có quyền truy cập&type=login&status=success");
            }

            return redirect("http://localhost:5173/google/callback?token={$token}");
        } catch (\Exception $e) {
            return response()->json(['error' => 'Đăng nhập thất bại!', 'message' => $e->getMessage()], 500);
        }
    }
}