<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccountActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $vendor;

    // public $emailLog;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$vendor)
    {
        //
        $this->name = $name;
        $this->vendor = $vendor;
        // $this->emailLog = $emailLog;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.new_vendor_account_pending_verification')->subject('New Service Provider Registered');
    }
}
