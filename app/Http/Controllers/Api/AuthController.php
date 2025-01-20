<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->symbols()]
        ]);

        if ($validator->failed()) {
            return response($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

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
        return response()->json([
            'user' => $user,
            'role' => $role,
            'token' => $token
        ], Response::HTTP_OK);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid login credentials.'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken(__CLASS__)->plainTextToken;

        $role = UserRole::where('user_id', $user->id)->first();
        $roleName = Role::where('id', $role->role_id)->first()->name;

        return response()->json([
            'user' => $user,
            'role' => $roleName,
            'token' => $token
        ], Response::HTTP_OK);
    }
}
