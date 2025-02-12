<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCourseRequest;
use App\Http\Requests\Api\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Document;
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

    // Document
    public function createDocument(Request $request, $course_id, $section_id, $lesson_id)
    {
        try {
            // Validate
            $validator = Validator::make($request->all(), [
                'document' => 'required|file|mimes:pdf,doc,docx|max:2048',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            // Kiểm tra Course, Section và Lesson có tồn tại không
            $course = Course::where('user_id', $request->user()->id)->find($course_id);
            if (!$course) {
                return response()->json([
                    'message' => 'Khóa học không tồn tại'
                ], 404);
            }

            $section = Section::where('id', $section_id)->where('course_id', $course_id)->first();
            if (!$section) {
                return response()->json([
                    'message' => 'Section không tồn tại'
                ], 404);
            }

            $lesson = Lesson::where('id', $lesson_id)->where('section_id', $section_id)->first();
            if (!$lesson) {
                return response()->json([
                    'message' => 'Lesson không tồn tại'
                ], 404);
            }

            // Upload file document
            $file = $request->file('document');
            $fileType = $file->getClientOriginalExtension();
            if ($request->hasFile('document')) {
                $filePath = Storage::put('documents', $request->file('document'));
            }

            // Thêm mới document vào database
            $document = $lesson->documents()->create([
                'document_url' => $filePath,
                'file_type' => $fileType,
            ]);

            return response()->json([
                'message' => 'Document tải lên thành công',
                'document' => $document
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function updateDocument(Request $request, $course_id, $section_id, $lesson_id, $document_id)
    {
        try {
            // Validate
            // sometimes không bắt buộc người dùng cập nhật file mới khi mà họ không muốn
            $validator = Validator::make($request->all(), [
                'document' => 'sometimes|file|mimes:pdf,doc,docx|max:2048',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            // Kiểm tra Course, Section, Lesson, Document có tồn tại không
            $course = Course::where('user_id', $request->user()->id)->find($course_id);
            if (!$course) {
                return response()->json([
                    'message' => 'Khóa học không tồn tại'
                ], 404);
            }
            $section = Section::where('id', $section_id)->where('course_id', $course_id)->first();
            if (!$section) {
                return response()->json([
                    'message' => 'Section không tồn tại'
                ], 404);
            }
            $document = Document::where('id', $document_id)->where('lesson_id', $lesson_id)->first();
            if (!$document) {
                return response()->json([
                    'message' => 'Document không tồn tại'
                ], 404);
            }

            // Nếu có file mới được upload thì sẽ xóa file cũ
            if ($request->hasFile('document')) {
                // Lấy đường dẫn file từ storage và xóa ảnh cũ
                $oldFilePath = $document->document_url;
                if (Storage::exists($oldFilePath)) {
                    Storage::delete($oldFilePath);
                }

                // Upload file mới
                $file = $request->file('document');
                $fileType = $file->getClientOriginalExtension();
                if ($request->hasFile('document')) {
                    $filePath = Storage::put('documents', $request->file('document'));
                }

                $document->document_url = $filePath;
                $document->file_type = $fileType;
            }

            $document->save();

            return response()->json([
                'message' => 'Document đã được cập nhật',
                'document' => $document
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function destroyDocument(Request $request, $course_id, $section_id, $lesson_id, $document_id)
    {
        try {
            // Kiểm tra Document có tồn tại không
            $course = Course::where('user_id', $request->user()->id)->find($course_id);
            if (!$course) {
                return response()->json([
                    'message' => 'Khóa học không tồn tại'
                ], 404);
            }
            $section = Section::where('id', $section_id)->where('course_id', $course_id)->first();
            if (!$section) {
                return response()->json([
                    'message' => 'Section không tồn tại'
                ], 404);
            }
            $document = Document::where('id', $document_id)->where('lesson_id', $lesson_id)->first();
            if (!$document) {
                return response()->json([
                    'message' => 'Document không tồn tại'
                ], 404);
            }

            // Lấy đường dẫn file từ storage và xóa file
            $oldFilePath = $document->document_url;
            if (Storage::exists($oldFilePath)) {
                Storage::delete($oldFilePath);
            }

            // Xóa document trong database
            $document->delete();

            return response()->json([
                'message' => 'Document đã được xóa'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
