<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\LecturerRegisterRequested;
use App\Http\Controllers\Controller;
use App\Models\LecturerRegister;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class LecturerRegisterController extends Controller
{
    /**
     * @OA\Post(
     *     path="api/register/answers",
     *     summary="Submit answers for lecturer registration",
     *     description="Gửi câu trả lời đăng ký giảng viên",
     *     tags={"Lecturer Registration"},
     *     security={{"BearerAuth":{}}},
     *     
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Property(property="answer1", type="string", description="Câu trả lời 1", example="Kinh nghiệm giảng dạy"),
     *             @OA\Property(property="answer2", type="string", description="Câu trả lời 2", example="Chứng chỉ sư phạm"),
     *             @OA\Property(property="answer3", type="string", description="Câu trả lời 3", example="Lĩnh vực chuyên môn"),
     *             @OA\Property(property="certificate_file", type="file", description="File chứng chỉ (tùy chọn)")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Answers submitted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="answer1", type="string", example="Kinh nghiệm giảng dạy"),
     *             @OA\Property(property="answer2", type="string", example="Chứng chỉ sư phạm"),
     *             @OA\Property(property="answer3", type="string", example="Lĩnh vực chuyên môn"),
     *             @OA\Property(property="message", type="string", example="Answers submitted successfully")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="nguoi dung da la giang vien hoặc ko du thong tin")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="answer1", type="array", @OA\Items(type="string", example="Answer 1 is required")),
     *                 @OA\Property(property="answer2", type="array", @OA\Items(type="string", example="Answer 2 is required")),
     *                 @OA\Property(property="answer3", type="array", @OA\Items(type="string", example="Answer 3 is required")),
     *                 @OA\Property(property="certificate_file", type="array", @OA\Items(type="string", example="Invalid file format"))
     *             )
     *         )
     *     )
     * )
     */

     public function submitAnswers(Request $request)
     {
         $user = $request->user();
     
         // Kiểm tra nếu user đã là giảng viên
         if ($user->hasRole('lecturer')) {
             return response()->json(['message' => 'Người dùng đã là giảng viên'], Response::HTTP_FORBIDDEN);
         }
     
         // Kiểm tra nếu thiếu thông tin hồ sơ
         if (empty($user->bio) || empty($user->profile_picture) || empty($user->phone_number)) {
             return response()->json(['message' => 'Không đủ thông tin hồ sơ'], Response::HTTP_FORBIDDEN);
         }
     
         // Validate dữ liệu đầu vào
         $validator = Validator::make($request->all(), [
             'answer1' => 'required|max:255',
             'answer2' => 'required|max:255',
             'answer3' => 'required|max:255',
             'certificate_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048', // Chỉ chấp nhận file PDF, JPG, PNG tối đa 2MB
         ]);
     
         if ($validator->fails()) {
             return response()->json(['errors' => $validator->errors()], Response::HTTP_FORBIDDEN);
         }
     
         // Xử lý upload file chứng chỉ nếu có
         if ($request->hasFile('certificate_file')) {
             $path = $request->file('certificate_file')->store('certificates');
             $user->update(['certificate_file' => $path]);
         }
     
         // Tạo yêu cầu xét duyệt giảng viên
         $lecturerRegister = LecturerRegister::create([
             'user_id' => $user->id,
             'answer1' => $request->answer1,
             'answer2' => $request->answer2,
             'answer3' => $request->answer3,
             'status' => 'pending',
         ]);
     
         // Kích hoạt event để xử lý kiểm duyệt tự động
         event(new LecturerRegisterRequested($lecturerRegister));
     
         return response()->json([
             'answer1' => $request->answer1,
             'answer2' => $request->answer2,
             'answer3' => $request->answer3,
             'message' => 'Yêu cầu xét duyệt đã được gửi',
         ], Response::HTTP_OK);
     }
     

    /**
     * Lấy danh sách người dùng đăng ký làm giảng viên.
     *
     * @OA\Get(
     *     path="/api/lecturer-registrations",
     *     summary="Lấy danh sách giảng viên đăng ký",
     *     tags={"Lecturer Registration"},
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách giảng viên đăng ký",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=5),
     *                     @OA\Property(property="answer1", type="string", example="Tôi có kinh nghiệm 5 năm giảng dạy"),
     *                     @OA\Property(property="answer2", type="string", example="Tôi muốn chia sẻ kiến thức về lập trình"),
     *                     @OA\Property(property="answer3", type="string", example="Tôi đã từng dạy tại các trung tâm"),
     *                     @OA\Property(property="admin_rejection_reason", type="string", nullable=true, example=null),
     *                     @OA\Property(property="status", type="string", example="pending"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-22T10:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-22T10:30:00.000000Z"),
     *                     @OA\Property(property="user", type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="name", type="string", example="Nguyễn Văn Thuyết"),
     *                         @OA\Property(property="email", type="string", example="thuyet@example.com"),
     *                         @OA\Property(property="phone_number", type="string", nullable=true, example="0987654321"),
     *                         @OA\Property(property="profile_picture", type="string", nullable=true, example="https://example.com/avatar.jpg"),
     *                         @OA\Property(property="bio", type="string", nullable=true, example="Lập trình viên web với 5 năm kinh nghiệm"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-01T09:00:00.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-20T08:00:00.000000Z")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy giảng viên đăng ký",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Không tìm thấy giảng viên đăng ký.")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getLecturerRegistrations(Request $request)
    {
        $lecturers = LecturerRegister::with('user')->get();

        return response()->json([
            'data' => $lecturers
        ]);
    }
}
