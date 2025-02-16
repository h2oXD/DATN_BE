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
    /**
     * @OA\Get(
     *     path="/api/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/documents",
     *     tags={"Document"},
     *     summary="Lấy tất cả document của một bài học",
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         description="ID của khóa học",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         description="ID của phần",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="lesson_id",
     *         in="path",
     *         description="ID của bài học",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thành công",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Danh sách Document"),
     *             @OA\Property(
     *                 property="document",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="lesson_id", type="integer", example=1),
     *                     @OA\Property(
     *                         property="document_url",
     *                         type="string",
     *                         example="documents/LoC65Sly8GUzGZ4sPzo2eVK205TJnarVfIMmjnXB.pdf"
     *                     ),
     *                     @OA\Property(property="file_type", type="string", example="pdf"),
     *                     @OA\Property(property="created_at", type="string", example="2025-02-16T17:26:06.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", example="2025-02-16T17:26:06.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized - Chưa đăng nhập hoặc token không hợp lệ",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy tài nguyên"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi server"
     *     )
     * )
     */
    public function index(Request $request, $course_id, $section_id, $lesson_id)
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

            $document = $lesson->documents;

            return response()->json([
                'message' => 'Danh sách Document',
                'document' => $document
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/documents",
     *     tags={"Document"},
     *     summary="Tải lên một document mới cho bài học",
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         description="ID của khóa học",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         description="ID của phần",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="lesson_id",
     *         in="path",
     *         description="ID của bài học",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="document_url",
     *                     type="file",
     *                     format="binary",
     *                     description="File tài liệu (pdf, doc, docx)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Document tải lên thành công",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Document tải lên thành công"),
     *             @OA\Property(
     *                 property="document",
     *                 type="object",
     *                 @OA\Property(property="document_url", type="string", example="documents/Vqt2K5A4Ju3AtyEscPR1m1y075qjtVjNRMRY2tVL.docx"),
     *                 @OA\Property(property="file_type", type="string", example="docx"),
     *                 @OA\Property(property="lesson_id", type="integer", example=1),
     *                 @OA\Property(property="updated_at", type="string", example="2025-02-16T17:54:39.000000Z"),
     *                 @OA\Property(property="created_at", type="string", example="2025-02-16T17:54:39.000000Z"),
     *                 @OA\Property(property="id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Lỗi validation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="document_url",
     *                     type="array",
     *                     @OA\Items(type="string", example="The document url field is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized - Chưa đăng nhập hoặc token không hợp lệ",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy tài nguyên"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi máy chủ"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/documents/{document_id}",
     *     tags={"Document"},
     *     summary="Lấy chi tiết một tài liệu cụ thể",
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         description="ID của khóa học",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         description="ID của phần",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="lesson_id",
     *         in="path",
     *         description="ID của bài học",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="document_id",
     *         in="path",
     *         description="ID của tài liệu",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thành công",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="chi tiết Document"),
     *             @OA\Property(
     *                 property="document",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="lesson_id", type="integer", example=1),
     *                 @OA\Property(
     *                     property="document_url",
     *                     type="string",
     *                     example="documents/LoC65Sly8GUzGZ4sPzo2eVK2o5TJnarVfIMmjnXB.pdf"
     *                 ),
     *                 @OA\Property(property="file_type", type="string", example="pdf"),
     *                 @OA\Property(property="created_at", type="string", example="2025-02-16T17:26:06.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2025-02-16T17:26:06.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized - Chưa đăng nhập hoặc token không hợp lệ",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy tài nguyên"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi máy chủ"
     *     )
     * )
     */
    public function show(Request $request, $course_id, $section_id, $lesson_id, $document_id)
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

            $document = Document::find($document_id);

            return response()->json([
                'message' => 'chi tiết Document',
                'document' => $document
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/documents/{document_id}",
     *     tags={"Document"},
     *     summary="Cập nhật một tài liệu cụ thể",
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         description="ID của khóa học",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         description="ID của phần",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="lesson_id",
     *         in="path",
     *         description="ID của bài học",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="document_id",
     *         in="path",
     *         description="ID của tài liệu",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="document_url",
     *                     type="file",
     *                     format="binary",
     *                     description="File tài liệu (pdf, doc, docx)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tài liệu đã được cập nhật",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Document đã được cập nhật"),
     *             @OA\Property(
     *                 property="document",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="lesson_id", type="integer", example=1),
     *                 @OA\Property(
     *                     property="document_url",
     *                     type="string",
     *                     example="documents/jVfmRkGGQDO2MAfjZVWCTgKMTPF1R9ClI55FZinc.docx"
     *                 ),
     *                 @OA\Property(property="file_type", type="string", example="docx"),
     *                 @OA\Property(property="created_at", type="string", example="2025-02-16T17:26:06.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2025-02-16T18:09:45.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Lỗi validation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="document_url",
     *                     type="array",
     *                     @OA\Items(type="string", example="The document url field is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized - Chưa đăng nhập hoặc token không hợp lệ",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy tài nguyên"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi máy chủ"
     *     )
     * )
     */
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
                'document_url' => 'file|mimes:pdf,doc,docx|max:2048|required',
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

    /**
     * @OA\Delete(
     *     path="/api/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/documents/{document_id}",
     *     tags={"Document"},
     *     summary="Xóa một tài liệu cụ thể",
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         description="ID của khóa học",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         description="ID của phần",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="lesson_id",
     *         in="path",
     *         description="ID của bài học",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="document_id",
     *         in="path",
     *         description="ID của tài liệu",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Tài liệu đã được xóa thành công"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized - Chưa đăng nhập hoặc token không hợp lệ",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy tài nguyên"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi máy chủ"
     *     )
     * )
     */
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
