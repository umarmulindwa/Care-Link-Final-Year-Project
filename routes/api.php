<?php

use App\Http\Controllers\Api\MobileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\StaffRegisterController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\SystemConfiguration;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//api authentication
Route::prefix('auth-token')->group(function () {
    Route::post('login', [\App\Http\Controllers\Api\AuthTokenController::class, 'login']);
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('user', [\App\Http\Controllers\Api\AuthTokenController::class, 'getUser']);
        Route::post('logout', [\App\Http\Controllers\Api\AuthTokenController::class, 'loginOut']);
    });
});
Route::get('/returnFromModule', [AuthController::class, 'returnFromModule']);

//This is a test api route that returns the currently logged in user (use api_token in axios headers)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//submiting a support request doesnt need auth
Route::prefix('support')->group(function () {
    Route::post("/submitRequest", [SupportController::class, 'submitSupportRequest']);
});


Route::group(['namespace' => 'Api'], function () {


    Route::group(['middleware' => [
        'auth:sanctum'

    ]], function () {

        Route::prefix('staff-register')->group(function () {
            Route::get('/list-pa-staff',[StaffRegisterController::class, 'listPAs'])->name('staff.list-pas');
            Route::post('/password-reset',[StaffRegisterController::class, 'resetStaffMail']);
            Route::post("/addStaff", [StaffRegisterController::class, 'saveSingleStaff']);
            Route::post("/batchUploadStaff", [StaffRegisterController::class, 'batchStaffUpload']);
            Route::get("/loadAddStaffDetails", [StaffRegisterController::class, 'loadAddStaffDetails']);
            Route::post("/addUserRole", [StaffRegisterController::class, 'addUserRole']);
            Route::post("/deleteUserRole", [StaffRegisterController::class, 'deleteUserRole']);
            Route::post("/assignRolesPermissions", [StaffRegisterController::class, 'assignRolesPermissions']);
            Route::post("/revokePermision", [StaffRegisterController::class, 'revokePermission']);
            Route::post("/revokeRole", [StaffRegisterController::class, 'revokeRole']);
            Route::post("/saveUserSignature", [StaffRegisterController::class, 'saveUserSignature']);
            Route::get("/get-userSignature", [StaffRegisterController::class, 'getUserSignature']);
            Route::get("/getArchivedLeaves", [StaffRegisterController::class, 'getArchivedLeaves']);
            Route::post("/batchUploadLeave", [StaffRegisterController::class, 'batchUploadLeave']);
            Route::post("/delete-leave", [StaffRegisterController::class, 'deleteLeave']);
            Route::post("/registerServiceProvider", [StaffRegisterController::class, 'registerServiceProvider']);
            Route::post("/activateServiceProvider", [StaffRegisterController::class, 'activateServiceProvider']);
            Route::post("/deactivateServiceProvider", [StaffRegisterController::class, 'deactivateServiceProvider']);
            Route::get("/downloadSPUploadBatchFile", [StaffRegisterController::class, 'downloadSPUploadBatchFile']);
            Route::post("/batchUploadSP", [StaffRegisterController::class, 'batchSPUpload']);
            Route::post("/blacklistemail", [StaffRegisterController::class, 'blacklistEmail']);
            Route::post("/removeblacklistemail", [StaffRegisterController::class, 'removeblacklistemail']);
            Route::post("/editRole", [StaffRegisterController::class, 'editRole']);
        });

        Route::prefix('mobile-api')->group(function () {
            Route::post("/upload-apk", [MobileController::class,  'uploadApk']);
            Route::post("/delete-apk", [MobileController::class,  'deleteApk']);
            Route::get("/download-data", [MobileController::class,  'getDownloadData']);
            Route::get("/get-latest-download-data", [MobileController::class,  'getLatestDownloadData']);
            Route::get("/download-apk", [MobileController::class,  'downloadApk']);
        });

        Route::prefix('video')->group(function () {
            Route::post("/uploadSystemVideo", [SupportController::class, 'uploadSystemVideo']);
        });

        Route::prefix('support')->group(function () {
            Route::post("/submitResponse", [SupportController::class, 'submitSupportResponse']);
            Route::post("/closeSupportRequest", [SupportController::class, 'closeSupportRequest']);
        });

        Route::prefix('system-configuration')->group(function () {
            Route::get("/generalConfig", [SystemConfiguration::class, 'getGeneralConfiguration']);
            Route::post("/generalConfig", [SystemConfiguration::class, 'submitGeneralConfig']);
            Route::post("/addNewDistrict", [SystemConfiguration::class, 'addNewDistrict']);
            Route::post("/deleteDistrict", [SystemConfiguration::class, 'deleteDistrict']);
            Route::post("/addNewBank", [SystemConfiguration::class, 'addBank']);
            Route::post("/deleteBank", [SystemConfiguration::class, 'deleteBank']);
            Route::post("/deleteDonor", [SystemConfiguration::class, 'deleteDonor']);
            Route::post("/addNewDonor", [SystemConfiguration::class, 'addNewDonor']);
            Route::post("/addNewPillar", [SystemConfiguration::class, 'addNewPillar']);
            Route::post("/deletePillar", [SystemConfiguration::class, 'deletePillar']);
            Route::post("/addNewSection", [SystemConfiguration::class, 'addNewSection']);
            Route::post("/deleteSection", [SystemConfiguration::class, 'deleteSection']);
            Route::post("/addNewOrganisaitionUnit", [SystemConfiguration::class, 'addNewOrganisaitionUnit']);
            Route::post("/deleteOrganisationUnit", [SystemConfiguration::class, 'deleteOrganisationUnit']);
            Route::post("/addNewGrade", [SystemConfiguration::class, 'addNewGrade']);
            Route::post("/deleteGrade", [SystemConfiguration::class, 'deleteGrade']);
            Route::post("/addNewCategory", [SystemConfiguration::class, 'addNewCategory']);
            Route::post("/deleteCategory", [SystemConfiguration::class, 'deleteCategory']);
            Route::post("/addNewAppointmentType", [SystemConfiguration::class, 'addNewAppointmentType']);
            Route::post("/deleteAppointmentType", [SystemConfiguration::class, 'deleteAppointmentType']);
            Route::post("/addNewRoom", [SystemConfiguration::class, 'addNewRoom']);
            Route::post("/deleteRoom", [SystemConfiguration::class, 'deleteRoom']);
            Route::post("/addDollarRate", [SystemConfiguration::class, 'addDollarRate']);
            Route::post("/deleteRate", [SystemConfiguration::class, 'deleteRate']);

            Route::post('/addNewFieldOffice',[SystemConfiguration::class, 'addFieldOffice']);
            Route::post('/deleteFieldOffice',[SystemConfiguration::class, 'deleteFieldOffice']);
            Route::get("field-offices",[SystemConfiguration::class, 'getFieldOffices'])->name('field-offices');

        });
        Route::prefix('userdependant')->group(function () {
            Route::post("/add", [SystemConfiguration::class, 'addDependant']);
        });
    });
});

Route::get("/get-latest-download-data", [MobileController::class,  'getLatestDownloadData']);


Route::group(["prefix" => "serialize"], function () {
    Route::post("list", [App\Http\Controllers\Api\GuestController::class, 'serializeList'])->name('serialize.list');
    Route::post("object", [App\Http\Controllers\Api\GuestController::class, 'serializeObject'])->name('serialize.object');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::group(["prefix" => "sp"], function () {
        Route::post("test-submit-form", [App\Http\Controllers\Api\TestController::class, 'submitForm'])->name('test-submit-form');
        Route::post("submit-temp-files", [App\Http\Controllers\Api\TestController::class, 'submitFile'])->name('test-submit-form');
        Route::post("submit-temp-files-efris", [App\Http\Controllers\Api\TestController::class, 'submitEfrisForm'])->name('test-submit-files-efris');
        Route::post("delete-temp-files", [App\Http\Controllers\Api\TestController::class, 'removeFile'])->name('test-submit-form');
    });
});
