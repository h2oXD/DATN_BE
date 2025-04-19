<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CensorWithdraw extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $status;
    public $bank_name;
    public $bank_number;
    public $bank_nameUser;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $status, $bank_name, $bank_number, $bank_nameUser)
    {
        $this->user             = $user;
        $this->status           = $status;
        $this->bank_name        = $bank_name;
        $this->bank_number      = $bank_number;
        $this->bank_nameUser    = $bank_nameUser;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông báo kết quả yêu cầu rút tiền',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.censor_withdraw',
            with: [
                'user'              => $this->user,
                'statusTransaction' => $this->status,
                'bank_name'         => $this->bank_name,
                'bank_number'       => $this->bank_number,
                'bank_nameUser'     => $this->bank_nameUser,
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
