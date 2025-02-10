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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
                'message' => 'Lỗi hệ thống',
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
        $course = Course::with([
            'sections' => function ($query) {
                $query->orderBy('order');
            },
            'sections.lessons' => function ($query) {
                $query->orderBy('order');
            },
            'sections.lessons.documents' => function ($query) {
                $query->orderBy('order');
            },
            'sections.lessons.videos',
            'sections.lessons.codings',
            'sections.lessons.quizzes'
        ])->where([['user_id', $user_id], ['id', $course_id]])->first();

        if (!$course) {
            return response()->json([
                'message' => 'Không tìm thấy khoá học',
            ], 404);
        }
        return response()->json([
            'course' => $course,
        ], 200);
    }
    public function updateLecturerCourse(UpdateCourseRequest $request, $course_id)
    {
        try {
            $user_id = $request->user()->id;
            $course = Course::where('user_id', $user_id)->find($course_id);
            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học'
                ], 404);
            }
            $data = $request->all();

            // Xử lý upload ảnh
            if ($request->hasFile('thumbnail')) {
                $oldThumbnail = $course->thumbnail; // Lưu đường dẫn ảnh cũ
                $file = $request->file('thumbnail');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $data['thumbnail'] = $file->storeAs('images/thumbnails', $fileName, 'public');
            }
            // Xử lý upload video_preview
            if ($request->hasFile('video_preview')) {
                $oldVideo = $course->video_preview; // Lưu đường dẫn video cũ
                $file = $request->file('video_preview'); // Thay đổi tên trường
                $fileName = time() . '_' . $file->getClientOriginalName();
                $data['video_preview'] = $file->storeAs('videos/courses', $fileName, 'public'); // Thay đổi tên trường
            }

            $course->update($data);

            // Xóa ảnh cũ sau khi update thành công
            if (isset($oldThumbnail)) {
                Storage::delete($oldThumbnail);
            }
            // Xóa video cũ sau khi update thành công
            if (isset($oldVideo)) {
                Storage::delete($oldVideo);
            }

            return response()->json([
                'message' => 'Cập nhật thành công'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống'
            ], 500);
        }
    }

    public function createSection(Request $request, $course_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'nullable|string',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            // Tìm Course, nếu không có sẽ trả về lỗi 404
            $course = Course::findOrFail($course_id);

            // Lấy order lớn nhất hiện tại và tăng thêm 1
            $maxOrder = $course->sections()->max('order') ?? 0;
            $newOrder = $maxOrder + 1;

            // Tạo mới Section
            $section = $course->sections()->create([
                'title' => $request->title,
                'description' => $request->description,
                'order' => $newOrder,
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
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'nullable|string',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            // Tìm Course, nếu không có thì trả về lỗi 404
            $course = Course::findOrFail($course_id);

            // Tìm Section trong Course, nếu không có thì trả về lỗi 404
            $section = $course->sections()->findOrFail($section_id);

            // Lấy order lớn nhất hiện tại trong section và tăng thêm 1
            $maxOrder = $section->lessons()->max('order') ?? 0;
            $newOrder = $maxOrder + 1;

            // Tạo mới Lesson
            $lesson = $section->lessons()->create([
                'title' => $request->title,
                'description' => $request->description,
                'order' => $newOrder, // Tự động tăng order
                'course_id' => $course_id,
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
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'nullable|string',
            ]);
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
                'order' => $request->order,
            ]);
            return response()->json([
                'lesson' => $lesson,
                'message' => 'Cập nhật lesson thành công',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th,
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
