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
