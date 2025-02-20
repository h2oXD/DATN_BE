<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LecturerRegister;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class LecturerRegisterController extends Controller
{
     /**
     * @OA\Post(
     *     path="/api/v1/lecturer-register/submit-answers",
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

        if ($user->hasRole('lecturer')) {
            return response()->json(['message' => 'nguoi dung da la giang vien'], Response::HTTP_FORBIDDEN);
        }
        if ((empty($user->bio) || empty($user->profile_picture) || empty($user->phone_number))) {
            return response()->json(['message' => 'ko du thong tin'], Response::HTTP_FORBIDDEN);
        }
        $validator = Validator::make($request->all(), [
            'answer1' => 'required|max:255',
            'answer2' => 'required|max:255',
            'answer3' => 'required|max:255',
            'certificate_file' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_FORBIDDEN);
        }


        if ($request->hasFile('certificate_file')) {
            $path = $request->file('certificate_file')->store('certificates');
            $user->update(['certificate_file' => $path]);
        }
        LecturerRegister::create([
            'user_id' => $user->id,
            'answer1' => $request->answer1,
            'answer2' => $request->answer2,
            'answer3' => $request->answer3,

        ]);
        return response()->json([
            'answer1' => $request->answer1,
            'answer2' => $request->answer2,
            'answer3' => $request->answer3,
            'message' => 'Answers submitted successfully'

        ], Response::HTTP_OK);
    }
}
