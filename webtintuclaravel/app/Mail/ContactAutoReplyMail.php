<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactAutoReplyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $contactData
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Chúng tôi đã nhận được liên hệ của bạn!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact_auto_reply',
            with: ['contact' => $this->contactData],
        );
    }
}
