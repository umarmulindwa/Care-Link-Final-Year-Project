<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserPasswordResetAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */


    public $user_name;
    public $email_address;
    public $new_password;


    public function __construct($user_name, $email_address, $new_password)
    {
        $this->user_name = $user_name;
        $this->email_address = $email_address;
        $this->new_password = $new_password;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.staff-register.user_password_reset_mail')
            ->subject('Your Password has been reset.')
            ->with($this->user_name)
            ->with($this->email_address)
            ->with($this->new_password);
    }
}
