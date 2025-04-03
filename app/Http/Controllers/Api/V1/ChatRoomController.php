<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\ChatRoomUser;
use Illuminate\Http\Request;

class ChatRoomController extends Controller
{
    //

    public function index(Request $request)
    {
        $user = $request->user();

        // Nếu là giảng viên, hiển thị nhóm chat của các khóa học họ tạo
        // if ($user->hasRole('lecturer')) {
        //     return ChatRoom::whereHas('course', function ($query) use ($user) {
        //         $query->where('user_id', $user->id);
        //     })->with('course')->get();
        // }

        // Nếu là học viên, hiển thị nhóm chat của các khóa học họ đã đăng ký
        // return ChatRoom::whereHas('course.enrollments', function ($query) use ($user) {
        //     $query->where('user_id', $user->id);
        // })->with('course')->get();

        $chatRoom = ChatRoomUser::where('user_id',$user->id)->with(['chatRoom','user'])->get();
        return response()->json([
            'room' => $chatRoom
        ],200);
    }

    public function show(Request $request, $id)
    {
        $chatRoom = ChatRoom::with(['messages.user','users'])->findOrFail($id);
        $user = $request->user();

        // Kiểm tra user có quyền truy cập không
        if (
            $user->id !== $chatRoom->owner_id &&
            !$chatRoom->course->enrollments()->where('user_id', $user->id)->exists()
        ) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($chatRoom);
    }


    public function destroy(Request $request, $id)
    {
        $chatRoom = ChatRoom::findOrFail($id);
        $user = $request->user();

        // Kiểm tra quyền: chỉ giảng viên sở hữu khóa học mới có thể xóa nhóm chat
        if ($user->id !== $chatRoom->owner_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Nếu khóa học bị chuyển về draft, vô hiệu hóa nhóm chat thay vì xóa
        if ($chatRoom->course->status === 'draft') {
            $chatRoom->update(['is_active' => false]);
            return response()->json(['message' => 'Nhóm chat đã bị vô hiệu hóa do khóa học chuyển về trạng thái draft']);
        }

        // Nếu không phải draft, xóa nhóm chat
        $chatRoom->delete();
        return response()->json(['message' => 'Nhóm chat đã được xóa']);
    }
}
