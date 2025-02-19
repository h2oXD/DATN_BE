<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Lấy danh sách khóa học đã đăng ký của học viên cùng tiến độ
     */
    public function getUserCoursesWithProgress(Request $request)
    {
        $userId = $request->user()->id; // Lấy user_id từ request

        // Lấy danh sách các khóa học mà user đã đăng ký với 3 trạng thái
        $enrollments = Enrollment::where('user_id', $userId)
            ->whereIn('status', ['active', 'canceled', 'completed'])
            ->with(['course', 'progress'])
            ->get()
            ->unique('course_id'); // Chỉ lấy 1 bản ghi duy nhất cho mỗi khóa học

        return response()->json([
            'status' => 'success',
            'data' => $enrollments->values() // Reset lại key để tránh lỗi index
        ]);
    }
}
