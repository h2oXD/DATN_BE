<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Progress;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CertificateController extends Controller
{
    /**
     * @OA\Post(
     *     path="/certificates/student/{user_id}/courses/{course_id}",
     *     summary="Cấp chứng chỉ cho người dùng sau khi hoàn thành khóa học",
     *     tags={"Certificate"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID của người dùng",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         required=true,
     *         description="ID của khóa học",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Chứng chỉ đã được cấp thành công",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Chứng chỉ đã được cấp."),
     *             @OA\Property(property="certificate_url", type="string", example="/storage/certificates/1_101_1700000000.pdf")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Người dùng không được phép thực hiện hành động này",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy đăng ký khóa học",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Bạn chưa đăng ký khóa học này.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Người dùng chưa hoàn thành khóa học hoặc chứng chỉ đã được cấp",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Bạn chưa hoàn thành khóa học này."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi hệ thống: [chi tiết lỗi]")
     *         )
     *     )
     * )
     */

    public function createCertificate(Request $request, $course_id)
    {
        try {
            $user_id = $request->user()->id;

            $enrollment = Enrollment::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->first();

            if (!$enrollment) {
                return response()->json(['message' => 'Bạn chưa đăng ký khóa học này.'], Response::HTTP_NOT_FOUND);
            }

            $progress = Progress::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->where('status', 'completed')
                ->first();

            if (!$progress) {
                return response()->json(['message' => 'Bạn chưa hoàn thành khóa học này.'], Response::HTTP_BAD_REQUEST);
            }

            if (Certificate::where('user_id', $user_id)->where('course_id', $course_id)->exists()) {
                return response()->json(['message' => 'Chứng chỉ đã được cấp.'], Response::HTTP_BAD_REQUEST);
            }

            $user = User::find($user_id);
            $course = Course::find($course_id);
            $img = 'https://res.cloudinary.com/dvrexlsgx/image/upload/v1743814633/background_template3_i8nd4m.jpg';
            // Tạo nội dung chứng chỉ, sử dụng view Blade hoặc thư viện PDF.
            $pdf = Pdf::loadView('certificates.template', compact('user', 'course','img'))->setPaper('A4', 'landscape');; // Bạn cần tạo view 'certificates.template.blade.php'
            // Lưu trữ chứng chỉ vào storage.
            $certificateFileName = 'certificates/' . $user_id . '_' . $course_id . '_' . time() . '.pdf';
            Storage::disk('public')->put($certificateFileName, $pdf->output());
            // Tạo bản ghi chứng chỉ trong database.
            Log::info('PDF Output: ' . $pdf->output());
            $certificate = Certificate::create([
                'user_id' => $user_id,
                'course_id' => $course_id,
                'certificate_url' => Storage::url($certificateFileName),
                'issued_at' => now(),
            ]);

            return response()->json([
                'message' => 'Chứng chỉ đã được cấp.',
                'certificate_url' => $certificate->certificate_url,
            ], Response::HTTP_CREATED);


        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi hệ thống: ' . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showCertificate($course_id)
    {
        $user_id = request()->user()->id;
        $certificate = Certificate::where('course_id', $course_id)->where('user_id', $user_id)->first();

        return response()->json([
            'data' => $certificate,
            'message' => 'Lấy chứng chỉ thành công'
        ], 200);
    }

    public function certificate($certificate_id)
    {
        $certificate = Certificate::find($certificate_id);

        return response()->json([
            'data' => $certificate,
            'message' => 'Lấy chứng chỉ thành công'
        ], 200);
    }
}