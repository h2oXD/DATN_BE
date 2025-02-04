<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function getUser(Request $request)
    {
        $user = User::with('roles')->find($request->user()->id);

        if (!$user) {
            return response()->json(['message' => 'Người dùng không tồn tại'], 404);
        }

        return response()->json($user);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'min:8', 'confirmed']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();
        try {
            /**
             * @var User $user
             */
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            $role = Role::select('id', 'name')->where('name', 'student')->first();
            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $role->id
            ]);
            $token = $user->createToken(__CLASS__)->plainTextToken;

            DB::commit();

            return response()->json([
                'user' => $user->id,
                'role' => $role,
                'token' => $token
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Lỗi hệ thống'
            ], 500);
        }
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Thông tin đăng nhập không chính xác'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken(__CLASS__)->plainTextToken;

        $role = UserRole::where('user_id', $user->id)->first();
        $roleName = Role::where('id', $role->role_id)->first()->name;

        return response()->json([
            'user' => $user->id,
            'role' => $roleName,
            'token' => $token
        ], Response::HTTP_OK);

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công'
        ]);
    }
}
