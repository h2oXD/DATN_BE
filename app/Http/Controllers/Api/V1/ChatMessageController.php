<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\NewChatMessage;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\ChatRoomUser;
use App\Models\MutedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatMessageController extends Controller
{
    //

    public function index($roomId)
    {
        return ChatMessage::with('user')->where('chat_room_id', $roomId)->get();
    }

    public function store(Request $request, $roomId)
    {
        $user = $request->user();

        // Kiểm tra phòng chat có tồn tại không
        $chatRoom = ChatRoom::findOrFail($roomId);

        // Kiểm tra người dùng có tham gia phòng chat không
        if (!ChatRoomUser::where('chat_room_id', $roomId)->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'You are not a member of this chat room'], 403);
        }


        // Kiểm tra nếu người dùng bị mute
        // if (MutedUser::where('chat_room_id', $roomId)->where('user_id', $user->id)->exists()) {
        //     return response()->json(['message' => 'You are muted'], 403);
        // }


        // Kiểm tra xem người dùng có gửi tin nhắn hoặc file không
        if (!$request->has('message') && !$request->hasFile('file')) {
            return response()->json(['message' => 'Message or file is required'], 400);
        }

        $message = ChatMessage::create([
            'chat_room_id' => $roomId,
            'user_id' => $user->id,
            'message' => $request->message,
            'file_path' => $request->file('file')?->store('chat_files'),
        ]);

        // Phát tin nhắn realtime
        broadcast(new NewChatMessage($message))->toOthers();

        return response()->json($message);
    }

    public function destroy(Request $request, $id)
{
    $user = $request->user();
    $message = ChatMessage::findOrFail($id);

    // Kiểm tra xem user có quyền xóa không
    $isOwner = ChatRoom::where('id', $message->chat_room_id)->where('owner_id', $user->id)->exists();
    if ($user->id !== $message->user_id && !$user->hasRole('admin') && !$isOwner) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Xóa file nếu có
    if ($message->file_path) {
        Storage::delete($message->file_path);
    }

    $message->delete();
    return response()->json(['message' => 'Message deleted']);
}

}
