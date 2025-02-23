<?php

namespace App\Http\Controllers\Api;

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
            // Bước 1: Kiểm tra đăng nhập và quyền truy cập
            if ($request->user()->id != $user_id) {
                return response()->json(['message' => 'Unauthorized.'], Response::HTTP_FORBIDDEN);
            }

            // Bước 2: Kiểm tra xem người dùng đã đăng ký khóa học hay chưa
            $enrollment = Enrollment::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->first();

            if (!$enrollment) {
                return response()->json(['message' => 'Bạn chưa đăng ký khóa học này.'], Response::HTTP_NOT_FOUND);
            }

            // Bước 3: Kiểm tra xem người dùng đã hoàn thành khóa học chưa
            $progress = Progress::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->where('status', 'completed')
                ->first();

            if (!$progress) {
                return response()->json(['message' => 'Bạn chưa hoàn thành khóa học này.'], Response::HTTP_BAD_REQUEST);
            }

            // Bước 4: Cấp chứng chỉ
            // Kiểm tra xem chứng chỉ đã được cấp chưa.
            if (Certificate::where('user_id', $user_id)->where('course_id', $course_id)->exists()) {
                return response()->json(['message' => 'Chứng chỉ đã được cấp.'], Response::HTTP_BAD_REQUEST);
            }

            // Tạo chứng chỉ.
            $user = User::find($user_id);
            $course = Course::find($course_id);

            // Tạo nội dung chứng chỉ, sử dụng view Blade hoặc thư viện PDF.
            $pdf = Pdf::loadView('certificates.template', compact('user', 'course')); // Bạn cần tạo view 'certificates.template.blade.php'

            // Lưu trữ chứng chỉ vào storage.
            $certificateFileName = 'certificates/' . $user_id . '_' . $course_id . '_' . time() . '.pdf';
            Storage::disk('public')->put($certificateFileName, $pdf->output());

            // Tạo bản ghi chứng chỉ trong database.
            $certificate = new Certificate();
            $certificate->user_id = $user_id;
            $certificate->course_id = $course_id;
            $certificate->certificate_url = Storage::url($certificateFileName);
            $certificate->issued_at = now();
            $certificate->save();

            return response()->json(['message' => 'Chứng chỉ đã được cấp.', 'certificate_url' => $certificate->certificate_url], Response::HTTP_CREATED);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi hệ thống: ' . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}