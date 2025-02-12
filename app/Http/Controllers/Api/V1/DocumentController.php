<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Document;
use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{

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
            $lesson = Lesson::where('id', $lesson_id)->where('section_id', $section_id)->first();
            if (!$lesson) {
                return response()->json([
                    'message' => 'Lesson không tồn tại'
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
            $lesson = Lesson::where('id', $lesson_id)->where('section_id', $section_id)->first();
            if (!$lesson) {
                return response()->json([
                    'message' => 'Lesson không tồn tại'
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
