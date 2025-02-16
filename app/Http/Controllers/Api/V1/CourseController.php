<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCourseRequest;
use App\Http\Requests\Api\UpdateCourseRequest;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CourseController extends Controller
{
    public function index()
    {
        try {
            $courses = request()->user()->courses()->with('category')->paginate(8);

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
    public function store(StoreCourseRequest $request)
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
    public function show($course_id)
    {
        try {
            $course = request()->user()->courses()->with([
                'sections' => function ($query) {
                    $query->orderBy('order');
                },
                'sections.lessons' => function ($query) {
                    $query->orderBy('order');
                },
                'sections.lessons.documents',
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
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function update(UpdateCourseRequest $request, $course_id)
    {
        try {
            $course = $request->user()->courses()->find($course_id);

            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($course->status !== 'draft') {
                return response()->json([
                    'message' => 'Không thể cập nhật khoá học này vì không phải bản nháp'
                ], Response::HTTP_FORBIDDEN);
            }

            $data = $request->except('thumbnail', 'video_preview');

            if ($request->hasFile('thumbnail')) {
                $currentThumbnail = $course->thumbnail;
                $file = $request->file('thumbnail');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $data['thumbnail'] = $file->storeAs('images/thumbnails', $fileName, 'public');
            }

            if ($request->hasFile('video_preview')) {
                $currentVideo = $course->video_preview;
                $file = $request->file('video_preview');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $data['video_preview'] = $file->storeAs('videos/courses', $fileName, 'public');
            }

            $course->update($data);

            if (
                isset($currentThumbnail) &&
                $currentThumbnail &&
                !empty($currentThumbnail) &&
                Storage::exists($currentThumbnail)
            ) {
                Storage::delete($currentThumbnail);
            }

            if (
                isset($currentVideo) &&
                $currentVideo &&
                Storage::exists($currentVideo) &&
                !empty($currentVideo)
            ) {
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
    public function destroy($course_id)
    {
        try {
            $course = request()->user()->courses()->find($course_id);

            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($course->status !== 'draft') {
                return response()->json([
                    'message' => 'Không thể xoá khoá học này vì không phải bản nháp'
                ], Response::HTTP_FORBIDDEN);
            }

            $currentThumbnail = $course->thumbnail;
            $currentVideo = $course->video_preview;

            $course->delete();

            if (
                isset($currentThumbnail) &&
                $currentThumbnail &&
                !empty($currentThumbnail) &&
                Storage::exists($currentThumbnail)
            ) {
                Storage::delete($currentThumbnail);
            }

            if (
                isset($currentVideo) &&
                $currentVideo &&
                Storage::exists($currentVideo) &&
                !empty($currentVideo)
            ) {
                Storage::delete($currentVideo);
            }

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function pending($course_id)
    {
        try {
            $course = request()->user()->courses()->find($course_id);

            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($course->status !== 'draft') {
                return response()->json([
                    'message' => 'Không thể gửi yêu cầu phê duyệt khoá học vì không phải bản nháp'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $course->status = 'pending';
            $course->submited_at = now();
            $course->save();

            return response()->json([
                'message' => 'Gửi yêu cầu phê duyệt thành công',
                'data' => $course
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
