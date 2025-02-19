<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnrollmentController extends Controller
{
    /**
     * Lấy danh sách khóa học đã đăng ký của học viên cùng tiến độ
     */
    public function getUserCoursesWithProgress()
    {
        $userId = Auth::id(); // Lấy ID user đang đăng nhập

        $enrollments = Enrollment::where('user_id', $userId)
            ->with(['course', 'progress'])
            ->get();

        if ($enrollments->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'User has no enrolled courses',
                'data' => []
            ]);
        }
    }
}
