<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $token,$expires_at;
    public function __construct($token,$expires_at)
    {
        $this->token = $token;
        $this->expires_at = $expires_at;
    }

    public function build(){
        return $this->markdown('emails.accounts.otp')->subject('OTP');
    }

}
