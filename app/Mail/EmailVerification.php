<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {


        return $this->view('emails.verify')
                    ->subject('Verify Your Email Address')
                    ->with([
                        'token' => $this->token,
                        'verificationUrl' => config('app.url') . '/api/verify-email?token=' . $this->token
                    ]);
    }
}