<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        
    }

    /**
     * @OA\Put(
     *     path="api/users",
     *     summary="Cập nhật thông tin người dùng",
     *     description="Cập nhật thông tin hồ sơ của người dùng đã đăng nhập.",
     *     operationId="updateUserProfile",
     *     tags={"User"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "phone_number", "profile_picture", "bio"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="John Doe"),
     *             @OA\Property(property="phone_number", type="string", maxLength=20, example="+84901234567"),
     *             @OA\Property(property="profile_picture", type="string", format="binary"),
     *             @OA\Property(property="bio", type="string", maxLength=255, example="Backend Developer"),
     *             @OA\Property(property="linkedin_url", type="string", nullable=true, example="https://linkedin.com/in/johndoe"),
     *             @OA\Property(property="website_url", type="string", nullable=true, example="https://johndoe.com"),
     *             @OA\Property(property="country", type="string", maxLength=255, nullable=true, example="Vietnam"),
     *             @OA\Property(property="province", type="string", maxLength=255, nullable=true, example="Hanoi"),
     *             @OA\Property(property="birth_date", type="string", format="date", nullable=true, example="1990-01-01"),
     *             @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, nullable=true, example="male")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thông tin người dùng đã được cập nhật thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User info updated successfully"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Người dùng không tồn tại",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Lỗi xác thực dữ liệu",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
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
            'country' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
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

    /**
     * @OA\Post(
     * path="/lecturer/insertBank",
     * summary="Thêm thông tin ngân hàng cho giảng viên",
     * description="Thêm thông tin ngân hàng cho người dùng có vai trò giảng viên.",
     * tags={"Lecturer"},
     * security={{"sanctum": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"bank_name", "bank_nameUser", "bank_number"},
     * @OA\Property(property="bank_name", type="string", example="Vietcombank", description="Tên ngân hàng"),
     * @OA\Property(property="bank_nameUser", type="string", example="Nguyen Van A", description="Tên chủ tài khoản"),
     * @OA\Property(property="bank_number", type="integer", example=1234567890, description="Số tài khoản ngân hàng")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Thêm thông tin ngân hàng thành công",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Thêm thông tin ngân hàng thành công")
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Lỗi server hoặc lỗi dữ liệu",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Lỗi server"),
     * @OA\Property(property="error", type="string", example="Chi tiết lỗi")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Không xác thực",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated.")
     * )
     * ),
     * @OA\Response(
     * response=403,
     * description="Không có quyền truy cập",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="error"),
     * @OA\Property(property="message", type="string", example="Chỉ tài khoản giảng viên mới có thể thao tác")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Không tìm thấy người dùng",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Không tìm thấy người dùng")
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Lỗi xác thực dữ liệu đầu vào",
     * @OA\JsonContent(
     * @OA\Property(property="errors", type="object",
     * @OA\Property(property="bank_name", type="array", @OA\Items(type="string", example="The bank name field is required.")),
     * @OA\Property(property="bank_nameUser", type="array", @OA\Items(type="string", example="The bank name user field is required.")),
     * @OA\Property(property="bank_number", type="array", @OA\Items(type="string", example="The bank number field is required."))
     * )
     * )
     * )
     * )
     */
    public function insertBank(Request $request)
    {
        try {

            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Không tìm thấy người dùng'
                ], Response::HTTP_NOT_FOUND);
            }

            // Kiểm tra dữ liệu truyền lên
            $validator = Validator::make($request->all(), [
                'bank_name' => 'required|string',
                'bank_nameUser' => 'required|string',
                'bank_number' => 'required|int'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $user->update([
                'bank_name' => $request->bank_name,
                'bank_nameUser' => $request->bank_nameUser,
                'bank_number' => $request->bank_number
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Thêm thông tin ngân hàng thành công'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     * path="/lecturer/getBank",
     * summary="Lấy thông tin ngân hàng của giảng viên",
     * description="Lấy thông tin ngân hàng của người dùng có vai trò giảng viên.",
     * tags={"Lecturer"},
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Thông tin ngân hàng của giảng viên",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="infoBank", type="object",
     * @OA\Property(property="bank_name", type="string", example="Vietcombank"),
     * @OA\Property(property="bank_nameUser", type="string", example="Nguyen Van A"),
     * @OA\Property(property="bank_number", type="integer", example="1234567890")
     * )
     * )
     * ),
     * @OA\Response(
     * response=204,
     * description="Không có thông tin ngân hàng",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="infoBank", type="string", example="Không có thông tin")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Xác thực không thành công",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated.")
     * )
     * ),
     * @OA\Response(
     * response=403,
     * description="Không có quyền truy cập",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="error"),
     * @OA\Property(property="message", type="string", example="Chỉ tài khoản giảng viên mới có thể thao tác")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Người dùng không tồn tại",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Không tìm thấy người dùng")
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Lỗi server",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Lỗi server"),
     * @OA\Property(property="error", type="string", example="Chi tiết lỗi")
     * )
     * )
     * )
     */
    public function getBank(Request $request)
    {
        try {

            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Không tìm thấy người dùng'
                ], Response::HTTP_NOT_FOUND);
            }

            $infoBank = [
                'bank_name' => $user->bank_name,
                'bank_nameUser' => $user->bank_nameUser,
                'bank_number' => $user->bank_number
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
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
