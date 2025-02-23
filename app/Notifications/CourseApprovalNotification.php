<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage; //Import thêm thư viện này


class CourseApprovalNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $courseId, public $userId)
    {
        //
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast']; //Thêm broadcast vào để lắng nghe
    }

    public function toDatabase($notifiable)
    {
        return [
            'course_id' => $this->courseId,
            'user_id' => $this->userId,
            'message' => 'Yêu cầu phê duyệt khóa học ' . $this->courseId . ' từ người dùng ' . $this->userId,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'course_id' => $this->courseId,
            'user_id' => $this->userId,
            'message' => 'Yêu cầu phê duyệt khóa học ' . $this->courseId . ' từ người dùng ' . $this->userId,
        ]);
    }
    public function broadcastOn()
    {
        return new PrivateChannel('sending');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    // public function toArray(object $notifiable): array
    // {
    //     return [
    //         //
    //     ];
    // }
}
                