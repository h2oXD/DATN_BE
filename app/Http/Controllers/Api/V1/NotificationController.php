<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * Đánh dấu thông báo là đã đọc.
     */
    public function markAsRead(Notification $notification)
    {
        if (Auth::id() !== $notification->notifiable_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $notification->update(['read_at' => now()]);
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Xóa thông báo.
     */
    public function destroy(Notification $notification)
    {
        if (Auth::id() !== $notification->notifiable_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $notification->delete();
        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }

    /**
     * Xóa tất cả thông báo.
     */
    public function destroyAll()
    {
        Auth::user()->notifications()->delete();
        return response()->json([
            'success' => true,
            'message' => 'All notifications deleted'
        ]);
    }
}
