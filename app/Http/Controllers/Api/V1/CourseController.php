<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCourseRequest;
use App\Http\Requests\Api\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Lesson;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function getLecturerCourse()
    {
        try {
            $courses = Course::where('user_id', request()->user()->id)
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
                'user_id' => $request->user()->id,
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
        $user_id = request()->user()->id;
        $course = Course::with(['sections', 'lessons', 'documents', 'videos', 'codings'])->where([['user_id', $user_id], ['id', $course_id]])->first();

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
        $user_id = $request->user()->id;
        $course = Course::where('user_id', $user_id)->find($course_id);
        if (!$course) {
            return response()->json([
                'message' => 'Không tìm thấy khoá học'
            ], 404);
        }
        $course->update($request->all());
        return response()->json([
            'message' => 'Cập nhật thành công'
        ], 200);
    }

    public function createSection(Request $request, $course_id)
    {
        try {
            $course = Course::findOrFail($course_id);
            $section = $course->sections()->create([
                'title' => $request->title,
                'description' => $request->description,
                'order' => $request->order,
            ]);
            return response()->json([
                'section' => $section,
                'message' => 'Tạo mới section thành công',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function updateSection(Request $request, $course_id, $section_id)
    {
        try {
            $user_id = $request->user()->id;
            $course = Course::where('user_id', $user_id)->find($course_id);
            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học'
                ], 404);
            }
            $section = Section::where('course_id', $course_id)->find($section_id);
            if (!$section) {
                return response()->json([
                    'message' => 'Không tìm thấy section'
                ], 404);
            }
            $section->update($request->all());
            return response()->json([
                'section' => $section,
                'message' => 'Cập nhật section thành công'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th
            ], 500);
        }
    }
    public function destroySection($course_id, $section_id)
    {
        try {
            $section = Section::where('course_id', $course_id)->findOrFail($section_id);
            $section->delete();
            return response()->json([
                'message' => 'Xoá section thành công',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    public function createLesson(Request $request, $course_id, $section_id)
    {
        try {
            $course = Course::findOrFail($course_id);
            $section = $course->sections()->findOrFail($section_id);
            
            $lesson = $section->lessons()->create([
                'title' => $request->title,
                'description' => $request->content,
                'order' => $request->order,
            ]);
            
            return response()->json([
                'lesson' => $lesson,
                'message' => 'Tạo mới lesson thành công',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function updateLesson(Request $request, $course_id, $section_id, $lesson_id)
    {
        try {
            $user_id = $request->user()->id;
            $course = Course::where('user_id', $user_id)->find($course_id);

            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khóa học'
                ], 404);
            }

            $section = Section::where('course_id', $course_id)->find($section_id);

            if (!$section) {
                return response()->json([
                    'message' => 'Không tìm thấy section'
                ], 404);
            }

            $lesson = Lesson::where('section_id', $section_id)->find($lesson_id);

            if (!$lesson) {
                return response()->json([
                    'message' => 'Không tìm thấy lesson'
                ], 404);
            }

            $lesson->update([
                'title' => $request->title,
                'description' => $request->description,
                'content' => $request->content,
                'order' => $request->order,
            ]);

            return response()->json([
                'message' => 'Cập nhật lesson thành công',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
            ], 500);
        }
    }

    public function destroyLesson(Request $request, $course_id, $section_id, $lesson_id)
    {
        try {
            $user_id = $request->user()->id;
            $course = Course::where('user_id', $user_id)->find($course_id);

            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khóa học'
                ], 404);
            }

            $section = Section::where('course_id', $course_id)->find($section_id);

            if (!$section) {
                return response()->json([
                    'message' => 'Không tìm thấy section'
                ], 404);
            }

            $lesson = Lesson::where('section_id', $section_id)->find($lesson_id);

            if (!$lesson) {
                return response()->json([
                    'message' => 'Không tìm thấy lesson'
                ], 404);
            }

            $lesson->delete();

            return response()->json([
                'message' => 'Xóa lesson thành công',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
            ], 500);
        }
    }
}
