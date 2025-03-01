<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Video;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/video/{video_id}/notes",
     *     summary="Lấy danh sách ghi chú của video",
     *     description="API này lấy danh sách ghi chú của một video do người dùng hiện tại tạo.",
     *     tags={"Notes"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="videoId",
     *         in="path",
     *         required=true,
     *         description="ID của video",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách ghi chú",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="video_id", type="integer", example=10),
     *                 @OA\Property(property="content", type="string", example="Ghi chú quan trọng"),
     *                 @OA\Property(property="timestamp", type="string", format="date-time", example="00:02:15")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Video không tồn tại",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Video không tồn tại")
     *         )
     *     )
     * )
     */
    public function index($videoId)
    {
        // Kiểm tra video_id
        $video = Video::find($videoId);
        if (!$video) {
            return response()->json(['message' => 'Video không tồn tại'], 404);
        }

        // Lấy danh sách ghi chú của người dùng hiện tại
        $notes = Note::where('user_id', auth()->id())
            ->where('video_id', $videoId)
            ->orderBy('timestamp')
            ->get();

        // Kiểm tra nếu không có ghi chú
        if ($notes->isEmpty()) {
            return response()->json(['message' => 'Hiện tại chưa có ghi chú nào cho video này'], 200);
        }

        return response()->json($notes);
    }

    /**
     * @OA\Post(
     *     path="/video/{video_id}/notes",
     *     summary="Tạo ghi chú cho video",
     *     description="API này tạo một ghi chú mới cho video do người dùng hiện tại tạo.",
     *     tags={"Notes"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="videoId",
     *         in="path",
     *         required=true,
     *         description="ID của video",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content", "timestamp"},
     *             @OA\Property(property="content", type="string", maxLength=1000, example="Ghi chú quan trọng"),
     *             @OA\Property(property="timestamp", type="string", example="01:23", description="Định dạng MM:SS hoặc số giây")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ghi chú đã được tạo thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ghi chú đã được lưu"),
     *             @OA\Property(property="note", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="video_id", type="integer", example=10),
     *                 @OA\Property(property="content", type="string", example="Ghi chú quan trọng"),
     *                 @OA\Property(property="timestamp", type="string", example="01:23")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Video không tồn tại",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Video không tồn tại")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dữ liệu đầu vào không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="content", type="array",
     *                     @OA\Items(type="string", example="Trường content là bắt buộc.")
     *                 ),
     *                 @OA\Property(property="timestamp", type="array",
     *                     @OA\Items(type="string", example="Trường timestamp không đúng định dạng.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request, $videoId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'timestamp' => 'required|regex:/^\d+(:[0-5]\d)?$/', // Chấp nhận số (123) hoặc MM:SS (1:23)
        ]);

        $video = Video::find($videoId);
        if (!$video) {
            return response()->json(['message' => 'Video không tồn tại'], 404);
        }

        $note = Note::create([
            'user_id' => auth()->id(),
            'video_id' => $videoId,
            'content' => $request->content,
            'timestamp' => $request->timestamp, // Mutator sẽ xử lý
        ]);

        return response()->json(['message' => 'Ghi chú đã được lưu', 'note' => $note], 201);
    }

    /**
     * @OA\Put(
     *     path="/video/{video_id}/notes/{note_id}",
     *     summary="Cập nhật ghi chú của video",
     *     description="API này cập nhật nội dung của một ghi chú do người dùng hiện tại tạo.",
     *     tags={"Notes"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="videoId",
     *         in="path",
     *         required=true,
     *         description="ID của video",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="noteId",
     *         in="path",
     *         required=true,
     *         description="ID của ghi chú",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content", "timestamp"},
     *             @OA\Property(property="content", type="string", maxLength=1000, example="Nội dung mới của ghi chú"),
     *             @OA\Property(property="timestamp", type="string", example="02:30", description="Định dạng MM:SS hoặc số giây")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ghi chú đã được cập nhật thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ghi chú đã được cập nhật"),
     *             @OA\Property(property="note", type="object",
     *                 @OA\Property(property="id", type="integer", example=5),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="video_id", type="integer", example=10),
     *                 @OA\Property(property="content", type="string", example="Nội dung mới của ghi chú"),
     *                 @OA\Property(property="timestamp", type="string", example="02:30")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Người dùng không có quyền cập nhật ghi chú",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Bạn không có quyền cập nhật ghi chú này")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Video hoặc ghi chú không tồn tại",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Video không tồn tại")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ghi chú không thuộc video này",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ghi chú không thuộc video này")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dữ liệu đầu vào không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="content", type="array",
     *                     @OA\Items(type="string", example="Trường content là bắt buộc.")
     *                 ),
     *                 @OA\Property(property="timestamp", type="array",
     *                     @OA\Items(type="string", example="Trường timestamp không đúng định dạng.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, $videoId, Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            return response()->json(['message' => 'Bạn không có quyền cập nhật ghi chú này'], 403);
        }

        $video = Video::find($videoId);
        if (!$video) {
            return response()->json(['message' => 'Video không tồn tại'], 404);
        }

        if ($note->video_id != $videoId) {
            return response()->json(['message' => 'Ghi chú không thuộc video này'], 400);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'timestamp' => 'required|regex:/^\d+(:[0-5]\d)?$/', // Chấp nhận số hoặc MM:SS
        ]);

        $note->update($request->only('content', 'timestamp'));
        return response()->json(['message' => 'Ghi chú đã được cập nhật', 'note' => $note]);
    }

    /**
     * @OA\Delete(
     *     path="/video/{video_id}/notes/{note_id}",
     *     summary="Xóa ghi chú của video",
     *     description="API này xóa một ghi chú của người dùng hiện tại dựa trên ID của video và note.",
     *     tags={"Notes"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="videoId",
     *         in="path",
     *         required=true,
     *         description="ID của video",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="noteId",
     *         in="path",
     *         required=true,
     *         description="ID của ghi chú cần xóa",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ghi chú đã được xóa thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ghi chú đã được xóa")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Người dùng không có quyền xóa ghi chú này",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Bạn không có quyền xóa ghi chú này")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Video hoặc ghi chú không tồn tại",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Video không tồn tại")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ghi chú không thuộc video này",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ghi chú không thuộc video này")
     *         )
     *     )
     * )
     */
    public function destroy($videoId, Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            return response()->json(['message' => 'Bạn không có quyền xóa ghi chú này'], 403);
        }

        $video = Video::find($videoId);
        if (!$video) {
            return response()->json(['message' => 'Video không tồn tại'], 404);
        }

        if ($note->video_id != $videoId) {
            return response()->json(['message' => 'Ghi chú không thuộc video này'], 400);
        }

        $note->delete();
        return response()->json(['message' => 'Ghi chú đã được xóa']);
    }
}
