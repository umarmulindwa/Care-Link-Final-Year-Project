<?php


use App\Models\EmailLog;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response as ResponseAlias;
use Illuminate\Support\Facades\DB;
use App\Models\StaffProfile;
use App\Models\LeaveDelegation;
use App\Models\Leave;
use App\Models\User;
use App\Models\IgnoredEmail;
use Carbon\Carbon;
use App\Jobs\ProcessDispatchMails;
use App\Jobs\ProcessDispatchEmailServiceProvider;
use App\Models\MainEmailLog;
use App\Models\Notification;
use App\Leave\LeaveHistory;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;



// */
// * Global function to send mail to the recipient and his/her officer in charge (OIC)
// * @params: $recipient - the intended recipient of the mail. Must be a User object.
// * @params: $subject - the email subject
// * @params: $mailable - the email class which inherits the Mailable class
// *
// *
// * For Example
// * To send an email to both the recipient and his/her OIC do the following
// *
// * $mailable = new App\Mail\Staff\HelloWorldMail();
// * $recipient = App\User::query()->find($recipientsId);
// * $subject = "Hello this is a sample Email";
// *
// * Now call the globalSendEmail() and pass the above variables as parameters to the function as shown below.
// *
// * globalSendEmail($recipient, $subject, $mailable)
// *
// * NOTE: You can call the above function anywhere in your class e.g controller class,
// * request class or repository class. ENSURE THAT YOU PASS IN ALL REQUIRED PARAMETERS TO THE FUNCTION.
// */

// function globalSendQueueEmail($recipient, $subject, $mailable, $carbonCopy = null, $isReminder = false, $referenceCode = null)
// {
//     //Check if the recipient is a UNICEF staff
//     if (!property_exists($recipient, "user_type")) {
//         $recipient = User::where("email", $recipient->email)->first();
//     }
//     if ($recipient != NULL && ($recipient->user_type == 'unicef' || $recipient->user_type == 'newstaff')) {
//         //recipient details
//         $recipientProfile = StaffProfile::query()->where('email', $recipient->email)->first();
//         //if the recipient exited unicef make thier substitute the recipient
//         $getSubstitute = getExitedStaffOic($recipientProfile);
//         if ($getSubstitute != null) {
//             $recipientProfile = $getSubstitute;
//         }

//         //check whether the reciepient is on leave
//         $todayDate = now();
//         $recieptActiveLeaves = Leave::where('end', '>=', $todayDate)->where('start', '<=' , $todayDate )->where('personal_id', $recipientProfile->personal_id)->with(['staffDelegated'])->get();

//         /**
//          * If the staff is on leave send emails to their OICs
//          */
//         if (count($recieptActiveLeaves) > 0) {

//             foreach($recieptActiveLeaves as $activeLeave){
//                 $oic = $activeLeave->staffDelegated;
//                 $recipientOICEmailLog = MainEmailLog::create([ // Now send a copy of email to the recipient's OIC
//                     "to" => $oic->email,
//                     "subject" => $subject . " [OIC]",
//                     "is_sent" => 0,
//                     "submitted_by" => auth()->user()->name,
//                     "reference_code" => $referenceCode,
//                     "carbon_copy" => $carbonCopy,
//                     "is_reminder" => $isReminder
//                 ]);

//                 try {
//                     $recipientOICEmailLog->update([
//                         'body' => $mailable->render()
//                     ]);
//                     ProcessDispatchMails::dispatch($recipientOICEmailLog, $mailable, StaffProfile::where("email", $oic->email)->first())->onQueue('emails');

//                     // Mail::to($recipientsOIC->email)->send($mailable);

//                 } catch (\Exception $e) {
//                     Log::error("Global Send Mail OIC failed: " . $e->getMessage());
//                 }
//             }
//         }
//     }

//     // send an email to recipient.
//     // The recipient can be a UNICEF staff, service provider (SP) or an implementing partner (IP)

//     $recipientsEmailLog = MainEmailLog::create([
//         "to" => $recipient->email,
//         "subject" => $subject,
//         "is_sent" => 0,
//         "submitted_by" => auth()->user()->name,
//         "reference_code" => $referenceCode,
//         "carbon_copy" => $carbonCopy,
//         "is_reminder" => $isReminder
//     ]);

//     // $recipientsEmailLog->updateCode();

//     try {
//         $recipientsEmailLog->update([
//             'body' => $mailable->render()
//         ]);
//         ProcessDispatchMails::dispatch($recipientsEmailLog, $mailable, StaffProfile::where("email", $recipient->email)->first())->onQueue('emails');

//         // Mail::to($recipient->email)->send($mailable);


//     } catch (\Exception $e) {
//         Log::error("Global Send Mail failed: " . $e->getMessage());
//     }
// }
// */
// * Global function to send mail to the recipient and his/her officer in charge (OIC)
// * @params: $recipient - the intended recipient of the mail. Must be a User object.
// * @params: $subject - the email subject
// * @params: $mailable - the email class which inherits the Mailable class
// * @params: $carbonCopy - specificies whether the email is a carboncopy
// * @params: $isReminder - specificies whether the email is a reminder
// * @params: $referenceCode - reference code for the request
// * @params: $notification - email notification, should have properties of the Notification Object
// *
// *
// *
// * For Example
// * To send an email to both the recipient and his/her OIC do the following
// *
// * $mailable = new App\Mail\Staff\HelloWorldMail();
// * $recipient = App\User::query()->find($recipientsId);
// * $subject = "Hello this is a sample Email";
// *
// * Now call the globalSendEmail() and pass the above variables as parameters to the function as shown below.
// *
// * globalSendEmail($recipient, $subject, $mailable, $carbonCopy, $isReminder, $referenceCode, $notification)
// *
// * NOTE: You can call the above function anywhere in your class e.g controller class,
// * request class or repository class. ENSURE THAT YOU PASS IN ALL REQUIRED PARAMETERS TO THE FUNCTION.
// */

function globalSendEmail($recipient, $subject, $mailable, $carbonCopy = null, $isReminder = false, $referenceCode = null,  $notification = null)
{
    try {
        $recipientUser = null;
        if ($recipient == null) {
            return;
        }
        //Check if the recipient is a UNICEF staff
        if (!property_exists($recipient, "user_type")) {
            $recipientUser = User::where("email", $recipient->email)->first();
        }
        if ($recipientUser != NULL && $recipientUser->user_type == 'unicef') {
            //recipient details
            $recipientProfile = StaffProfile::query()->where('email', $recipientUser->email)->first();
            //if the recipient exited unicef make thier substitute the recipient
            $getSubstitute = getExitedStaffOic($recipientProfile);
            if ($getSubstitute != null) {
                $recipientProfile = $getSubstitute;
            }

            //check whether the reciepient is on leave
            $todayDate = now();
            $recieptActiveLeaves = Leave::where('end', '>=', $todayDate)->where('start', '<=', $todayDate)->where('leaving_staff', $recipientProfile->email)->with(['staffDelegated'])->get();

            /**
             * If the staff is on leave send emails to their OICs
             */
            if (count($recieptActiveLeaves) > 0) {

                foreach ($recieptActiveLeaves as $activeLeave) {
                    $oic = $activeLeave->staffDelegated;

                    $shouldReceive = shouldReceiveEmail($oic->email, $subject);

                    if ($shouldReceive == true) {
                        $recipientOICEmailLog = MainEmailLog::create([ // Now send a copy of email to the recipient's OIC
                            "to" => $oic->email,
                            "subject" => $subject . " [OIC]",
                            "is_sent" => 0,
                            "submitted_by" => auth()->user()->name,
                            "reference_code" => $referenceCode,
                            "carbon_copy" => $carbonCopy,
                            "is_reminder" => $isReminder
                        ]);

                        try {

                            $oicProfile = StaffProfile::where("email", $oic->email)->first();

                            /**
                             * NOTE:
                             * The emaillog is saved before the actual email is sent so that in case the job fails
                             * this email will automatically go under the Outbox section in the email logs
                             */
                            $recipientOICEmailLog->update([
                                'body' => $mailable->render()
                            ]);

                            //sending the email
                            ProcessDispatchMails::dispatch($recipientOICEmailLog, $mailable, $oicProfile)->onQueue('emails');

                            //send a notification
                            if ($notification != null) {
                                Notification::create([
                                    "personal_id" => $oicProfile->email,
                                    "action" => array_key_exists('action', $notification) ? $notification['action'] : null,
                                    "description" => array_key_exists('description', $notification) ? $notification['description'] : null,
                                    "submitted_by" => array_key_exists('submitted_by', $notification) ? $notification['submitted_by'] : null,
                                    "title" => array_key_exists('title', $notification) ? $notification['title']  : null,
                                    "profile_photo_path" => array_key_exists('profile_photo_path', $notification) ? $notification['profile_photo_path'] : null
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error("Global Send Mail OIC failed: " . $e->getMessage(), ["errors" => $e->getTrace()]);
                        }
                    }
                }
            }
        }


        $shouldReceive = shouldReceiveEmail($recipient->email, $subject);


        if ($shouldReceive == true) {
            // send an email to recipient.
            // The recipient can be a UNICEF staff, service provider (SP) or an implementing partner (IP)

            $recipientEmailLog = MainEmailLog::create([ // Now send a copy of email to the recipient's OIC
                "to" => $recipient->email,
                "subject" => $subject,
                "is_sent" => 0,
                "submitted_by" => Auth::check() ? auth()->user()->name : 'Platform',
                "reference_code" => $referenceCode,
                "carbon_copy" => $carbonCopy,
                "is_reminder" => $isReminder
            ]);

            // $recipientsEmailLog->updateCode();

            try {
                $recipientEmailLog->update([
                    'body' => $mailable->render()
                ]);


                $recipientProfile = StaffProfile::where("email", $recipient->email)->first();
                $recipientProfile =  $recipientProfile == null ? $recipient : $recipientProfile;
                ProcessDispatchMails::dispatch($recipientEmailLog, $mailable, $recipientProfile)->onQueue('emails');

                //send a notification
                if ($notification != null) {
                    Notification::create([
                        "personal_id" => $recipientProfile->email,
                        "action" => array_key_exists('action', $notification) ? $notification['action'] : null,
                        "description" => array_key_exists('description', $notification) ? $notification['description'] : null,
                        "submitted_by" => array_key_exists('submitted_by', $notification) ? $notification['submitted_by'] : null,
                        "title" => array_key_exists('title', $notification) ? $notification['title'] . " OIC" : null,
                        "profile_photo_path" => array_key_exists('profile_photo_path', $notification) ? $notification['profile_photo_path'] : null
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error("Global Send Mail failed: " . $e->getMessage(), ["errors" => $e->getTrace()]);
            }
        }
    } catch (\Throwable $th) {
        Log::error("Global Send Mail failed: " . $th->getMessage(), ["errors" => $th->getTrace()]);
    }
}

function shouldReceiveEmail($recipient, $subject)
{
    $isBlackListed = IgnoredEmail::where('email', $recipient)->where('subject', $subject)->where('is_deactivated', true)->first();
    if (is_null($isBlackListed)) {
        return true;
    } else {
        return false;
    }
}
/**
 * Global Send mail for Service Providers
 * Logs are stored in the MainEmailLog table
 */
function globalSendEmailServiceProvider($recipient, $subject, $mailable, $carbonCopy = null, $isReminder = false, $referenceCode = null,  $notification = null)
{

    // send an email to recipient.
    // The recipient can be a UNICEF staff, service provider (SP) or an implementing partner (IP)

    $recipientEmailLog = MainEmailLog::create([ // Now send a copy of email to the recipient's OIC
        "to" => $recipient->email,
        "subject" => $subject,
        "is_sent" => 0,
        "submitted_by" => auth()->user()->name,
        "reference_code" => $referenceCode,
        "carbon_copy" => $carbonCopy,
        "is_reminder" => $isReminder
    ]);

    // $recipientsEmailLog->updateCode();

    try {
        $recipientEmailLog->update([
            'body' => $mailable->render()
        ]);

        $serviceProvider = ServiceProvider::where('email', $recipient->email)->first();
        ProcessDispatchEmailServiceProvider::dispatch($recipientEmailLog, $mailable, $serviceProvider)->onQueue('emails');
        //send a notification
        if ($notification != null) {
            Notification::create([
                "personal_id" => $serviceProvider->email,
                "action" => array_key_exists('action', $notification) ? $notification['action'] : null,
                "description" => array_key_exists('description', $notification) ? $notification['description'] : null,
                "submitted_by" => array_key_exists('submitted_by', $notification) ? $notification['submitted_by'] : null,
                "title" => array_key_exists('title', $notification) ? $notification['title'] . " OIC" : null,
                "profile_photo_path" => array_key_exists('profile_photo_path', $notification) ? $notification['profile_photo_path'] : null
            ]);
        }
    } catch (\Exception $e) {
        Log::error("Global Send Mail failed: " . $e->getMessage());
    }
}



/*
* If user1 goes on leave and user1 indicates that user2 is their OIC (Officer in Charge - or deputy)
* Then getUsersSupervisor(user2) should return user1 thereby indicating that user1 is the supervisor of user2 or user2 is the OIC of user1
* @parameters: $user, in this case, running getUsersSupervisor("user2") returns who the supervisor is user2 is.
*/
function getUsersSupervisor($user)
{
    $staff = StaffProfile::query()->where('email', $user->email)->first();
    $leaveDelegation = LeaveDelegation::query()->where('delegated_role_to', $staff->personal_id)
        ->orderBy('created_at', 'desc')->first();
    $supervisor = Leave::query()->where('id', $leaveDelegation->leave_id)
        ->orderBy('created_at', 'desc')->first();
    return $supervisor;
}


function getSupervisorsOIC($user)
{
    $staff = StaffProfile::query()->where('email', $user->email)->first();

    if ($staff->exited_unicef) return getExitedStaffOic($staff);

    $staffsLeaveDetails = Leave::query()->where('personal_id', $staff->personal_id)
        ->orderBy('created_at', 'desc')->first();

    $leaveDelegation = LeaveDelegation::query()->where('leave_id', $staffsLeaveDetails->id)
        ->orderBy('created_at', 'desc')->first();

    $officerInCharge = StaffProfile::query()->where('personal_id', $leaveDelegation->delegated_role_to)->first();

    return $officerInCharge;
}

/*
 * Find delegate staff/oic of exited staff.
 */
function getExitedStaffOic(\Illuminate\Database\Eloquent\Model $staff)
{
    if ($staff->exited_unicef == 0) return Null;

    //get their delegate/oic
    $exit_logs = \App\Models\StaffExitStatusLog::query()
        ->where('staff_email', '=', $staff->email)
        ->where('status', '=', 1)
        ->orderBy('id', 'desc')
        ->select('roles_deligated_to', 'inherited_permissions')
        ->first();

    $oic =  StaffProfile::query()
        ->where('personal_id', '=', $exit_logs->roles_deligated_to)
        ->first();

    if (!$oic) return Null;

    $oic_current_permissions = (User::query()
        ->where('email', '=', $oic->user_id)
        ->first())->allPermissions;


    $inherited_permissions = json_decode($exit_logs->inherited_permissions);
    $combined_permissions = array_merge($inherited_permissions, $oic_current_permissions);
    $oic->user_permissions = $combined_permissions;

    return $oic;
}

function generateAndUpdateNumber($name)
{
    $file = $name . "_number_date.txt";
    $currentDate = date('Y-m-d');
    if (file_exists($file)) {
        $data = file_get_contents($file);
        list($lastDate, $number) = explode('|', $data);
        if (date('Y-m', strtotime($lastDate)) != date('Y-m')) {
            $number = 1;
        } else {
            $number++;
        }
    } else {
        $number = 1;
    }
    $newData = "$currentDate|$number";
    file_put_contents($file, $newData);

    return $number;
}

function storeFile($path, $file, $type = "")
{
    $filen = $type . '_' . implode('-', str_split(substr(strtolower(md5(microtime() . rand(1000, 9999))), 0, 30), 6));
    $filename = $path . $filen . '.' . $file->getClientOriginalExtension();
    Storage::disk('public')->put($filename, File::get($file));
    return (object)["name" => $file->getClientOriginalName(), "path" => $filename];
}

function deleteFile($path)
{
    $output =  Storage::disk('public')->delete($path);
    return (object)["message" => $output];
}

//check whether the user is on leave
function isStaffOnLeave($staffProfile)
{
    $todayDate = now();
    $userLeaves = Leave::where('end', '>=', $todayDate)->where('personal_id', $staffProfile->personal_id)->with(['staffDelegated'])->get();
    if (count($userLeaves) > 0) {
        return false;
    } else {
        return true;
    }
}

function randomColor()
{
    $colors = [
        '#A3A1FB', '#DDDF00', '#24CBE5', '#64E572', '#FF9655',
        '#9DFCFF', '#FFD59D', '#FC46FC', '#00E5E5', '#E500B9',
        '#6CC476', '#E59F00'
    ];

    return $colors[array_rand($colors)];
}

function randomPassword($length)
{
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    for ($i = 0; $i < $length; $i++) {
        $n = rand(0, strlen($alphabet) - 1);
        $pass[$i] = $alphabet[$n];
    }
    return implode($pass);
}



function messageResponse($message, $status)
{
    return response()->json([
        'message' => $message,
        'status' => $status
    ], $status);
}

function datesPast($num)
{
    $dates = array();

    $month = date("m");
    $day = date("d");
    $year = date("Y");

    for ($i = 0; $i <= $num; $i++) {
        $dates[] = date('Y-m-d', mktime(0, 0, 0, $month, ($day - $i), $year));
    }
    return $dates;
}

function monthsPast($num)
{

    $months = array();

    $month = date("m");
    $year = date("Y");

    for ($i = 0; $i <= $num; $i++) {
        $months[] = date('Y-m-d', mktime(0, 0, 0, ($month - $i), 1, $year));
    }
    return $months;
}

function generateRandomString($length)
{
    $str = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $id = array();
    $strLength = strlen($str) - 1;
    for ($i = 0; $i < $length; $i++) {
        $p = mt_rand(0, $strLength);
        $id[] = $str[$p];
    }
    return implode($id);
}

function generateRandom($length)
{
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pieces = [];
    $max = \mb_strlen($chars, '8bit') - 1;
    for ($i = 0; $i < $length; $i++) {
        $pieces[] = $chars[random_int(0, $max)];
    }
    return \implode('', $pieces);
}

function apiResponse($results, $status = ResponseAlias::HTTP_OK)
{
    return response()->json([
        "results" => $results,
    ], $status);
}

function apiErrorResponse($message, $errors = [], $status = ResponseAlias::HTTP_BAD_REQUEST)
{
    return response()->json([
        "message" => $message,
        "errors" => $errors
    ], $status);
}

function getMailServerCredentials()
{
    try {
        $url = env("MAILER_URL") . "auth/user";

        $token = Cache::get("mail_token");
        if ($token === null) {
            $url = env("MAILER_URL") . "auth/login";
            $data = [
                "email" => env("MAIL_USERNAME"),
                "password" => env("MAILER_PASSWORD"),
            ];

            $response = Http::post($url, $data);

            if ($response->successful()) {
                $results = $response->json();
                $user = (object) $results['results']['user'];
                $mail_token = $user->api_token;
                Cache::put("mail_user", $user, now()->addMinutes(10));
                Cache::put("mail_token", $mail_token, now()->addMinutes(10));
            } else {
                Cache::put("mail_token", null, now()->addSeconds(120));
                Log::error("mail cred", ['details' => $response->messageResponse()]);
            }
        }
    } catch (\Throwable $th) {
        throw $th;
    }
}

function pushEmailsToMailServer(array $emailBody)
{
    try {
        $url = env("MAILER_URL") . "mail-logs/store";

        $data = [
            "subject" => $emailBody['subject'],
            "body" => $emailBody['body'],
            "to" => $emailBody['to'],
            "cc" => $emailBody['cc'],
            "bcc" => $emailBody['bcc'],
            "from" => $emailBody['from'],
            "from_name" => $emailBody['from_name'],
            "reply_to" => $emailBody['reply_to'],
            "server_name" => $emailBody['server_name'],
            "attachments" => $emailBody['attachments'],
            "headers" => $emailBody['headers'],
            "is_reminder" => $emailBody['is_reminder'],
            "channel" => $emailBody['channel'],
            "reference_code" => $emailBody['reference_code'],
        ];
        $token = Cache::get("mail_token");
        if ($token === null) {
            /**
             * @helper function uses cached token to
             */
            getMailServerCredentials();
            $token = Cache::get("mail_token");
        }
        $response = Http::withToken($token)->post($url, $data);

        if ($response->successful()) {
            return true;
        } else {
            getMailServerCredentials(); // in case this authentication issue
            // $token = Cache::get("mail_token");
            return abort(500);
        }
    } catch (\Throwable $th) {
        throw $th;
    }
}
