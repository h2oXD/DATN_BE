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
     *     summary="Chuyển hướng người dùng đến Google để xác thực",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=302,
     *         description="Chuyển hướng đến trang đăng nhập của Google",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Lỗi hệ thống: [chi tiết lỗi]")
     *         )
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
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Đăng nhập thành công, trả về thông tin người dùng và access token",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Nguyen Van A"),
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="google_id", type="string", example="1234567890"),
     *             ),
     *             @OA\Property(property="access_token", type="string", example="1|abcdef1234567890"),
     *             @OA\Property(property="roles", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="student")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Tài khoản không được phép đăng nhập",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Tài khoản này không được phép đăng nhập!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Đăng nhập thất bại",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Đăng nhập thất bại!"),
     *             @OA\Property(property="message", type="string", example="Chi tiết lỗi")
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

            if ($user->roles->contains('id', 3)) {
                return redirect("http://localhost:5173/?message=Tài khoản không có quyền truy cập&type=login&status=success");
            }

            // Tạo access token
            $token = $user->createToken('GoogleAuthToken')->plainTextToken;

            return redirect("http://localhost:5173/google/callback?token={$token}");
        } catch (\Exception $e) {
            return response()->json(['error' => 'Đăng nhập thất bại!', 'message' => $e->getMessage()], 500);
        }
    }
}
