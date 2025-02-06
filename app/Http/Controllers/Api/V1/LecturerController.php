<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lecturer;
use Illuminate\Http\Request;

class LecturerController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'data' => request()->user()
        ]);
    }
    public function getLecturerInfo($lecturer_id)
    {
        $id = request()->user()->lecturer_id;
        if (!(request()->user()->lecturer_id == $lecturer_id)) {
            return response()->json([
                'message' => 'Không tìm thấy trang'
            ], 404);
        }
        $lecturerInfo = Lecturer::find(request()->user()->lecturer_id);
        return response()->json([
            'data' => $lecturerInfo
        ]);
    }
    public function getCourse($lecturer_id)
    {
        $id = request()->user()->lecturer_id;
        if (!(request()->user()->lecturer_id == $lecturer_id)) {
            return response()->json([
                'message' => 'Không tìm thấy trang'
            ], 404);
        }
        try {
            $courses = Course::where('lecturer_id', $id)->get();
            return response()->json([
                'message' => 'Lấy dữ liệu thành công',
                'data' => $courses
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi serve'
            ]);
        }

    }
}
