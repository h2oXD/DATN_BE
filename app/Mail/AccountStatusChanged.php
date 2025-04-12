<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class AccountStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $statusText;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->statusText = $this->getStatusText($user->status);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông báo thay đổi trạng thái tài khoản',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.account_status_changed',
            with: [
                'user' => $this->user,
                'statusText' => $this->statusText,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Trả về mô tả trạng thái tài khoản.
     */
    protected function getStatusText($status): string
    {
        return match ((int) $status) {
            0 => 'Hoạt động',
            1 => 'Khóa chức năng giảng viên',
            2 => 'Khóa chức năng giảng viên và học viên',
            default => 'Không xác định',
        };
    }
}
