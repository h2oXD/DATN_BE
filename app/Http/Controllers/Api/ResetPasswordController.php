<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    //

    /**
     * @OA\Post(
     *     path="/reset-password",
     *     summary="Đặt lại mật khẩu",
     *     description="Cho phép người dùng đặt lại mật khẩu bằng token đã gửi qua email.",
     *     tags={"Reset Password"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             example={
     *                 "email": "user@example.com",
     *                 "token": "abc123",
     *                 "password": "newpassword",
     *                 "password_confirmation": "newpassword"
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mật khẩu đã được đặt lại thành công.",
     *         @OA\JsonContent(
     *             example={"message": "Mật khẩu đã được đặt lại thành công!"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Token không hợp lệ hoặc đã hết hạn.",
     *         @OA\JsonContent(
     *             example={"error": "Token không hợp lệ hoặc đã hết hạn!"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Email không tồn tại.",
     *         @OA\JsonContent(
     *             example={"error": "Email không tồn tại!"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dữ liệu nhập vào không hợp lệ.",
     *         @OA\JsonContent(
     *             example={
     *                 "errors": {
     *                     "email": {"Trường email là bắt buộc."},
     *                     "password": {"Mật khẩu phải có ít nhất 6 ký tự."}
     *                 }
     *             }
     *         )
     *     )
     * )
     */

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Lấy thông tin từ request
        $email = $request->email;
        $token = $request->token;

        // Kiểm tra email có tồn tại không
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['error' => 'Email không tồn tại!'], 404);
        }

        // Kiểm tra token
        $resetData = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetData || !Hash::check($token, $resetData->token)) {
            return response()->json(['error' => 'Token không hợp lệ hoặc đã hết hạn!'], 400);
        }

        // Cập nhật mật khẩu
        $user->password = Hash::make($request->password);
        $user->save();

        // Xóa token sau khi đặt lại mật khẩu
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return response()->json(['message' => 'Mật khẩu đã được đặt lại thành công!'], 200);
    }




    /**
     * @OA\Post(
     *     path="/change-password",
     *     summary="Đổi mật khẩu người dùng",
     *     description="Cho phép người dùng đổi mật khẩu khi đã đăng nhập.",
     *     tags={"Reset Password"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             example={
     *                 "current_password": "oldpassword",
     *                 "password": "newpassword",
     *                 "password_confirmation": "newpassword"
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Đổi mật khẩu thành công.",
     *         @OA\JsonContent(
     *             example={"message": "Đổi mật khẩu thành công."}
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Mật khẩu hiện tại không đúng.",
     *         @OA\JsonContent(
     *             example={"error": "Mật khẩu hiện tại không đúng."}
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dữ liệu nhập vào không hợp lệ.",
     *         @OA\JsonContent(
     *             example={
     *                 "errors": {
     *                     "current_password": {"Trường mật khẩu hiện tại là bắt buộc."},
     *                     "password": {"Mật khẩu phải có ít nhất 6 ký tự."}
     *                 }
     *             }
     *         )
     *     )
     * )
     */


    public function resetPassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra mật khẩu hiện tại có đúng không
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Mật khẩu hiện tại không đúng.'], 403);
        }

        // Cập nhật mật khẩu mới
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Đổi mật khẩu thành công.']);
    }
}
