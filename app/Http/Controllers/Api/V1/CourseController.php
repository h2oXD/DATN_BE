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



    //VIDEO

    public function createVideo(Request $request, $course_id, $section_id, $lesson_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'video_url' => 'required|file|mimetypes:video/mp4,video/x-msvideo,video/x-matroska',
                'duration' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $file = $request->file('video_url');
            $filePath = $file->store('videos');
            $fileUrl = Storage::url($filePath);

            $lesson = Lesson::findOrFail($lesson_id);
            $video = $lesson->videos()->create([
                'video_url' => $fileUrl,
                'duration' => $request->duration,
            ]);

            return response()->json([
                'video' => $video,
                'message' => 'Tạo mới video thành công',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Cập nhật video
     */
    public function updateVideo(Request $request, $course_id, $section_id, $lesson_id, $video_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'video_url' => 'required|url',
                'duration' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }

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
            $video = $lesson->videos()->where('id', $video_id)->firstOrFail();
            $video->update([
                'video_url' => $request->video_url,
                'duration' => $request->duration,
            ]);

            return response()->json([
                'video' => $video,
                'message' => 'Cập nhật video thành công',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Xóa video
     */
    public function destroyVideo(Request $request, $course_id, $section_id, $lesson_id, $video_id)
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
            $video = $lesson->videos()->where('id', $video_id)->firstOrFail();
            $video->delete();

            return response()->json([
                'message' => 'Xóa video thành công',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
