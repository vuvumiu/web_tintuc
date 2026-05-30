<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetUrl;
    public $token;

    public function __construct($user, $resetUrl, $token)
    {
        $this->user = $user;
        $this->resetUrl = $resetUrl;
        $this->token = $token;
    }

    public function build(): self
    {
        return $this
            ->subject('Đặt lại mật khẩu - ' . config('app.name'))
            ->view('emails.reset-password')
            ->with([
                'userName'    => $this->user->fullname ?? $this->user->username,
                'resetUrl'    => $this->resetUrl,
                'token'       => $this->token,
            ]);
    }
}
