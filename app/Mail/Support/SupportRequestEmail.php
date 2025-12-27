<?php

namespace App\Mail\Support;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SupportRequestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $issueDetails;
    public $adminName;
    // public $emailLog;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $issueDetails, $adminName)
    {
        //
        $this->name = $name;
        $this->issueDetails = $issueDetails;
        $this->adminName =$adminName;
        // $this->emailLog = $emailLog;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.support.support_request')->subject('New Support Request');
    }
}
