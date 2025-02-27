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
use Symfony\Component\HttpFoundation\Response;

class CertificateController extends Controller
{
    public function createCertificate(Request $request, $user_id, $course_id)
    {
        try {
            if ($request->user()->id != $user_id) {
                return response()->json(['message' => 'Unauthorized.'], Response::HTTP_FORBIDDEN);
            }

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

            // Tạo nội dung chứng chỉ, sử dụng view Blade hoặc thư viện PDF.
            $pdf = Pdf::loadView('certificates.template', compact('user', 'course')); // Bạn cần tạo view 'certificates.template.blade.php'
            // Lưu trữ chứng chỉ vào storage.
            $certificateFileName = 'certificates/' . $user_id . '_' . $course_id . '_' . time() . '.pdf';
            Storage::disk('public')->put($certificateFileName, $pdf->output());
            // Tạo bản ghi chứng chỉ trong database.
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
}