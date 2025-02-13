<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use getID3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    //

    public function index(Request $request, $course_id, $section_id, $lesson_id)
    {
        try {

            $course = $request->user()->courses()
                ->with(['sections' => function ($query) use ($section_id) {
                    $query->where('id', $section_id);
                }, 'sections.lessons' => function ($query) use ($lesson_id) {
                    $query->where('id', $lesson_id);
                }])
                ->find($course_id);


            if (!$course || !$course->sections->first() || !$lesson = $course->sections->first()->lessons->first()) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404);
            }


            $videos = $lesson->videos;


            return response()->json([
                'data' => $videos,
            ]);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Đã xảy ra lỗi trong quá trình xử lý'], 500);
        }
    }
    public function store(Request $request, $course_id, $section_id, $lesson_id)
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
                $newFileName = time() . '_' . $file->getClientOriginalName(); // Add timestamp to filename
                $data['video_url'] = $file->storeAs('videos', $newFileName);
            }

            // Lấy thời lượng video bằng getID3
            $getID3 = new getID3();
            $fileInfo = $getID3->analyze($file->getRealPath());

            if (!isset($fileInfo['playtime_seconds'])) {
                Storage::delete($data['video_url']);
                return response()->json([
                    'message' => 'Không thể lấy thời lượng video',
                ], 400);
            }

            $duration = round($fileInfo['playtime_seconds']);

            if ($duration <= 60) {
                Storage::delete($data['video_url']);
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
                'message' => 'Lỗi hệ thống',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    public function show(Request $request, $course_id, $section_id, $lesson_id, $video_id)
    {
        try {
            // Lấy khóa học thuộc về người dùng hiện tại và kiểm tra section và lesson
            $course = $request->user()->courses()
                ->with(['sections' => function ($query) use ($section_id) {
                    $query->where('id', $section_id);
                }, 'sections.lessons' => function ($query) use ($lesson_id) {
                    $query->where('id', $lesson_id);
                }])
                ->find($course_id);

            // Kiểm tra xem course, section, và lesson có tồn tại không
            if (!$course || !$course->sections->first() || !$lesson = $course->sections->first()->lessons->first()) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404);
            }

            // Kiểm tra xem video có tồn tại trong lesson không
            $video = $lesson->videos->where('id', $video_id)->first();
            if (!$video) {
                return response()->json(['message' => 'Video không tồn tại'], 404);
            }

            // Trả về response dạng JSON
            return response()->json([
                'data' => $video,
            ]);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json(['message' => 'Đã xảy ra lỗi trong quá trình xử lý'], 500);
        }
    }


    /**
     * Cập nhật video
     */
    public function update(Request $request, $course_id, $section_id, $lesson_id, $video_id)
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
                'video_url' => 'required|file|mimes:mp4,mov,avi',
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

                    $newFileName = time() . '_' . $file->getClientOriginalName(); // Add timestamp to filename
                    $data['video_url'] = $file->storeAs('videos', $newFileName);
                }
            }


            // Lấy thời lượng video bằng getID3
            $getID3 = new getID3();
            $fileInfo = $getID3->analyze($file->getRealPath());

            if (!isset($fileInfo['playtime_seconds'])) {
                Storage::delete($data['video_url']);
                return response()->json([
                    'message' => 'Không thể lấy thời lượng video',
                ], 400);
            }

            $duration = round($fileInfo['playtime_seconds']);

            if ($duration <= 60) {
                Storage::delete($data['video_url']);
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
                'message' => 'Lỗi hệ thống',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    /**
     * Xóa video
     */
    public function destroy(Request $request, $course_id, $section_id, $lesson_id, $video_id)
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

            $currentVideo = $video->video_url;

            $video->delete();

            if ($currentVideo && Storage::exists($currentVideo) && !empty($currentVideo)) {
                Storage::delete($currentVideo);
            }

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
