<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseApprove extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $course, public $lecturer)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject('Thông báo đã phê duyệt khóa học')
            ->view('emails.course_approve', [
                'notifiable' => $notifiable,
                'course' => $this->course, // nếu bạn có thông tin khóa học
                'reason' => $this->course->admin_comment  // nếu bạn muốn gửi lý do từ chối
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'created_at' => $notifiable->created_at,
            'course_id' => $this->course->id,
            'course_thumbnail' => $this->course->thumbnail,
            'course_title' => $this->course->title,
            'message' => 'Khóa học "' . $this->course->title . '" đã được phê duyệt.',
            'type' => 'approve_course',
            'lecturer_id' => $this->lecturer->id
        ];
    }
    public function broadcastOn()
    {
        return new PrivateChannel('notifications-client.' . $this->lecturer->id); // Tạo PrivateChannel dựa trên user ID
    }

    public function toBroadcast(object $notifiable)
    {
        return ['data' => $this->toArray($notifiable), 'read_at' => null];
    }
}
