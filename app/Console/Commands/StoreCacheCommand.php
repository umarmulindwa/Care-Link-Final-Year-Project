<?php

namespace App\Console\Commands;

use App\Models\AdministrationEmailLogs;
use App\Models\MainEmailLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class StoreCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailer:dispatch-pending-mails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store In-Memory Cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            if (Str::lower(env('APP_ENV', 'local')) == 'production') {
                getMailServerCredentials();



                $unSentMails = MainEmailLog::where('is_sent', 0)->limit(200)->get();

                foreach ($unSentMails as $key => $item) {
                    try {
                        $emailBody = $item->toArray();
                        try {
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

                            $item->update(['is_sent' => 1]);
                        } catch (\Throwable $th) {
                            $item->update(['is_sent' => 0]);
                            throw $th;
                        }
                    } catch (\Throwable $th) {
                        $item->update(['is_sent' => 0]);

                        throw $th;
                    }
                }

                $unSentMails = AdministrationEmailLogs::where('is_sent', 0)->limit(100)->get();

                foreach ($unSentMails as $key => $item) {
                    try {
                        $emailBody = $item->toArray();
                        try {
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

                            $item->update(['is_sent' => 1]);
                        } catch (\Throwable $th) {
                            $item->update(['is_sent' => 0]);
                            throw $th;
                        }
                    } catch (\Throwable $th) {
                        $item->update(['is_sent' => 0]);

                        throw $th;
                    }
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
