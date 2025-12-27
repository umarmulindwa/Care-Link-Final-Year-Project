<?php

/** @noinspection ALL */

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User;
use App\Http\Controllers\EmailsInboxController;
use App\Http\Controllers\StaffRegisterController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FaceFormController;
use App\Http\Controllers\SystemConfiguration;
use App\Http\Controllers\IncidenReporting;
use App\Http\Middleware\TeamsPermission;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\EquipmentRequest;
use App\Http\Controllers\MainSearchController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// })->name('welcome');

//making the login page the default page
// Route::redirect('/', '/login');

Route::get('/login', function () {
    return Inertia::render('Auth/Login', [
        "accountActive" => true
    ]);
})->name('login');

Route::get('/', function () {
    return to_route('login', [
        "accountActive" => true
    ]);
});

Route::post('/send-otp', [AuthController::class, 'sendOTP']);
Route::post('/confirm-identity-with-otp', [AuthController::class, 'confirmIdentityWithOtp']);

Route::get('/terms', function () {
    //authenticated user
    $user = auth()->user();
    return Inertia::render('TermsOfService', [
        'user' => $user,
    ]);
})->name('terms');

Route::get('/privacy', function () {
    //authenticated user
    $user = auth()->user();
    return Inertia::render('PrivacyPolicy', [
        'user' => $user,
    ]);
})->name('privacy');

/**
 * Support request initiation
 */
Route::get('/support', [SupportController::class, 'supportRequest'])->name('supportRequest');

/**
 *Travel Planner Reviews
 *
 */
Route::get('/tps-reviews/{code}', [\App\Http\Controllers\TravelPlanReviewsController::class, 'reviews'])->name('travelPlanReviews');



/**
 * Authenticated User Routes
 */
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $user->token = Session::getId();
        $userNow = User::find($user->id);
        if (!is_null($userNow)) {
            $userNow->token = $user->token;
            $userNow->save();
        }
        //check if user has a sanctum token
        $userApiToken = PersonalAccessToken::where('tokenable_id', $userNow->id)->first();
        //  if ($userApiToken == null) {
        // create a  token if the user does not have one already
        $userApiToken = $userNow->createToken($user->name)->plainTextToken;
        //save the api token on the user's table
        $currentUser = User::where('id', $user->id)->first();
        $currentUser->api_token =  $userApiToken;
        $currentUser->save();
        //  }
        //if user is a service provider na
        if ($user->user_type == 'sp' || $user->user_type == 'sp_driver') {

            //Service providers whose accounts have not been activated or disbled will not access the dashboard
            if ($user->status == 'pending' || $user->status == 'disabled') {

                DB::table('sessions')
                    ->whereUserId($currentUser->id)
                    ->delete();
                Route::post('logout');

                return to_route('login', [
                    "accountActive" => false
                ]);
            } else {
                return Inertia::render('ServiceProviderDashboard', [
                    'user' => $user,
                    // 'tkn' => $userApiToken->makeVisible('token')->toArray()
                ]);
            }
        }
        //staff that have just been onboarded through the staff onboarding system.
        elseif ($user->user_type == 'newstaff') {
            if ($user->status == 'active') {
                return Inertia::render('Dashboard', [
                    'user' => $user,
                ]);
            } else {
                //getting request details
                $requestToview = EquipmentRequest::where('email', auth()->user()->email)->with(['timeline' => function ($query) {
                    $query->latest()->first()->toArray();
                }, 'items', 'files' => function ($query) {
                    $query->latest()->first()->toArray();
                }])->first();

                return Inertia::render('NewOnboardedStaff/NewStaff', [
                    'user' => $user,
                    'request' => $requestToview,
                ]);
            }
        } else {
            // $userApiToken->assignAbility('can_assign');
            return Inertia::render('Dashboard', [
                'user' => $user,
            ]);
        }
    })->name('dashboard');





    //logging in as another user

    Route::post('/loginAs', [AuthController::class, 'loginAs'])->name('loginAs');




    //user's Inbox
    Route::get('/inbox', [EmailsInboxController::class, 'getUserEmails'])->name('myInbox');
    Route::get('/sent', [EmailsInboxController::class, 'getUserSentEmails'])->name('sent');
    Route::get('/outbox', [EmailsInboxController::class, 'getUserOutboxEmails'])->name('outbox');

    //search
    Route::get('/search', [MainSearchController::class, 'getSearchResults'])->name('search');



    // Email Logs
    Route::prefix('logs')->group(function () {
        Route::get('/administration', [EmailsInboxController::class, 'getAdminstrationLogs'])->name('AdminLogs');
        Route::get('/finance', [EmailsInboxController::class, 'getFinanceLogs'])->name('bscFinance');
        Route::get('/hr', [EmailsInboxController::class, 'getHrLogs'])->name('HrLogs');
        Route::get('/all', [EmailsInboxController::class, 'getAllLogs'])->name('AllLogs');
        Route::get('/supply', [EmailsInboxController::class, 'getSupplyLogs'])->name('SupplyLogs');
        Route::get('/ict', [EmailsInboxController::class, 'getICTLogs'])->name('ICTLogs');
        Route::get('/blacklist', [EmailsInboxController::class, 'getBlackListEmails'])->name('blacklist');
    });

    // Incident Reporting
    Route::get('/create-incident', [IncidenReporting::class, 'createIncident'])->name('createIncident');
    Route::get('/sp-register-signature', [IncidenReporting::class, 'registerSignature'])->name('sp-register-signature');
    Route::get(
        '/myIncidents',
        [IncidenReporting::class, 'index']
    )->name("myIncidents");
    Route::get('/review_incident', [IncidenReporting::class, 'reviewIncident'])->name('incidentReview');





    // Email Logs
    Route::prefix('logs')->group(function () {
        Route::get('/administration', [EmailsInboxController::class, 'getAdminstrationLogs'])->name('AdminLogs');
        Route::get('/finance', [EmailsInboxController::class, 'getFinanceLogs'])->name('bscFinance');
        Route::get('/hr', [EmailsInboxController::class, 'getHrLogs'])->name('HrLogs');
        Route::get('/all', [EmailsInboxController::class, 'getAllLogs'])->name('AllLogs');
        Route::get('/supply', [EmailsInboxController::class, 'getSupplyLogs'])->name('SupplyLogs');
        Route::get('/ict', [EmailsInboxController::class, 'getICTLogs'])->name('ICTLogs');
    });

    //staff regiter
    Route::get('/staff_register', [StaffRegisterController::class, 'getStaff'])->name('MainStaffRegister');
    Route::get('/staff_register_search', [StaffRegisterController::class, 'searchStaff'])->name('MainStaffRegisterSearch');
    Route::get('/unice_staff_profile', [StaffRegisterController::class, 'UnicefStaffProfile'])->name('UnicefProfileView');
    Route::get('/getSPIPTemplate', [StaffRegisterController::class, 'downloadSPUploadBatchFile'])->name('spBatchTemplate');



    //Support Center for super Admins
    Route::get('/supportCenter', [SupportController::class, 'supportCenter'])->name('supportCenter');

    //System configurations for super Admins
    Route::get('/system_congifuration', [SystemConfiguration::class, 'systemconfig'])->name('systemconfig');
    Route::get('/mobile-app', [SystemConfiguration::class, 'mobileApp'])->name('mobile-app');

    //dependants
    Route::post('/addDependant', [SystemConfiguration::class, 'addDependant']);
    Route::post('/addDependant', [SystemConfiguration::class, 'addDependant']);
    Route::get('/getLatestForexRates', [SystemConfiguration::class, 'getLatestForexRates']);
    Route::post('/removeDependant', [SystemConfiguration::class, 'removeDependant']);




    //BSC App Route
    Route::get('/bsc', function () {
        return Inertia::location(env('BSC_APP_URL'));
    })->name('bsc');

    //.......... Beneficiary Payments ...........
    Route::get('/beneficiary-payments', [\App\Http\Controllers\BeneficiaryController::class, 'uploadBeneficiaries'])->name('beneficiary-payments');
    Route::get('/beneficiary-payments/{id}', [\App\Http\Controllers\BeneficiaryController::class, 'ipViewBeneficiaries'])->name('beneficiary-payments-details');
    Route::get('/beneficiary-reconcile-payments', [\App\Http\Controllers\BeneficiaryController::class, 'reconcileBeneficiaries'])->name('beneficiary-reconcile-payments');
    Route::get('/beneficiary-reconcile-payments/{id}', [\App\Http\Controllers\BeneficiaryController::class, 'reviewBeconcileBeneficiaries'])->name('beneficiary-reconcile-review');
    Route::get('/beneficiary-attendance/{id}', [\App\Http\Controllers\BeneficiaryController::class, 'viewAttendance'])->name('view-beneficiary-attendance');





    //..............................................
    // Service Provier Routes
    //.....................................................
    Route::group(['prefix' => "sp"], function () {
        Route::get('create-invoice', [App\Http\Controllers\Web\Sp\InvoiceController::class, 'createInvoiceForm'])->name('sp.create-invoice');
        Route::get('invoices', [App\Http\Controllers\Web\Sp\InvoiceController::class, 'allInvoices'])->name('sp.invoices');
        Route::get('statistics', [App\Http\Controllers\Web\Sp\InvoiceController::class, 'statistics'])->name('sp.test-form');
        Route::get('invoice/{id}', [App\Http\Controllers\Web\Sp\InvoiceController::class, 'updateInvoice'])->name('sp.update-invoice');

        Route::group(['prefix' => 'quotations'], function () {
            Route::resource('/low-value-procurement', \App\Http\Controllers\SProviders\LowValueController::class);
        });

        Route::group(['prefix' => 'face-forms'], function () {
            Route::get("create", [FaceFormController::class, 'createFace'])->name('create-face');
            Route::get("list", [FaceFormController::class, 'listFace'])->name('list-faces');
            Route::get("reports", [FaceFormController::class, 'reportsFace'])->name('reports-face');
        });

        Route::group(['prefix' => 'vehicles'], function () {
            Route::resource('/vehicle-inspection', \App\Http\Controllers\SProviders\VehicleInspectionController::class);
            Route::resource('/vehicle-hire', \App\Http\Controllers\SProviders\VehicleHireController::class)->except(['show', 'create']);
            Route::get('/vehicle-hire/requests/{id}/{month}', [\App\Http\Controllers\SProviders\VehicleHireController::class, 'show'])->name('vehicle-hire.show');
            Route::get('/vehicle-hire/my-offfer/{trip_id}/{vhr_id}/{vehicle}', [\App\Http\Controllers\SProviders\VehicleHireController::class, 'create'])->name('vehicle-hire.create');
        });
    });


    //..............................................
    // Dashboards
    //.....................................................
    Route::get('system-dashboards', \App\Http\Controllers\SystemDashbooardsController::class)->name('dashboards');
});

Route::get('/test', function () {
    return Session::getId();
});

Route::get('schedules', function (Request $request) {


    try {

        sleep(10);
        $schedule = new Schedule();

        $schedule->command("schedule:run")->everyThreeHours();
    } catch (\Throwable $th) {
        //throw $th;
        Log::error("Scheduling schedule:run Failed: " . $th->getMessage());
    }


    Log::info("Scheduling passed");
});

Route::get('test-schedules', function (Request $request) {
    /**
     * this route is created to trigger scheduled tasks
     *
     */
    try {
        //code...
        sleep(10);
        $schedule = new Schedule();

        $now = Carbon::now();
        Artisan::call("send-test:mail 'web route scheduled test $now'");
        sleep(10);
        $schedule->command("send-test:mail 'every five minutes test $now'")->everyFiveMinutes();
    } catch (\Throwable $th) {
        //throw $th;
        Log::error('wget send-test:mail failed:' . $th->getMessage());
    }
});

Route::get('queue-schedules', function (Request $request) {


    try {
        set_time_limit(30);

        sleep(10);

        Artisan::call("queue:restart");
        Artisan::call("queue:work  --timeout=120 --sleep=300  --queue=emails,default,registerStaf --tries=2");

        // $schedule->command("queue:restart")->cron("*/10 * * * *");
        // $schedule->command("queue:work  --timeout=120 --sleep=300  --queue=emails,default,registerStaf --tries=1")->cron("*/2 * * * *");
    } catch (\Throwable $th) {

        Log::error('queue-schedules failed:' . $th->getMessage());
    }
});
