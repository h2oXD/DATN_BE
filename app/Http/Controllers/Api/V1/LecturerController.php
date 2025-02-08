<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCourseRequest;
use App\Http\Requests\Api\UpdateCourseRequest;
use App\Models\Coding;
use App\Models\Course;
use App\Models\Document;
use App\Models\Lecturer;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Section;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LecturerController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'data' => request()->user()
        ]);
    }
    public function getLecturerInfo()
    {
        $lecturerInfo = Lecturer::find(request()->user()->lecturer_id);
        return response()->json([
            'message' => 'Lấy dữ liệu thành công',
            'lecturer' => $lecturerInfo
        ], 201);
    }
    public function getLecturerCourse()
    {
        try {
            // $courses = Course::with('category')
            // ->select(['title','category_id','status','thumbnail','level'])
            // ->where('lecturer_id', request()->user()->lecturer_id)
            // ->get();
            $courses = Course::where('lecturer_id', request()->user()->lecturer_id)
                ->paginate(4);
            return response()->json([
                'message' => 'Lấy dữ liệu thành công',
                'courses' => $courses
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi serve',
            ], 500);
        }

    }
    public function createLecturerCourse(StoreCourseRequest $request)
    {
        try {
            $course = Course::create([
                'title' => $request->title,
                'lecturer_id' => $request->user()->lecturer->id,
                'category_id' => $request->category_id ?? null,
                'status' => 'draft',
                'admin_commission_rate' => 30,
                'created_at' => Carbon::now(),
            ]);
            return response()->json([
                'course_id' => $course->id,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
            ], 500);
        }
    }
    public function showLecturerCourse($course_id)
    {
        $lecturer_id = request()->user()->lecturer_id;
        $course = Course::with(['sections','lessons','documents','videos','codings'])->where([['lecturer_id', $lecturer_id], ['id', $course_id]])->first();

        if (!$course) {
            return response()->json([
                'message' => 'Không tìm thấy khoá học',
            ], 404);
        }
        return response()->json([
            'course' => $course,
        ], 201);
    }
    public function updateLecturerCourse(UpdateCourseRequest $request, $course_id)
    {

    }
}
