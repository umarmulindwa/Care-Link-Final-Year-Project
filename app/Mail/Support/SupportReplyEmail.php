<?php

namespace App\Mail\Support;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SupportReplyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $supportDetails;
    public $title;
    // public $emailLog;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $supportDetails, $title)
    {
        $this->supportDetails = $supportDetails;
        $this->title = $title;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.support.support_reply')->subject($this->title);
    }
}
