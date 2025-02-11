<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCourseRequest;
use App\Http\Requests\Api\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\Video;
use Carbon\Carbon;
use getID3;
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
            $course = $request->user()->courses()->with(['sections' => function ($query) use ($section_id) {
                $query->where('id', $section_id);
            }, 'sections.lessons' => function ($query) use ($lesson_id) {
                $query->where('id', $lesson_id);
            }])->find($course_id);

            if (!$course || !$course->sections->first() || !$lesson = $course->sections->first()->lessons->first()) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404); // Combined check
            }

            $validator = Validator::make($request->all(), [
                'video_url' => 'required|file|mimes:mp4,mov,avi',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $request->except('video_url');

            if ($request->hasFile('video_url')) {
                $file = $request->file('video_url');
                $timestamp = now()->timestamp; // Current timestamp
                $newFileName = $timestamp . '_' . $file->getClientOriginalName(); // Add timestamp to filename
                $data['video_url'] = $file->storeAs('videos', $newFileName);
            }

            // Lấy thời lượng video bằng getID3
            $getID3 = new getID3();
            $fileInfo = $getID3->analyze($file->getRealPath());

            if (!isset($fileInfo['playtime_seconds'])) {
                return response()->json([
                    'message' => 'Không thể lấy thời lượng video',
                ], 400);
            }

            $duration = round($fileInfo['playtime_seconds']);

            if ($duration <= 60) {
                return response()->json([
                    'message' => 'Thời lượng video không đủ',
                    'errors' => [
                        'duration' => ['Thời lượng video phải lớn hơn 1 phút.'],
                    ],
                ], 422);
            }

            $video = $lesson->videos()->create([
                'video_url' => $data['video_url'],
                'duration' => $duration,
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
            $course = $request->user()->courses()->with(['sections' => function ($query) use ($section_id) {
                $query->where('id', $section_id);
            }, 'sections.lessons' => function ($query) use ($lesson_id) {
                $query->where('id', $lesson_id);
            }, 'sections.lessons.videos' => function ($query) use ($video_id) {
                $query->where('id', $video_id);
            }])->find($course_id);

            if (
                !$course ||
                !$course->sections->first() ||
                !$course->sections->first()->lessons->first() ||
                !$video = $course->sections->first()->lessons->first()->videos->first()
            ) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404); // Combined check
            }

            $validator = Validator::make($request->all(), [
                'video_url' => 'nullable|file|mimes:mp4,mov,avi',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $data = $request->except('video_url');

            $currentVideo = $video->video_url;

            if ($request->hasFile('video_url')) {
                if ($request->hasFile('video_url')) {
                    $file = $request->file('video_url');
                    $timestamp = now()->timestamp; // Current timestamp
                    $newFileName = $timestamp . '_' . $file->getClientOriginalName(); // Add timestamp to filename
                    $data['video_url'] = $file->storeAs('videos', $newFileName);
                }
            }


            // Lấy thời lượng video bằng getID3
            $getID3 = new getID3();
            $fileInfo = $getID3->analyze($file->getRealPath());

            if (!isset($fileInfo['playtime_seconds'])) {
                return response()->json([
                    'message' => 'Không thể lấy thời lượng video',
                ], 400);
            }

            $duration = round($fileInfo['playtime_seconds']);

            if ($duration <= 60) {
                return response()->json([
                    'message' => 'Thời lượng video không đủ',
                    'errors' => [
                        'duration' => ['Thời lượng video phải lớn hơn 1 phút.'],
                    ],
                ], 422);
            }

            // Cập nhật video mới vào database
            $video->update([
                'video_url' => $data['video_url'],
                'duration' => $duration,
            ]);
            if ($currentVideo && Storage::exists($currentVideo) && !empty($currentVideo)) {
                Storage::delete($currentVideo);
            }

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
            $course = $request->user()->courses()->with(['sections' => function ($query) use ($section_id) {
                $query->where('id', $section_id);
            }, 'sections.lessons' => function ($query) use ($lesson_id) {
                $query->where('id', $lesson_id);
            }, 'sections.lessons.videos' => function ($query) use ($video_id) {
                $query->where('id', $video_id);
            }])->find($course_id);


            if (!$course) {
                return response()->json(['message' => 'Không tìm thấy khóa học'], 404);
            }

            $section = $course->sections->first();
            if (!$section) {
                return response()->json(['message' => 'Không tìm thấy section'], 404);
            }

            $lesson = $section->lessons->first();
            if (!$lesson) {
                return response()->json(['message' => 'Không tìm thấy lesson'], 404);
            }

            $video = $lesson->videos->first();
            if (!$video) {
                return response()->json(['message' => 'Không tìm thấy video bài học'], 404);
            }

            $currentVideo = $video->video_url;

            $video->delete();

            if ($currentVideo && Storage::exists($currentVideo) && !empty($currentVideo)) {
                Storage::delete($currentVideo);
            }

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
