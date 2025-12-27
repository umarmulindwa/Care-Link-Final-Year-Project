<?php

namespace App\Jobs;

use App\Mail\CustomMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Mail\Mailable;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\MainEmailLog;
use Illuminate\Support\Str;


class ProcessDispatchEmailServiceProvider implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $mail;
    protected $staff;
    protected $emailLog;
    public function __construct(MainEmailLog $email, Mailable $mail, ServiceProvider $staff)
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
            $environmentStatus = env('APP_ENV');
            // if ($environmentStatus != 'local') {
            //     Mail::to($this->staff->email)->send($this->mail);
            // Log::info("Should be sent: ");

            // }

            // $this->emailLog->update([
            //     // "is_sent" => 1
            // ]);
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
            }

            try {
                if (Str::lower(env('APP_ENV', 'local')) == 'local') {
                    Mail::to($this->emailLog->to)->cc($this->emailLog->carbon_copy)->send(new CustomMail($this->emailLog));

                    Log::info("local mail dispatched: ");
                }
            } catch (\Throwable $th) {
                Log::info("local mail dispatching failed: ".$th->getMessage());
            }

            $this->emailLog->update([
                "is_sent" => 1
            ]);

            Log::info("mail dispatched: " . $environmentStatus);
        } catch (\Throwable $th) {
            //throw $th;
            $this->emailLog->update([
                "is_sent" => 0
            ]);
            Log::error("Send Mail Error: " . $th->getMessage());
        }
    }
}
