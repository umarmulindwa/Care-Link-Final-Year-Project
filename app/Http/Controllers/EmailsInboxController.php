<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\AdministrationEmailLogs;
use App\Models\FinanceEmailLog;
use App\Models\HrEmailLog;
use App\Models\ICTEmailLog;
use App\Models\MainEmailLog;
use App\Models\SupplyEmailLog;
use Illuminate\Http\Request;
use App\Models\IgnoredEmail;

//TODO: sort resposes

class EmailsInboxController extends Controller
{
    /**
     * This returns all emails that were sent to the currently logged in User, and their respective counts
     */
    public function getUserEmails()
    {
        //TODO:paginate emails page
        //Emails from Adminstration sent to current user 
        $userInboxAdmin = AdministrationEmailLogs::where('to', auth()->user()->email)->where('is_sent', 1)->get()->toArray();
        // $userInboxAdminCount = count($userInboxAdmin);

        //Emails from BSC sent to the current user
        $userInboxBsc = FinanceEmailLog::where('to', auth()->user()->email)->where('is_sent', 1)->get()->toArray();
        // $userInboxBscCount = count($userInboxBsc);

        //merging the emails and sorting
        $mergedArrays = array_merge($userInboxAdmin, $userInboxBsc);
        $mergedInboxEmails = collect($mergedArrays);
        $sortedEmails = $mergedInboxEmails->sortBy('created_at');
        $reversedEmails = $sortedEmails->reverse();
        $inboxEmails = $reversedEmails->values()->all();

        //outbox Emails
        // $userOutboxAdminCount = AdministrationEmailLogs::where('submitted_by', auth()->user()->name)->where('is_sent', 0)->count();
        // $userOutboxBscCount = FinanceEmailLog::where('submitted_by', auth()->user()->name)->where('is_sent', 0)->count();
        // //calculating total Emails outbox
        // $totalOutboxCount = $userOutboxAdminCount + $userOutboxBscCount;

        //calcutating total count
        // $totalInboxCount = $userInboxAdminCount + $userInboxBscCount;

        // //sent Emails
        // //Adminstration Sent Emails count
        // $userSentAdminCount = AdministrationEmailLogs::where('submitted_by', auth()->user()->name)->whereNot('to', auth()->user()->email)->where('is_sent', 1)->count();
        // //BSC Sent Emails count
        // $userSentBscCount = FinanceEmailLog::where('submitted_by', auth()->user()->name)->whereNot('to', auth()->user()->email)->where('is_sent', 1)->count();
        // //calculating total Emails sent
        // $totalSentCount = $userSentAdminCount + $userSentBscCount;

        //Logs
        // $allBscLogsCount = FinanceEmailLog::where('is_sent', 1)->latest()->get()->count();
        // $allAdminLogsCount = AdministrationEmailLogs::where('is_sent', 1)->latest()->get()->count();
        // $allFinanceLogsCount = FinanceEmailLog::where('is_sent', 1)->count();


        return Inertia::render('MyInbox/Inbox', [
            'myInbox' => $inboxEmails,
            "myInboxCount" => 0,
            "allBscLogsCount" => 0,
            'allFinanceCount' => 0,
            "allAdminLogsCount" => 0,
            "allSentCount" => 0,
            "allOutboxCount" => 0,
        ]);
    }

    public function getAllLogs()
    {
        $user = auth()->user();
        $userPermissions = $user->getAllPermissions();
        $isSuperAdmin = $userPermissions->contains(function ($item) {
            return $item->name == 's_admin';
        });
        $mailLogs = [];
        $adminLogs = [];
        $financeLogs =[];
        $hrLogs  = [];

        //TODO: Modify queries to use paginations
        //mainemaillogs
        if($isSuperAdmin){
            $mailLogs = MainEmailLog::orderBy('created_at', 'desc')->limit(250)->get()->toArray();
            $adminLogs = AdministrationEmailLogs::orderBy('created_at', 'desc')->limit(200)->get()->toArray();
            $financeLogs = FinanceEmailLog::orderBy('created_at', 'desc')->limit(200)->get()->toArray();
            $hrLogs = HrEmailLog::orderBy('created_at', 'desc')->limit(200)->get()->toArray();
        }else{
            $mailLogs = MainEmailLog::orderBy('created_at', 'desc')->limit(200)->where('to', auth()->user()->email)->orWhere('submitted_by', auth()->user()->name)->get()->toArray();
            $adminLogs = AdministrationEmailLogs::orderBy('created_at', 'desc')->limit(200)->where('submitted_by', auth()->user()->name)->orWhere('to', auth()->user()->email)->where('is_sent', 1)->get()->toArray();
            $financeLogs = FinanceEmailLog::orderBy('created_at', 'desc')->limit(200)->where('submitted_by', auth()->user()->name)->orWhere('to', auth()->user()->email)->where('is_sent', 1)->get()->toArray();
            $hrLogs  = HrEmailLog::orderBy('created_at', 'desc')->limit(200)->where('submitted_by', auth()->user()->name)->orwhere('to', auth()->user()->email)->where('is_sent', 1)->get()->toArray();
        }

        $mergedArrays = array_merge($mailLogs, $adminLogs, $financeLogs, $hrLogs);
        $mergedSentEmails = collect($mergedArrays);
        $sortedEmails = $mergedSentEmails->sortBy('created_at');
        $reversedEmails = $sortedEmails->reverse();
        $sortedMergedEmails = $reversedEmails->values()->all();

        return Inertia::render('MyInbox/Inbox', [
            "allEmails" => $sortedMergedEmails,
        ]);
    }

    /**
     * This returns all emails that were sent out by the currently logged in user and their respective counts
     */
    public function getUserSentEmails()
    {

        //sent Emails
        //Adminstration Emails sent by current user
        $userSentAdmin = AdministrationEmailLogs::where('submitted_by', auth()->user()->name)->whereNot('to', auth()->user()->email)->where('is_sent', 1)->get()->toArray();
        // $userSentAdminCount = count($userSentAdmin);

        //BSC Sent Emails sent by current user
        $userSentBsc = FinanceEmailLog::where('submitted_by', auth()->user()->name)->whereNot('to', auth()->user()->email)->where('is_sent', 1)->get()->toArray();
        // $userSentBscCount = count($userSentBsc);

        //HR Sent Emails sent by current user
        $userSentHr = HrEmailLog::where('submitted_by', auth()->user()->name)->whereNot('to', auth()->user()->email)->where('is_sent', 1)->get()->toArray();


        //merging the emails and sorting

        $mergedArrays = array_merge($userSentAdmin, $userSentBsc, $userSentHr);
        $mergedSentEmails = collect($mergedArrays);
        $sortedEmails = $mergedSentEmails->sortBy('created_at');
        $reversedEmails = $sortedEmails->reverse();
        $sentEmails = $reversedEmails->values()->all();

        //calculating total Emails sent
        // $totalSentCount = $userSentAdminCount + $userSentBscCount;

        return Inertia::render('MyInbox/Inbox', [
            "allSentCount" => 0,
            "sentEmails" => $sentEmails,
        ]);
    }

    /**
     * This returns all Emails that were sent out by the currently logged in user but they didnt reach their intended recipients,
     * and their respective counts
     */
    public function getUserOutboxEmails()
    {
        $user = auth()->user();
        $userPermissions = $user->getAllPermissions();
        $isSuperAdmin = $userPermissions->contains(function ($item) {
            return $item->name == 's_admin';
        });
        $mailLogs = [];

     
        //outbox emails from main email Logs
        //mainemaillogs
        if ($isSuperAdmin) {
            //outbox Administration Emails for current user
            $userOutboxAdmin = AdministrationEmailLogs::where('is_sent', 0)->latest()->get()->toArray();
            // $userOutboxAdminCount = count($userOutboxAdmin);

            //outbox BSC  Emails for current user
            $userOutboxBsc = FinanceEmailLog::where('is_sent', 0)->latest()->get()->toArray();
            // $userOutboxBscCount = count($userOutboxBsc);

            $userOutboxHr = HrEmailLog::where('is_sent', 0)->latest()->get()->toArray();

            $mailLogs = MainEmailLog::where('is_sent', 0)->get()->toArray();
        } else {
            //outbox Administration Emails for current user
            $userOutboxAdmin = AdministrationEmailLogs::where('submitted_by', auth()->user()->name)->where('is_sent', 0)->latest()->get()->toArray();
            // $userOutboxAdminCount = count($userOutboxAdmin);

            //outbox BSC  Emails for current user
            $userOutboxBsc = FinanceEmailLog::where('submitted_by', auth()->user()->name)->where('is_sent', 0)->latest()->get()->toArray();
            // $userOutboxBscCount = count($userOutboxBsc);

            $userOutboxHr = HrEmailLog::where('submitted_by', auth()->user()->name)->where('is_sent', 0)->latest()->get()->toArray();

            $mailLogs = MainEmailLog::where('submitted_by', auth()->user()->name)->where('is_sent', 0)->get()->toArray();
        }
        //merging the emails and sorting

        $mergedArrays = array_merge($userOutboxAdmin, $userOutboxBsc, $userOutboxHr, $mailLogs);
        $mergedOutboxEmails = collect($mergedArrays);
        $sortedEmails = $mergedOutboxEmails->sortBy('created_at');
        $reversedEmails = $sortedEmails->reverse();
        $outboxEmails = $reversedEmails->values()->all();

        //calculating total Emails outbox
        // $totalOutboxCount = $userOutboxAdminCount + $userOutboxBscCount;

        return Inertia::render('MyInbox/Inbox', [
            "allOutboxCount" => 0,
            "outboxEmails" => $outboxEmails,
        ]);
    }

    /**
     * This returns all emails from Administration and their respective counts
     */
    public function getAdminstrationLogs()
    {
        //TODO: paginate logs
        $allAdminLogs = AdministrationEmailLogs::where('is_sent', 1)->latest()->get();
        $allAdminLogsCount = count($allAdminLogs);

        return Inertia::render('MyInbox/Inbox', [
            'AdminLogs' => $allAdminLogs,
            "allAdminLogsCount" => $allAdminLogsCount,

        ]);
    }
    /**
     * This returns all emails from BSC and their respective counts
     */
    public function getFinanceLogs()
    {
        //TODO: paginate logs
        $allBscLogs = FinanceEmailLog::where('is_sent', 1)->latest()->get();
        $allBscLogsCount = count($allBscLogs);

        return Inertia::render('MyInbox/Inbox', [
            'bsclogs' => $allBscLogs,
            "allBscLogsCount" => $allBscLogsCount,
        ]);
    }

    /**
     * This returns all emails form HR and their respective counts
     */

    public function getHrLogs()
    {
        $allHrLogs = HrEmailLog::where('is_sent', 1)->latest()->get();

        return Inertia::render('MyInbox/Inbox', [
            'hrlogs' => $allHrLogs,
        ]);
    }

    /**
     * This returns all emails from BSC and their respective counts
     */
    public function getSupplyLogs()
    {
        //TODO: paginate logs
        $allSupplyLogs = SupplyEmailLog::where('is_sent', 1)->latest()->get();
        $allSupplyLogsCount = count($allSupplyLogs);

        return Inertia::render('MyInbox/Inbox', [
            'supplyLogs' => $allSupplyLogs,
            "supplyLogsCount" => $allSupplyLogsCount,
        ]);
    }

    /**
     * This returns all emails from BSC and their respective counts
     */
    public function getICTLogs()
    {
        //TODO: paginate logs
        $allICTLogs = ICTEmailLog::where('is_sent', 1)->latest()->get();
        $allICTLogsCount = count($allICTLogs);

        return Inertia::render('MyInbox/Inbox', [
            'ictLogs' => $allICTLogs,
            "ictLogsCount" => $allICTLogsCount,
        ]);
    }

    //disabled emails
    public function getBlackListEmails(){
        $user = auth()->user();
        $allBlacklistedEmails = [];
        $userPermissions = $user->getAllPermissions();
        $isSuperAdmin = $userPermissions->contains(function ($item) {
            return $item->name == 's_admin';
        });
        //blacklisted Emails
        $myBlackListedEmails = IgnoredEmail::where('email', auth()->user()->email)->where('is_deactivated', true)->latest()->get();
        if ($isSuperAdmin) {
            $allBlacklistedEmails = IgnoredEmail::where('is_deactivated', true)->latest()->get();
        }

        return Inertia::render(
            'MyInbox/Inbox',
            [
                'myBlackListedEmails' => $myBlackListedEmails,
                'allBlacklistedEmails' => $allBlacklistedEmails
            ]
        );
    }
}
