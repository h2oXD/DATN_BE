<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseApprovalRequestedNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;
    public $course;
    public $user;
    public $recipientId; // Thêm property để lưu ID người nhận

    public function __construct(Course $course, User $user, $recipientId) // Thêm $recipientId vào constructor
    {
        $this->course = $course;
        $this->user = $user;
        $this->recipientId = $recipientId;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'created_at' => $notifiable->created_at,
            'user_name' => $this->user->name,
            'user_avatar' => $this->user->profile_picture,
            'course_id' => $this->course->id,
            'user_id' => $this->user->id,
            'message' => 'Khóa học "' . $this->course->title . '" cần được phê duyệt.',
            'type' => 'approval_request',
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->recipientId); // Tạo PrivateChannel dựa trên user ID
    }

    public function toBroadcast($notifiable)
    {
        return ['data' => $this->toArray($notifiable), 'read_at' => null];
    }
}
