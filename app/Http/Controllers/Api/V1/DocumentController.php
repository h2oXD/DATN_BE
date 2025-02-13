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
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{

    public function store(Request $request, $course_id, $section_id, $lesson_id)
    {
        try {
            $course = $request->user()->courses()->with([
                'sections' => function ($query) use ($section_id) {
                    $query->where('id', $section_id);
                },
                'sections.lessons' => function ($query) use ($lesson_id) {
                    $query->where('id', $lesson_id);
                }
            ])->find($course_id);

            if (!$course || !$course->sections->first() || !$lesson = $course->sections->first()->lessons->first()) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404); // Combined check
            }

            $validator = Validator::make($request->all(), [
                'document_url' => 'required|file|mimes:pdf,doc,docx|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $data = $request->all();

            if ($request->hasFile('document_url')) {
                $data['document_url'] = Storage::putFile('documents', $request->file('document_url'));
                $data['file_type'] = $request->file('document_url')->getClientOriginalExtension();
            }

            $document = $lesson->documents()->create($data);

            return response()->json([
                'message' => 'Document tải lên thành công',
                'document' => $document
            ], Response::HTTP_CREATED);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function update(Request $request, $course_id, $section_id, $lesson_id, $document_id)
    {
        try {
            $course = $request->user()->courses()->with([
                'sections' => function ($query) use ($section_id) {
                    $query->where('id', $section_id);
                },
                'sections.lessons' => function ($query) use ($lesson_id) {
                    $query->where('id', $lesson_id);
                },
                'sections.lessons.documents' => function ($query) use ($document_id) {
                    $query->where('id', $document_id);
                }
            ])->find($course_id);

            if (
                !$course ||
                !$course->sections->first() ||
                !$course->sections->first()->lessons->first() ||
                !$document = $course->sections->first()->lessons->first()->documents->first()
            ) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404); // Combined check
            }

            $validator = Validator::make($request->all(), [
                'document_url' => 'sometimes|file|mimes:pdf,doc,docx|max:2048|required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $data = $request->all();

            if ($request->hasFile('document_url')) {
                $currentFileDocument = $document->document_url;
                $data['document_url'] = Storage::putFile('documents', $request->file('document_url'));
                $data['file_type'] = $request->file('document_url')->getClientOriginalExtension();
            }

            $document->update($data);

            if (
                isset($currentFileDocument) &&
                $currentFileDocument &&
                !empty($currentFileDocument) &&
                Storage::exists($currentFileDocument)
            ) {
                Storage::delete($currentFileDocument);
            }

            return response()->json([
                'message' => 'Document đã được cập nhật',
                'document' => $document
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function destroy(Request $request, $course_id, $section_id, $lesson_id, $document_id)
    {
        try {
            $course = $request->user()->courses()->with([
                'sections' => function ($query) use ($section_id) {
                    $query->where('id', $section_id);
                },
                'sections.lessons' => function ($query) use ($lesson_id) {
                    $query->where('id', $lesson_id);
                },
                'sections.lessons.documents' => function ($query) use ($document_id) {
                    $query->where('id', $document_id);
                }
            ])->find($course_id);

            if (
                !$course ||
                !$course->sections->first() ||
                !$course->sections->first()->lessons->first() ||
                !$document = $course->sections->first()->lessons->first()->documents->first()
            ) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404); // Combined check
            }

            $currentFileDocument = $document->document_url;

            $document->delete();

            if (
                isset($currentFileDocument) &&
                $currentFileDocument &&
                !empty($currentFileDocument) &&
                Storage::exists($currentFileDocument)
            ) {
                Storage::delete($currentFileDocument);
            }

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
