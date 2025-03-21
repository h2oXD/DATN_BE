<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/api/users",
     *     summary="Cập nhật thông tin người dùng",
     *     description="Cập nhật thông tin cá nhân của người dùng đang đăng nhập.",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", maxLength=255, example="Nguyễn Văn A"),
     *                 @OA\Property(property="phone_number", type="string", maxLength=20, example="0987654321"),
     *                 @OA\Property(property="profile_picture", type="string", format="binary"),
     *                 @OA\Property(property="bio", type="string", maxLength=255, example="Lập trình viên web."),
     *                 @OA\Property(property="linkedin_url", type="string", format="url", example="https://linkedin.com/in/username"),
     *                 @OA\Property(property="website_url", type="string", format="url", example="https://example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thông tin người dùng cập nhật thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User info updated successfully"),
     *             @OA\Property(property="message_success", type="string", example="Thông tin đã được cập nhật thành công."),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
     *                 @OA\Property(property="email", type="string", example="nguyenvana@example.com"),
     *                 @OA\Property(property="phone_number", type="string", example="0987654321"),
     *                 @OA\Property(property="profile_picture", type="string", example="profile_pictures/abc123.jpg"),
     *                 @OA\Property(property="bio", type="string", example="Lập trình viên web."),
     *                 @OA\Property(property="linkedin_url", type="string", example="https://linkedin.com/in/username"),
     *                 @OA\Property(property="website_url", type="string", example="https://example.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-21T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-21T12:45:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy người dùng",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Lỗi xác thực đầu vào",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array",
     *                     @OA\Items(type="string", example="The name field is required.")
     *                 ),
     *                 @OA\Property(property="phone_number", type="array",
     *                     @OA\Items(type="string", example="The phone number field must not be greater than 20 characters.")
     *                 ),
     *                 @OA\Property(property="profile_picture", type="array",
     *                     @OA\Items(type="string", example="The profile picture must be an image.")
     *                 ),
     *                 @OA\Property(property="bio", type="array",
     *                     @OA\Items(type="string", example="The bio must not be greater than 255 characters.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */


    public function update(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|max:20',
            'profile_picture' => 'required|image|max:2048',
            'bio' => 'required|max:255|string',
            'linkedin_url' => 'nullable',
            'website_url' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $request->all();

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures');
            $data['profile_picture'] = $path;
        }
        try {
            if ($request->hasFile('profile_picture')) {
                $data['profile_picture'] = Storage::put('profile_pictures', $request->file('profile_picture'));
            }
        } catch (\Throwable $th) {
            if (!empty($data['profile_picture']) && Storage::exists($data['profile_picture'])) {
                Storage::delete($data['profile_picture']);
            }
        }

        $user->update($data);
        return response()->json(['message' => 'User info updated successfully', 'user' => $user], Response::HTTP_OK);
    }

    // Thêm thông tin ngân hàng
    public function insertBank(Request $request)
    {
        try {
            
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Không tìm thấy người dùng'
                ], Response::HTTP_NOT_FOUND);
            }
            // Kiểm tra người dùng có vai trò giảng viên hay không
            if (!$user->roles()->where('name', 'lecturer')->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chỉ tài khoản giảng viên mới có thể thao tác'
                ], Response::HTTP_FORBIDDEN);
            }

            // Kiểm tra dữ liệu truyền lên
            $validator = Validator::make($request->all(), [
                'bank_name'         => 'required|string',
                'bank_nameUser'     => 'required|string',
                'bank_number'       => 'required|int'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $user->update([
                'bank_name'         => $request->bank_name,
                'bank_nameUser'     => $request->bank_nameUser,
                'bank_number'       => $request->bank_number
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Thêm thông tin ngân hàng thành công'
            ], Response::HTTP_OK);
            
        } catch (\Throwable $th) {
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    // Lấy thông tin ngân hàng
    public function getBank(Request $request)
    {
        try {
            
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Không tìm thấy người dùng'
                ], Response::HTTP_NOT_FOUND);
            }
            // Kiểm tra người dùng có vai trò giảng viên hay không
            if (!$user->roles()->where('name', 'lecturer')->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chỉ tài khoản giảng viên mới có thể thao tác'
                ], Response::HTTP_FORBIDDEN);
            }

            $infoBank = [
                'bank_name'         => $user->bank_name,
                'bank_nameUser'     => $user->bank_nameUser,
                'bank_number'       => $user->bank_number
            ];

            if ($infoBank) {
                
                return response()->json([
                    'status' => 'success',
                    'infoBank' => $infoBank
                ], Response::HTTP_OK);

            } else {
                
                return response()->json([
                    'status' => 'success',
                    'infoBank' => 'Không có thông tin'
                ], Response::HTTP_NO_CONTENT);

            }
            
        } catch (\Throwable $th) {
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

}
