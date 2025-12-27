<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailLog;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog)
    {
        $this->emailLog = $emailLog;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->emailLog->user->email, $this->emailLog->user->name)->subject($this->emailLog->subject)
            ->html($this->emailLog->body);
    }
}
