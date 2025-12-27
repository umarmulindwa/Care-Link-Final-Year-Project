<?php

namespace App\Jobs;

use App\Mail\CustomMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use App\Models\StaffProfile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\MainEmailLog;
use Illuminate\Support\Str;

class ProcessDispatchMails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $mail;
    protected $staff;
    protected $emailLog;
    public function __construct(MainEmailLog $email, Mailable $mail, $staff)
    {
        $this->emailLog = $email;
        $this->mail = $mail;
        $this->staff = $staff;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        sleep(10);
        //
        try {
            //code...
            $this->emailLog->updateCode();
            // if (env('APP_ENV') != 'local') {
            //     Mail::to($this->staff->email)->send($this->mail);
            // }
            /**
             * @helper function dispatches emails to the mail server
             */

            if (Str::lower(env('APP_ENV', 'local')) == 'production') {
                $emailBody = $this->emailLog->toArray();

                pushEmailsToMailServer([
                    "subject" => $emailBody['subject'],
                    "body" => $emailBody['body'],
                    "to" => $emailBody['to'],
                    "cc" => $emailBody['carbon_copy'],
                    "bcc" => null,
                    "from" => env("MAIL_USERNAME"),
                    "from_name" => env("MAIL_FROM_NAME"),
                    "reply_to" => env("MAIL_USERNAME"),
                    "server_name" => env('VITE_COUNTRY_OFFICE'),
                    "attachments" => null,
                    "headers" => null,
                    "is_reminder" => $emailBody['is_reminder'],
                    "channel" => 'Main ' . env('VITE_COUNTRY_OFFICE'),
                    "reference_code" => $emailBody['reference_code'],
                ]);




                Log::info("mail dispatched: ");
            }

            try {
                if (env('MAIL_FROM_ADDRESS') === 'hello@example.com') {
                    Mail::to($this->emailLog->to)->cc($this->emailLog->carbon_copy)->send(new CustomMail($this->emailLog));
                }
                Log::info("local mail dispatched: ");
            } catch (\Throwable $th) {
            }
            $this->emailLog->update([
                "is_sent" => 1
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error("Send Mail Error: " . $th->getMessage());
        }
    }
}
