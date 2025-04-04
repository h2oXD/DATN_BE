<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;


/**
 * @OA\Info(
 *      title="API Documentation",
 *      version="1.0.0",
 *      description="Tài liệu API của hệ thống học trực tuyến",
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server"
 * )
 */
class AuthController extends Controller
{

    public function broadcasting()
    {
        $user = request()->user();
        Log::info($user);
        if (!$user) {
            return response()->json(['Bạn không có quyền'], 403);
        }
        return Broadcast::auth(request());
    }
    public function getUser(Request $request)
    {
        $user = $request->user();
        $user->roles;
        return response()->json($user);
    }

    /**
     * @OA\Post(
     *      path="/api/register",
     *      tags={"Authentication"},
     *      summary="Đăng ký tài khoản mới",
     *      description="Cho phép người dùng đăng ký tài khoản với tên, email và mật khẩu.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email","password","password_confirmation"},
     *              @OA\Property(property="name", type="string", example="Nguyễn Hữu Hào"),
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123"),
     *              @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Đăng ký thành công",
     *          @OA\JsonContent(
     *              @OA\Property(property="user", type="integer", example=1),
     *              @OA\Property(property="role", type="string", example="student"),
     *              @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1Q...")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Lỗi validation",
     *          @OA\JsonContent(
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="email", type="array", @OA\Items(type="string", example="Email đã tồn tại")),
     *                  @OA\Property(property="password", type="array", @OA\Items(type="string", example="Mật khẩu ít nhất 8 ký tự"))
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Lỗi hệ thống"
     *      )
     * )
     */
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
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
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
            $role = Role::where('name', 'student')->first();
            $user->roles()->attach($role->id);

            $user->wallet()->create(['balance' => 0]);
            $token = $user->createToken(__CLASS__)->plainTextToken;

            DB::commit();

            return response()->json([
                'user' => $user->id,
                'role' => $role,
                'token' => $token
            ], Response::HTTP_CREATED);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Lỗi hệ thống'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/login",
     *      tags={"Authentication"},
     *      summary="Đăng nhập vào hệ thống",
     *      description="Yêu cầu đăng nhập với email và password",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Đăng nhập thành công",
     *          @OA\JsonContent(
     *              @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1...")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Thông tin đăng nhập không chính xác"
     *      )
     * )
     */
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

        $role = $user->roles;

        if ($user->hasRole('admin')) {
            return response()->json(['message' => 'Thông tin đăng nhập không chính xác'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken(__CLASS__)->plainTextToken;

        return response()->json([
            'user_id' => $user->id,
            'token' => $token,
            'role' => $role
        ], Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công'
        ], Response::HTTP_OK);
    }
}
