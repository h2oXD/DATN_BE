<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCourseRequest;
use App\Http\Requests\Api\UpdateCourseRequest;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CourseController extends Controller
{
    public function getLecturerCourse()
    {
        try {
            $courses = request()->user()->course()->paginate(5);
            if (!$courses) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học',
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'Lấy dữ liệu thành công',
                'courses' => $courses
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function createLecturerCourse(StoreCourseRequest $request)
    {
        try {
            $course = $request->user()->courses()->create([
                'title' => $request->title,
                'category_id' => $request->category_id,
                'status' => 'draft',
                'admin_commission_rate' => 30,
            ]);

            return response()->json([
                'course_id' => $course->id,
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function showLecturerCourse($course_id)
    {
        $course = request()->user()->courses()->with([
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
        ])->find($course_id);

        if (!$course) {
            return response()->json([
                'message' => 'Không tìm thấy khoá học',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'course' => $course,
        ], Response::HTTP_OK);
    }
    public function updateLecturerCourse(UpdateCourseRequest $request, $course_id)
    {
        try {

            $course = $request->user()->courses()->find($course_id);
            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học'
                ], Response::HTTP_NOT_FOUND);
            }
            $data = $request->except('thumbnail', 'video_preview');


            // Xử lý upload ảnh
            if ($request->hasFile('thumbnail')) {
                $currentThumbnail = $course->thumbnail; // Lưu đường dẫn ảnh cũ
                $file = $request->file('thumbnail');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $data['thumbnail'] = $file->storeAs('images/thumbnails', $fileName, 'public');
            }
            // Xử lý upload video_preview
            if ($request->hasFile('video_preview')) {
                $currentVideo = $course->video_preview; // Lưu đường dẫn video cũ
                $file = $request->file('video_preview'); // Thay đổi tên trường
                $fileName = time() . '_' . $file->getClientOriginalName();
                $data['video_preview'] = $file->storeAs('videos/courses', $fileName, 'public'); // Thay đổi tên trường
            }

            $course->update($data);

            // Xóa ảnh cũ sau khi update thành công
            if (isset($currentThumbnail) && $currentThumbnail && !empty($currentThumbnail) && Storage::exists($currentThumbnail)) {
                Storage::delete($currentThumbnail);
            }
            // Xóa video cũ sau khi update thành công
            if (isset($currentVideo) && $currentVideo && Storage::exists($currentVideo) && !empty($currentVideo)) {
                Storage::delete($currentVideo);
            }
            return response()->json([
                'message' => 'Cập nhật thành công',
                'data' => $course
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
                'error' => $th
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function destroyLecturerCourse($course_id)
    {
        $course = request()->user()->courses()->find($course_id);
        if (!$course) {
            return response()->json([
                'message' => 'Không tìm thấy khoá học'
            ], Response::HTTP_NOT_FOUND);
        }

        $currentThumbnail = $course->thumbnail;
        $currentVideo = $course->video_preview;

        $course->delete();

        if (isset($currentThumbnail) && $currentThumbnail && !empty($currentThumbnail) && Storage::exists($currentThumbnail)) {
            Storage::delete($currentThumbnail);
        }
        if (isset($currentVideo) && $currentVideo && Storage::exists($currentVideo) && !empty($currentVideo)) {
            Storage::delete($currentVideo);
        }
        return response()->noContent();
    }
}
