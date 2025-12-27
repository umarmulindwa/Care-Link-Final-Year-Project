<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemporaryPasswordNewAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */


    public $new_staff_name;
    public $temporary_password;
    public $email_address;


    public function __construct($new_staff_name, $temporary_password, $email_address)
    {
        $this->new_staff_name = $new_staff_name;
        $this->temporary_password = $temporary_password;
        $this->email_address = $email_address;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.staff-register.new_staff_temp_password_mail')
            ->subject('Your account has been created.')
            ->with($this->new_staff_name)
            ->with($this->temporary_password)
            ->with($this->email_address);
    }
}
