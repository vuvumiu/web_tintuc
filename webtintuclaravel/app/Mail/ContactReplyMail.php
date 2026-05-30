<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $replyData
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Phản hồi từ đội ngũ hỗ trợ - ' . ($this->replyData['subject'] ?? 'Liên hệ của bạn'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact_reply',
            with: ['reply' => $this->replyData],
        );
    }
}
