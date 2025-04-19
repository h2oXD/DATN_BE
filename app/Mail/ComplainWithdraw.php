<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComplainWithdraw extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $status;
    public $feedback;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $status, $feedback)
    {
        $this->user         = $user;
        $this->status       = $status;
        $this->feedback     = $feedback;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông báo kết quả giải quyết yêu cầu khiếu nại',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.complain_withdraw',
            with: [
                'user'              => $this->user,
                'statusComplain'    => $this->status,
                'feedback'          => $this->feedback,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
