<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    //

    public function sendResetLinkEmail(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        $token = Str::random(60);
        $hashedToken = Hash::make($token); // Hash trước khi lưu

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $hashedToken, 'created_at' => now()]
        );

        // Gửi email reset mật khẩu
        Mail::to($user->email)->queue(new ResetPasswordMail($user->email, $token));

        return response()->json(['message' => 'Liên kết đặt lại mật khẩu đã được gửi!'], 200);
    }
}
