<?php

namespace App\Listeners;

use App\Mail\AccountActivationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\AdministrationEmailLogs;
use App\Models\ServiceProvider;
use App\Models\User;
use App\Models\StaffProfile;
use illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Log;


class BeforeEmailSentListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSending $event): void
    {
        $subject = $event->message->getSubject();
        $recipient = collect($event->message->getTo())->map(fn ($item) => $item->getAddress());
        // $carbonCopy = collect($event->message->getCc())->map(fn ($item) => $item->getAddress());
        if (strpos($subject, 'Reset Password Notification') !== false || strpos($subject, 'Verify Email Address') !== false) {

            AdministrationEmailLogs::create([
                'to' => $recipient[0],
                'subject' => $subject,
                'body' => $event->message->getHtmlBody(),
                'submitted_by' => 'system',
                'is_sent' => false,
                'is_reminder' => false
            ]);
        }

        //send email to all super Admins to activate newly created accounts
        if (strpos($subject, 'Verify Email Address') !== false) {

            // //getting all super Admins
            $admins = User::select('email', 'name')->permission('s_admin')->get();

            // sending support request email to all  super admins
            if (count($admins) > 0) {

                foreach ($admins as $admin) {
                    //checking whether the staff is on Leave
                    $staff = StaffProfile::where('email', $admin->email)->first();
                    try {
                        $subject =  "New Service Provider Registered.";
                        $vendor = ServiceProvider::latest()->first();
                        $mail = new AccountActivationMail($staff->name,$vendor);
                        globalSendEmail($staff, $subject, $mail, null, false, null, [
                            "personal_id" => $staff->email,
                            "action" => env('APP_URL') . "/staff_register",
                            "description" => "A new Service Provider has been registered, please activate their account.",
                            "submitted_by" => 'Platform',
                            "title" => $subject,
                            "profile_photo_path" => null
                        ]);
                    } catch (\Exception $ex) {
                        Log::error("Send Mail Error: " . $ex);
                    }
                }
            }
        }
    }
}
