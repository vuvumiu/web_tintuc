<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterConfirmMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $email,
        public string $confirmUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận đăng ký nhận tin khuyến mại',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter_confirm',
            with: [
                'email' => $this->email,
                'confirmUrl' => $this->confirmUrl,
            ],
        );
    }
}
