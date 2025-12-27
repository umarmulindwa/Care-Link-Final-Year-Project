<?php

namespace App\Http\Controllers;

use App\Models\AirtimeData\StaffCategory;
use App\Models\DollarRate;
use App\Models\Grade;
use App\Models\StaffIdentity;
use Inertia\Inertia;
use App\Models\StaffProfile;
use App\Models\ServiceProvider;
use App\Actions\Fortify\CreateNewUser;
use Helpers;
use Illuminate\Http\Request;
use App\Mail\TemporaryPasswordNewAccountMail;
use App\Mail\UserPasswordResetAccountMail;
use App\Models\AppointmentType;
use App\Models\RoleDescription;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Invoice;
use App\Models\BscRequests;
use App\Models\Category;
use App\Models\Section;
use App\Models\User;
use App\Models\UserSignature;
use App\Models\Leave;
use Carbon\Carbon;
use App\Models\LeaveDelegation;
use App\Models\LeaveRoles;
use App\Models\LeaveSchedule;
use App\Models\OrganisationUnit;
use App\Models\Pillar;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission as ModelsPermission;
use Illuminate\Support\Facades\Log;
use App\Models\IgnoredEmail;


class StaffRegisterController extends Controller
{


    public function getStaff(Request $request)
    {

        //getting staffprofile details
        // $staffDetails = StaffProfile::select('id', 'personal_id', 'name', 'position_text','email', 'section')->with(['user'])->filter('seachFor', 'seachWithIn')->paginate(20)->withQueryString();

        //getting service providers
        $serviceProviders = ServiceProvider::select('id', 'name', 'phone', 'contractor_number', 'vendor_number', 'email')->with(['user'])->paginate(20);

        //all Staffnames
        $allStaffNames = StaffProfile::select('id', 'email', 'name', 'section')->latest()->get();

        //getting user roles
        $userRoles = Role::with('permissions')->latest()->get();

        //users with roles
        // $usersWithRoles =  Role::all();

        //seeded permissions
        $seededPermissions = Permission::select('name')->get();

        //upcoming leaves
        $todayDate = now();
        $upcomingLeaves = Leave::where('start', '>', $todayDate)->where('end', '>=', $todayDate)->with(['staffDelegated'])->latest()->get();
        $ongoingLeaves = Leave::where('end', '>=', $todayDate)->where('start', '<=', $todayDate)->with(['staffDelegated'])->latest()->get();

        $searchWithIn =  $request->input('seachWithIn');
        $seachPerms =  $request->input('seachFor');



        return Inertia::render(
            'StaffRegister/MainStaffRegister',
            [
                'unicefStaff' => StaffProfile::query()
                ->orWhereHas('user',  function ($query) use ( $seachPerms) {
                    $query->whereHas('permissions', function ($query2)  use ( $seachPerms) {
                        $query2->where('name', "like", "%$seachPerms%");
                    })->orWhereHas('roles.permissions', function ($query3)  use ( $seachPerms) {
                        $query3->where('name', "like", "%$seachPerms%");
                    });
                })
                    ->with(['user', 'staffIdentity'])
                    ->when($request->input('seachFor'), function ($query, $seachFor) use ($searchWithIn) {
                        switch ($searchWithIn) {
                                //TODO: implement the filters for different cases
                            case 'All':
                                $query->where('name', 'like', '%' . $seachFor . '%')
                                    ->OrWhere('email', 'like', '%' . $seachFor . '%');
                                break;
                            case 'Name':
                                $query->where('name', 'like', '%' . $seachFor . '%');
                                break;
                            case 'Section':
                                $query->where('name', 'like', '%' . $seachFor . '%')
                                    ->OrWhere('email', 'like', '%' . $seachFor . '%');
                                break;
                            case 'Roles':

                                break;
                            case 'Permissions':


                                break;
                            case 'Availability':
                                $query->where('name', 'like', '%' . $seachFor . '%')
                                    ->OrWhere('email', 'like', '%' . $seachFor . '%');
                                break;
                        }
                    })->orderBy('section', 'asc')->orderBy('name', 'asc')->paginate(20)
                    ->withQueryString(),
                'filters' => $request->only(['seachFor']),

                'serviceProviders' => $serviceProviders,
                'userRoles' => $userRoles,
                'allStaffNames' => $allStaffNames,
                'seededPermissions' => $seededPermissions,
                'upcomingLeaves' => $upcomingLeaves,
                'ongoingLeaves' => $ongoingLeaves,
                // '$usersWithRoles' => $usersWithRoles
            ]
        );
    }

    public function listPAs(Request $request)
    {
        try {
            $q = $request->query("q");
            $data = User::whereHas('permissions', function ($query) {
                $query->where('name', "like", "pa_%");
            })->orWhereHas('roles.permissions', function ($query) {
                $query->where('name', "like", "pa_%");
            })->whereAny(['name', 'email'], 'like', "%$q%")->get();
            return apiResponse(compact('data'));
        } catch (\Throwable $th) {
            return apiErrorResponse("Error List:: " . $th->getMessage(), ['error' => $th->getTrace()]);
        }
    }
    // public function searchStaff(Request $request){
    //     $request->validate([
    //         'seachFor' => 'required|string',
    //         'seachWithIn' => 'required|string',

    //     ]);

    //     switch ($request->query('seachWithIn')) {
    //         case 'All':
    //             return Inertia::render(
    //                 'StaffRegister/MainStaffRegister',
    //                 [
    //                     'unicefStaffSearch' => StaffProfile::query()
    //                     ->when($request->query('seachFor'), function ($query, $seachFor) {

    //                         $query->where('name', 'like', '%' . $seachFor . '%')
    //                             ->OrWhere('email', 'like', '%' . $seachFor . '%');
    //                     })->paginate(20)
    //                     ->withQueryString(),

    //                 ]
    //             );
    //             break;
    //         case 'Name':
    //             echo "i equals 1";
    //             break;
    //         case 'Section':
    //             echo "i equals 2";
    //             break;
    //         case 'Role':
    //             echo "i equals 2";
    //             break;
    //         case 'Availability':
    //             echo "i equals 2";
    //             break;
    //     }

    // }

    public function UnicefStaffProfile(Request $request)
    {

        //setting staff Details
        $staffDetails = StaffProfile::where('id', $request->staffProfileID)->with(['user', 'organisationUnit'])->first();

        //user invoices
        $userInvoices = Invoice::select('status', 'ip_profile_id', 'service_provider_id', 'invoice_number', 'service_name', 'invoice_currency', 'invoice_value_amount', 'tourism_levy_amount', 'exise_duty_amount', 'other_taxes_amount', 'vat_amount')->where('assigned_staff', auth()->user()->email)->orWhere('invoice_manager', auth()->user()->email)->latest()->limit(7)->get();

        //use bsc requests
        $userbscRequests = BscRequests::where('assigned_staff', auth()->user()->email)->orWhere('requests_manager', auth()->user()->email)->latest()->limit(7)->get();


        return Inertia::render(
            'StaffRegister/UnicefProfileView',
            [
                'staffDetails' => $staffDetails,
                'invoices' => $userInvoices,
                'userbscRequests' => $userbscRequests,
            ]
        );
    }

    public function resetStaffMail(Request $request)
    {
        try {
            $request->validate([
                'staff_id' => 'required'
            ]);
            $data = $request->all();
            $staff = StaffProfile::find($data['staff_id']);
            if (is_null($staff)) {
                return apiErrorResponse('Staff not found');
            }
            if ($staff->email == null || $staff->email == '') {
                return apiErrorResponse('The staff doesn\'t have an email address and a user account yet');
            }
            $password = "unicef#" . mt_rand(1000, 9999);
            $user = User::updateOrCreate([
                "email" => $staff->email,
            ], [
                "name" => $staff->name,
                "user_type" => "unicef",
                "password" => bcrypt($password)
            ]);
            //notify user
            // $userFcm = UserFcmToken::where("user_id", $user->id)->first();
            // if ($userFcm) {
            //     sendNotification($userFcm->token, "Your password has been updated");
            // }
            $mail = new UserPasswordResetAccountMail($staff->name, $user->email, $password);
            globalSendEmail($staff, 'Your Password has been reset', $mail, null, false, 'SSD-PWD-RESET');

            return apiResponse('SUCCESS');
        } catch (\Throwable $th) {
            //throw $th;
            return apiErrorResponse('Reset Password: ' . $th->getMessage(), ['error' => $th->getMessage()]);
        }
    }

    public function saveSingleStaff(Request $request)
    {
        //update the staffProfile
        $staffId = $request->has('id') ? $request->input('id') : null;

        if (!$staffId) {
            $user_exists = User::query()->where('email', $request->input('email'))->exists();

            if ($user_exists) {
                return response()->json(['User with this email exists']);
            }
        }

        $staff = StaffProfile::query()->updateOrCreate(["id" => $staffId], $request->all());

        //fill identity
        $staffIdentity = $staff->staffIdentity;

        if ($staffIdentity) {
            $staffIdentity->update($request->input('identity'));
        } else {
            $staffIdentity = new StaffIdentity($request->input('identity'));
            $staff->staffIdentity()->save($staffIdentity);
        }

        $this->saveIdentityProof($request, $staffIdentity, $staff->id);

        if ($staffId) {
            if ($request->has('photo')) {
                $staff->user->updateProfilePhoto($request->file('photo'));
            }
            return response()->json('success');
        }

        //adding staff to the users' table and generating a random password
        $randomPassword = "unicef#" . mt_rand(1000, 9999);

        $createUserObj = new CreateNewUser;

        $createUserObj->create([
            'name' => $staff->name,
            'email' => $staff->email,
            'password' => $randomPassword,
            'password_confirmation' => $randomPassword,
            'terms' => true,
            'newStaff' => true,
        ]);

        $userAccount = User::where('email', $staff->email)->first();
        $userAccount->user_type = 'unicef';
        $userAccount->password_expires_at = Carbon::now()->addDays(2);
        $userAccount->save();

        //send email to account created with temporary password
        try {
            $subject =  "Your account has been created.";
            $mail = new TemporaryPasswordNewAccountMail($staff->name, $randomPassword, $staff->email);
            $staff = StaffProfile::where('email', $staff->email)->first();
            globalSendEmail($staff, $subject, $mail, null, false, null, [
                "personal_id" => $staff->email,
                "action" => env('APP_URL'),
                "description" => "Your account has been created you can proceed with using the platform",
                "submitted_by" => "Platform",
                "title" => $subject,
                "profile_photo_path" => null
            ]);
        } catch (\Exception $ex) {
        }

        return response()->json(['success' => 200, 'newStaff' => $staff]);
    }

    public function saveIdentityProof(Request $request, $staffIdentity, $staffId): void
    {
        $front_un_id = $request->file('identity.front_un_id_proof');
        $front_diplomatic = $request->file('identity.front_diplomatic_passport_proof');
        $front_national = $request->file('identity.front_national_passport_proof');
        $front_visa = $request->file('identity.front_visa_proof');

        $back_un_id = $request->file('identity.back_un_id_proof');
        $back_diplomatic = $request->file('identity.back_diplomatic_passport_proof');
        $back_national = $request->file('identity.back_national_passport_proof');
        $back_visa = $request->file('identity.back_visa_proof');

        if ($front_un_id) {
            $previous = $staffIdentity->staff_files()->where('file_name', 'front un id')->first();
            if ($previous) {
                Storage::delete(str_replace('storage', 'public', $previous->file_path));
                $previous->delete();
            }

            $proof = $front_un_id->store('/public/identities');
            $staffIdentity->staff_files()->create([
                'staff_id' => $staffId,
                'file_name' => 'front un id',
                'file_path' => str_replace('public', '/storage', $proof),
                'file_type' => $front_un_id->getMimeType(),
                'file_size' => $front_un_id->getSize(),
            ]);
        }

        if ($back_un_id) {
            $previous = $staffIdentity->staff_files()->where('file_name', 'back un id')->first();
            if ($previous) {
                Storage::delete(str_replace('storage', 'public', $previous->file_path));
                $previous->delete();
            }

            $proof = $back_un_id->store('/public/identities');
            $staffIdentity->staff_files()->create([
                'staff_id' => $staffId,
                'file_name' => 'back un id',
                'file_path' => str_replace('public', '/storage', $proof),
                'file_type' => $back_un_id->getMimeType(),
                'file_size' => $back_un_id->getSize(),
            ]);
        }

        if ($front_diplomatic) {
            $previous = $staffIdentity->staff_files()->where('file_name', 'front diplomatic')->first();
            if ($previous) {
                Storage::delete(str_replace('storage', 'public', $previous->file_path));
                $previous->delete();
            }

            $proof = $front_diplomatic->store('/public/identities');
            $staffIdentity->staff_files()->create([
                'staff_id' => $staffId,
                'file_name' => 'front diplomatic',
                'file_path' => str_replace('public', '/storage', $proof),
                'file_type' => $front_diplomatic->getMimeType(),
                'file_size' => $front_diplomatic->getSize(),
            ]);
        }

        if ($back_diplomatic) {
            $previous = $staffIdentity->staff_files()->where('file_name', 'back diplomatic')->first();
            if ($previous) {
                Storage::delete(str_replace('storage', 'public', $previous->file_path));
                $previous->delete();
            }

            $proof = $back_diplomatic->store('/public/identities');
            $staffIdentity->staff_files()->create([
                'staff_id' => $staffId,
                'file_name' => 'back diplomatic',
                'file_path' => str_replace('public', '/storage', $proof),
                'file_type' => $back_diplomatic->getMimeType(),
                'file_size' => $back_diplomatic->getSize(),
            ]);
        }

        if ($front_national) {
            $previous = $staffIdentity->staff_files()->where('file_name', 'front national')->first();
            if ($previous) {
                Storage::delete(str_replace('storage', 'public', $previous->file_path));
                $previous->delete();
            }

            $proof = $front_national->store('/public/identities');
            $staffIdentity->staff_files()->create([
                'staff_id' => $staffId,
                'file_name' => 'front national',
                'file_path' => str_replace('public', '/storage', $proof),
                'file_type' => $front_national->getMimeType(),
                'file_size' => $front_national->getSize(),
            ]);
        }

        if ($back_national) {
            $previous = $staffIdentity->staff_files()->where('file_name', 'back national')->first();
            if ($previous) {
                Storage::delete(str_replace('storage', 'public', $previous->file_path));
                $previous->delete();
            }

            $proof = $back_national->store('/public/identities');
            $staffIdentity->staff_files()->create([
                'staff_id' => $staffId,
                'file_name' => 'back national',
                'file_path' => str_replace('public', '/storage', $proof),
                'file_type' => $back_national->getMimeType(),
                'file_size' => $back_national->getSize(),
            ]);
        }

        if ($front_visa) {
            $previous = $staffIdentity->staff_files()->where('file_name', 'front visa')->first();
            if ($previous) {
                Storage::delete(str_replace('storage', 'public', $previous->file_path));
                $previous->delete();
            }

            $proof = $front_visa->store('/public/identities');
            $staffIdentity->staff_files()->create([
                'staff_id' => $staffId,
                'file_name' => 'front visa',
                'file_path' => str_replace('public', '/storage', $proof),
                'file_type' => $front_visa->getMimeType(),
                'file_size' => $front_visa->getSize(),
            ]);
        }

        if ($back_visa) {
            $previous = $staffIdentity->staff_files()->where('file_name', 'back visa')->first();
            if ($previous) {
                Storage::delete(str_replace('storage', 'public', $previous->file_path));
                $previous->delete();
            }


            $proof = $back_visa->store('/public/identities');
            $staffIdentity->staff_files()->create([
                'staff_id' => $staffId,
                'file_name' => 'back visa',
                'file_path' => str_replace('public', '/storage', $proof),
                'file_type' => $back_visa->getMimeType(),
                'file_size' => $back_visa->getSize(),
            ]);
        }
    }

    public function insertParts(): array
    {
        $organizationUnit = function ($unit) {
            if (!$unit) return null;
            return OrganisationUnit::query()->updateOrCreate(['name' => $unit]);
        };

        $pillar = function ($pillar) {
            if (!$pillar) return null;
            return Pillar::query()->updateOrCreate(['name' => $pillar]);
        };

        $grade = function ($grade) {
            if (!$grade) return null;
            return  Grade::query()->updateOrCreate(['name' => $grade]);
        };

        $categories = function ($category) {
            if (!$category) return null;
            return  Category::query()->updateOrCreate(['name' => $category]);
        };

        $section = function ($section) {
            if (!$section) return null;
            return Section::query()->updateOrCreate(['name' => $section]);
        };

        $appointmentType = function ($type) {
            if (!$type) return null;
            return AppointmentType::query()->updateOrCreate(['name' => $type]);
        };

        return [
            'organizationUnit' => $organizationUnit,
            'pillar' => $pillar,
            'grade' => $grade,
            'categories' => $categories,
            'section' => $section,
            'appointmentType' => $appointmentType,
        ];
    }

    public function batchStaffUpload(Request $request)
    {
        set_time_limit(0);

        $data = $request->dataArray;
        $newStaff = [];

        $parts = $this->insertParts();

        foreach ($data as $staff) {

            $email =  $staff['Email'];
            if (!preg_match("/^\S+@\S+\.\S+$/", $email)) {
                continue;
            }

            $newStaff[] = $newUser = StaffProfile::query()->updateOrCreate(["email" =>  $staff['Email'],], [
                "personal_id" => $staff['Personnel ID'] ?? null,
                "index_number" => $staff['Index Number'] ?? null,
                "name" =>  isset($staff['Names']) ? iconv('UTF-8', 'UTF-8//IGNORE', $staff['Names']) : null,
                "email" => $staff['Email'] ?? null,
                "staff_type" => $staff['Staff Type'] ?? null,
                "position_text" => $staff['Staff Title'] ?? 'Temporary Appt.',
                "position_id" => $staff['Position ID'] ?? null,
                "section" => $parts['section']($staff['Section'] ?? null)?->id ?? null,
                "organisation_unit" => $parts['organizationUnit']($staff['Organization Unit'] ?? null)?->id ?? null,
                "reports_to" => $staff['Reports to'] ?? null,
                "category" => $staff['Category'] ?? null,
                "categories" => $parts['categories']($staff['Categories'] ?? null)?->id ?? null,
                "gender" => $staff['Gender'] ?? null,
                "appointment_type" => $parts['appointmentType']($staff['Appointment Type'] ?? null)?->id ?? null,
                "date_began_working_with_unicef" => $staff['Date began with UNICEF (as a whole)'] ?? null,
                "date_began_working_with_unicef_country_level" => $staff['Date began with UNICEF SSD'] ?? null,
                "date_contract_end" => $staff['Date contract with UNICEF SSD ends'] ?? null,
                "is_imported" => true,
                "marital_status" => $staff['MaritalStatus'] ?? null,
                "phone" => $staff['Phone'] ?? null,
                "phone_whatsapp" => $staff['Whatsapp'] ?? null,
                "address" => $staff['Address'] ?? null,
                "pillar" => $parts['pillar']($staff['Pillar'] ?? null)->id ?? null,
                "grade" => $parts['grade']($staff['Grade'] ?? null)?->id ?? null,
                "allocated_phone_number" => $staff['Allocated Phone Number'] ?? null,
            ]);

            //adding staff to the users' table and generating a random password
            $user_exists = User::query()->where('email', $newUser->email)->exists();
            if ($user_exists) continue;

            $randomPassword = "unicef#" . mt_rand(1000, 9999);
            $createUserObj = new CreateNewUser;

            $createUserObj->create([
                'name' => $newUser->name,
                'email' => $newUser->email,
                'password' => $randomPassword,
                'password_confirmation' => $randomPassword,
                'terms' => true,
                'newStaff' => true,
            ]);

            $userAccount = User::where('email', $newUser->email)->first();
            $userAccount->email_verified_at = date("Y-m-d H:i:s");
            $userAccount->user_type = 'unicef';
            $userAccount->password_expires_at = Carbon::now()->addDays(2);
            $userAccount->save();

            //send email to account created with temporary password

            try {
                $subject =  "Your account has been created.";
                $mail = new TemporaryPasswordNewAccountMail($newUser->name, $randomPassword, $newUser->email);
                $staff = StaffProfile::where('email', $newUser->email)->first();
                globalSendEmail($staff, $subject, $mail, null, false, null, [
                    "personal_id" => $staff->email,
                    "action" => env('APP_URL'),
                    "description" => "Your account has been created you can proceed with using the platform",
                    "submitted_by" => "Platform",
                    "title" => $subject,
                    "profile_photo_path" => null
                ]);
            } catch (\Exception $ex) {
            }
        }
        return response()->json(['success' => 200, 'newStaff' => $newStaff]);
    }


    public function addUserRole(Request $request)
    {

        $roleData = $request->roleData;
        $roleId = array_key_exists('id', $roleData) ? $roleData['id'] : null;
        $roleDescId = array_key_exists('role_descriptionId', $roleData) ? $roleData['role_descriptionId'] : null;


        //creating role
        $role = Role::create(['guard_name' => 'web', 'name' => $roleData['roleName'], 'description' => $roleData['roleDescription'], 'created_by' => auth()->user()->name]);
        // //assigning permissions to role
        foreach ($roleData['rolePermissions'] as $rolePermission) {
            $role->givePermissionTo($rolePermission);
        }
        //update role description
        // if ($roleDescId == null) {
        //     RoleDescription::create([
        //         'role_id' => $role->id,
        //         'description' => $roleData['roleDescription'],
        //         'created_by' => auth()->user()->name,
        //     ]);
        // }


        return apiResponse('success');
    }
    public function assignRolesPermissions(Request $request)
    {
        $rolesPermissions = $request->rolesPermissions;

        foreach ($rolesPermissions['staff'] as $selectedStaff) {

            $staffProfileEmail = StaffProfile::where('id', $selectedStaff['code'])->first();

            $user = User::where('email', $staffProfileEmail->email)->first();

            //getting email username
            $markCenter = strpos($staffProfileEmail->email, '@');
            $emailUsername = substr($staffProfileEmail->email, 0, $markCenter);
            //assigning roles to the user
            if (array_key_exists('roles', $rolesPermissions)) {
                foreach ($rolesPermissions['roles'] as $role) {
                    $user->assignRole($role);

                    //if it is a finance permission assign a user another permission to manage finance requests
                    $roleDef = Role::where('name', $role)->first();
                    if ($roleDef->hasPermissionTo('finance_staff')) {
                        Permission::updateOrCreate(['name' => 'fnc_' . $emailUsername], ['name' => 'fnc_' . $emailUsername, 'guard_name' => 'web']);
                        $user->givePermissionTo('fnc_' . $emailUsername);
                    }
                }
            }

            //assigning permissions
            if (array_key_exists('permissions', $rolesPermissions)) {
                foreach ($rolesPermissions['permissions'] as $permission) {
                    $user->givePermissionTo($permission);

                    //if it is a finance permission assign a user another permission to manage finance requests
                    if ($permission == 'finance_staff') {
                        Permission::updateOrCreate(['name' => 'fnc_' . $emailUsername], ['name' => 'fnc_' . $emailUsername, 'guard_name' => 'web']);
                        $user->givePermissionTo('fnc_' . $emailUsername);
                    }
                }
            }
        }

        return apiResponse('success');
    }

    public function revokePermission(Request $request)
    {

        $user = User::where('email', $request->staffEmail)->first();
        $user->revokePermissionTo($request->permission);

        return apiResponse('success');
    }
    public function revokeRole(Request $request)
    {

        $user = User::where('email', $request->staffEmail)->first();
        $user->removeRole($request->role);

        return apiResponse('success');
    }
    public function deleteUserRole(Request $request)
    {
        $role = Role::findOrFail($request->roleId);
        $role->delete();

        return apiResponse('Role Deleted');
    }

    public function saveUserSignature(Request $request)
    {

        $request->validate([
            'signature' => ['required'],
            'userId' => 'required',
        ]);

        UserSignature::updateOrCreate(['user_id' => $request->userId], [
            "image" => $request->signature,
            'user_id' => $request->userId
        ]);
        return apiResponse('Success');
    }

    public function getUserSignature()
    {
        $usersignature = UserSignature::select('image')->where('user_id', auth()->user()->id)->first();
        return apiResponse($usersignature);
    }
    public function getArchivedLeaves()
    {
        $todayDate = now();
        $archivedLeaves = Leave::where('end', '<=', $todayDate)->where('start', '<=', $todayDate)->with(['staffDelegated'])->latest()->get();
        return apiResponse($archivedLeaves);
    }
    public function batchUploadLeave(Request $request)
    {

        //TODO

        foreach ($request->dataArray as $leaveSchedule) {

            $currentStaffProfile = StaffProfile::where('email', $leaveSchedule["Employee email"])->first();
            $delegateStaffProfile = StaffProfile::where('email', $leaveSchedule["Officer In Charge Email"])->first();
            $todayDate = date('Y-m-d', strtotime('today'));
            $startDate = date('Y-m-d', strtotime($leaveSchedule["Leave Starts"]));
            $isStartDateInPast = strtotime($leaveSchedule["Leave Starts"]) < strtotime(date('Y-m-d'));



            $leave = Leave::create(
                [
                    "leaving_staff" => $currentStaffProfile->email,
                    "name" => $leaveSchedule["Employee name"],
                    "start" => $leaveSchedule["Leave Starts"],
                    "end" => $leaveSchedule["Leave Ends"],
                    "status" => 'approved',
                    "reason_for_leave" => "",
                    "delegated_to" => $leaveSchedule["Officer In Charge Email"],
                    "delegated_to_name" => $leaveSchedule["Officer In Charge Name"]
                ]
            );

            LeaveSchedule::create(
                [
                    "leaving_staff" => $currentStaffProfile->email,
                    "start" => $leaveSchedule["Leave Starts"],
                    "end" => $leaveSchedule["Leave Ends"],
                    "type_Vacation" => "",
                    "delegateto" => $leaveSchedule["Officer In Charge Email"],
                    "leave_id" => $leave->id,
                ]
            );
            LeaveDelegation::create(
                [
                    "leave_id" => $leave->id,
                    "delegated_role_to" => $leave->delegated_to,
                    "status" => 'approved',
                ]
            );


            /*
         * Assign roles and permissions of the staff going on leave to the delegated staff
         * (if the leave starts today/past. future leaves will are catered for by a background process)
         */
            if ($isStartDateInPast || ($todayDate == $startDate)) {
                //get the roles and permissions of the staff going on leave

                $user = User::where('email', $currentStaffProfile->email)->first();
                $delegatedUser = User::where('email', $delegateStaffProfile->email)->first();

                //roles & Permissions for staff going on leave
                $permissionNames = $user->getPermissionNames()->toArray();
                $roles = $user->getRoleNames()->toArray();

                //roles & Permissions for delegated staff
                $delegatedUserRoles = $delegatedUser->getRoleNames()->toArray();
                $delegatedUserPermissionNames = $delegatedUser->getPermissionNames()->toArray();


                //assigning roles to the user
                foreach ($roles as $role) {
                    // assign the delegated staff the role if they do not have it already
                    if (!in_array($role, $delegatedUserRoles)) {
                        $delegatedUser->assignRole($role);
                        //update leave roles
                        $curentRole = Role::where('name', $role)->first();
                        LeaveRoles::updateOrCreate(
                            ['leave_id' => $leave->id],
                            [
                                "leave_id" => $leave->id,
                                'role_id' => $curentRole->id,
                            ]
                        );
                    }
                }

                //assigning direct permissions to the user
                foreach ($permissionNames as $permission) {
                    // assign the delegated staff the permission if they do not have it already
                    if (!in_array($permission, $delegatedUserPermissionNames)) {
                        $delegatedUser->givePermissionTo($permission);
                        //update leave roles
                        $curentPermission = ModelsPermission::where('name', $permission)->first();
                        LeaveRoles::updateOrCreate(
                            ['leave_id' => $leave->id],
                            [
                                "leave_id" => $leave->id,
                                'permission_id' => $curentPermission->id,
                            ]
                        );
                    }
                }

                //updating staff leave on staffprofile
                $currentStaffProfile->isOnLeave = true;
                $currentStaffProfile->leaveOIC = $delegateStaffProfile->email;
                $currentStaffProfile->save();
            }
            //......
            //
            // Send mail to staff about their Leave Schedule being submitted
            // $staffDelegated = StaffProfile::where('email', $leave->delegated_to)->first();

            // $start_date = Carbon::parse($leave->start);
            // $end_date = Carbon::parse($leave->end);

            // $diff = $start_date->diffInDays($end_date);

            // $emailInfo = [
            //     'requester_name' => auth()->user()->name,
            //     'no_of_days' => $diff,
            //     'start_date' => date_format($start_date, "d/M/Y"),
            //     'end_date' => date_format($end_date, "d/M/Y"),
            //     'delegated_to' => $staffDelegated->name,
            // ];

            // $log = EmailLog::create([
            //     'to' => auth()->user()->email,
            //     'description' => 'Delegation of Roles'
            // ]);

            // $log->updateCode('Delegation of Roles');

            // try {
            //     $mail = new LeaveSchedule($emailInfo, $log);

            //     $log->update([
            //         "body" => $mail->render()
            //     ]);

            //     Mail::to(auth()->user()->email)->send($mail);
            // } catch (\Exception $ex) {
            //     Log::error("UpdateSchedule :" . $ex->getMessage(), [$ex]);
            // }



            //TODO: send mail
            // Incase of a leave conflict, send conflict email to the requester
            // if (count($leaveConflict) > 0) {
            //     $confLog = EmailLog::create([
            //         'to' => auth()->user()->email,
            //         'description' => 'Leave Delegation Conflict'
            //     ]);

            //     $confLog->updateCode('Leave Delegation Conflict');

            //     try {
            //         $confMail = new LeaveDelegationConflict($emailInfo);

            //         $confLog->update([
            //             "body" => $confMail->render()
            //         ]);

            //         Mail::to(auth()->user()->email)->send($confMail);
            //     } catch (\Exception $ex) {
            //     }

            //     //
            //     // Send web notification as well
            //     $staff_profile = StaffProfile::where('email', auth()->user()->email)->first();
            //     Notification::create([
            //         'personal_id' => $staff_profile->personal_id,
            //         'action' => env('APP_URL') . '/delegate-roles',
            //         'description' => "Possible delegation conflict. Please review",
            //     ]);
            // }



            //
            // Send email to delegated staff letting them know that tasks of a person going on leave
            // have been assigned to them
            // $emailInfoTwo = [
            //     'receiver_name' => $staffDelegated->name,
            //     'requester_name' => auth()->user()->name,
            //     'start_date' => date_format($start_date, "d/M/Y"),
            //     'end_date' => date_format($end_date, "d/M/Y"),
            //     'start_leave_today' => $start_date->isToday() ? 1 : 0,
            // ];
            //TODO: send mail
            // $logTwo = EmailLog::create([
            //     'to' => $staffDelegated->email,
            //     'description' => 'Delegation of Roles'
            // ]);

            // $logTwo->updateCode('Delegation of Roles');

            // try {
            //     $mailTwo = new LeaveScheduleDelegate($emailInfoTwo, $logTwo);

            //     $logTwo->update([
            //         "body" => $mailTwo->render()
            //     ]);

            //     Mail::to($staffDelegated->email)->send($mailTwo);
            // } catch (\Exception $ex) {
            // }

            //TODO: Implement the depRep special case
            // $delegateToDepRep = $this->staff_dep_id != null ? StaffProfile::find($this->staff_id) : null;
            // $delegate = $staffDelegated;
            // if ($delegateToDepRep != null) {
            // $leave = Leave::updateOrCreate([
            //     "personal_id" => $delegate->personal_id,
            //     "start" => $this->start_date,
            //     "end" => $this->end_date,
            // ], [
            //     "start" => $this->start_date,
            //     "end" => $this->end_date,
            //     "status" => "deputizing",
            //     "name" => $delegate->name,
            //     "days" => $days,
            //     'reason_for_leave' => 'is deputizing ' . $delegate->name . 'as (OIC) ' . $delegate->position_text . ' who is deputizing ' . $staff->name . ' as (OIC) ' . $staff->position_text . 'who is unavailable on ' . $reason_for_leave
            // ]);

            // LeaveDelegation::updateOrCreate([
            //     'leave_id' => $leave->id,
            //     "start" => $this->start_date,
            //     "end" => $this->end_date,
            // ], ['delegated_role_to' => $delegateToDepRep->personal_id]);


            // $isPast = (time() - strtotime($this->start_date . ' 00:00:00')) > 0;

            // $dates = Utils::datesFromIntervals($this->start_date, $this->end_date);
            // $scheduleUUID = Utils::generateUUID();

            // $createdBy = StaffProfile::where('email', auth()->user()->email)->first();

            // foreach ($dates as $date) {
            //     CalendarSchedule::create([
            //         'staff_profile_id' => $staff->id,
            //         'platform' => 'LEAVE',
            //         'title' => 'Leave',
            //         'description' => $staff->name . ' is on leave',
            //         'scheduled_date' => $date,
            //         'schedule_uuid' => $scheduleUUID,
            //         'created_by_id' => $createdBy->id,
            //         'metadata' => json_encode(['leave_id' => $leave->id, 'delegated_roles_to' => $delegate->id]),
            //     ]);
            // }

            //TODO: implement emails
            // $this->dispatchEmailDelegator($leave,  $delegate, $delegateToDepRep, $isPast);
            // $this->dispatchEmailDelegatee($leave,  $delegate, $delegateToDepRep, $isPast);
            // $this->dispatchEmail($leave,  $delegate, $delegateToDepRep);
            //  }
        }


        return apiResponse('success');
    }

    public function deleteLeave(Request $request)
    {
        $leave = Leave::where("id", $request->leave_id)->first();
        $currentStaffProfile = StaffProfile::where('email', $leave->leaving_staff)->first();
        //check if user still has active leave schedules
        $userLeaves = Leave::where('leaving_staff', $leave->leaving_staff)->whereDate('end', '>=', date('Y-m-d'))->get();




        //revoke roles and permissions
        //Roles (and direct permissions) Assigned during leave
        $leaveRoles = LeaveRoles::where('leave_id', $leave->id)->get();

        //cascade to roles only if the leave schedule doesnt have roles
        if (count($leaveRoles) > 0) {
            $delegateStaffProfile = StaffProfile::where('email', $leave->delegated_to)->first();

            //get the roles and permissions of the staff going on leave
            $delegatedUser = User::where('email', $delegateStaffProfile->email)->first();

            foreach ($leaveRoles as $leaverole) {
                if ($leaverole->role_id != null) {
                    $roleToRevoke = Role::where('id', $leaverole->role_id)->first();
                    $delegatedUser->removeRole($roleToRevoke->name);
                }
                if ($leaverole->permission_id != null) {
                    $permissionToRevoke = ModelsPermission::where('id', $leaverole->permission_id)->first();
                    $delegatedUser->revokePermissionTo($permissionToRevoke->name);
                }

                //delete leave role
                $leaverole->delete();
            }
        }

        // delete delegations and leave
        LeaveDelegation::where("leave_id", $leave->id)->delete();
        $leave->delete();

        if (count($userLeaves) > 0) {
            //updating staff leave on staffprofile
            $currentStaffProfile->isOnLeave = false;
            $currentStaffProfile->leaveOIC = null;
            $currentStaffProfile->save();
        } else {
            //get the latest leave
            $latestLeave = Leave::where('leaving_staff', $leave->leaving_staff)->whereDate('end', '>=', date('Y-m-d'))->latest()->first();
            $delegatedStaffofLastsetLeave = StaffProfile::where('email', $latestLeave->delegated_to)->first();
            //updating staff leave on staffprofile
            $currentStaffProfile->isOnLeave = true;
            $currentStaffProfile->leaveOIC = $delegatedStaffofLastsetLeave->email;
            $currentStaffProfile->save();
        }
        return 'Success';
    }

    //Service Providers
    public function registerServiceProvider(Request $request)
    {

        $randomPassword = generateRandomString(12);



        $createUserObj = new CreateNewUser;

        $createUserObj->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $randomPassword,
            'password_confirmation' => $randomPassword,
            'terms' => true,
            'newStaff' => true,
        ]);

        $serviceProvider = ServiceProvider::updateOrCreate([
            "email" => $request->email,
        ], [
            "name" => $request->name,
            "email" => $request->email,
            'user_id' => rand(280, 2890),
            "vendor_number" => $request->vendorNumber,
        ]);

        $userAccount = User::where('email', $serviceProvider->email)->first();
        $userAccount->email_verified_at = date("Y-m-d H:i:s");
        $userAccount->user_type = 'sp';
        $userAccount->status = 'active';
        $userAccount->save();
        $serviceProvider->user_id = $userAccount->id;
        $serviceProvider->is_authenticated = true;
        $serviceProvider->save();

        //TODO: Send Emails

        //To New Created account
        try {
            $subject =  "Your account has been created as a Service Provider.";
            $mail = new TemporaryPasswordNewAccountMail($serviceProvider->name, $randomPassword, $serviceProvider->email);
            $staff = ServiceProvider::where('email', $userAccount->email)->first();
            globalSendEmailServiceProvider($staff, $subject, $mail, null, false, null, [
                "personal_id" => $staff->email,
                "action" => env('APP_URL'),
                "description" => "Your account has been created you can proceed with using the platform",
                "submitted_by" => "Platform",
                "title" => $subject,
                "profile_photo_path" => null
            ]);
        } catch (\Exception $ex) {
            Log::error("Send Mail Error: " . $ex);
        }


        return apiResponse('success');
    }

    public function activateServiceProvider(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $user->status = 'active';
        $user->save();

        $sp = ServiceProvider::where('email', $request->email)->first();
        $sp->is_authenticated = true;
        $sp->save();

        //TODO: Send Emails

        return apiResponse('success');
    }

    public function deactivateServiceProvider(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $user->status = 'disabled';
        $user->save();

        $sp = ServiceProvider::where('email', $request->email)->first();
        $sp->is_authenticated = false;
        $sp->save();

        //TODO: Send Emails
        return apiResponse('success');
    }

    public function downloadSPUploadBatchFile()
    {
        return response()->download(public_path('storage/templates/ServiceProvidersIPTemplate.xlsx'));
    }
    public function batchSPUpload(Request $request)
    {
        $data = $request->dataArray;
        $newStaff = [];

        foreach ($data as $staff) {


            $email =  $staff['Email'];
            if (!preg_match("/^\S+@\S+\.\S+$/", $email)) {
                continue;
            }
            $randomPassword = "unicef#" . mt_rand(1000, 9999);;


            $serviceProvider = ServiceProvider::updateOrCreate(
                [
                    "email" => isset($staff['Email']) ? $staff['Email'] : null,
                ],
                [
                    "name" => isset($staff['Name']) ? $staff['Name'] : null,
                    "email" => isset($staff['Email']) ? $staff['Email'] : null,
                    'user_id' => rand(280, 2890),
                    "vendor_number" => isset($staff['Vendor No']) ? $staff['Vendor No'] : null,
                    "street" => isset($staff['Sreet/House Number']) ? $staff['Sreet/House Number'] : null,
                    "phone" => isset($staff['Telephone']) ? $staff['Telephone'] : null,

                ]
            );

            /**
             * Note:
             * If a Service provider already has an account do not create a new one
             * but incase this already existing account is disabled then activate it
             */
            $userAccount = User::where('email', $serviceProvider->email)->first();

            if ($userAccount != null) {
                $userAccount = User::where('email', $serviceProvider->email)->first();
                $userAccount->name = $serviceProvider->name;

                $userAccount->status = 'active';
                $userAccount->save();

                $serviceProvider->user_id = $userAccount->id;
                $serviceProvider->is_authenticated = true;
                $serviceProvider->save();
                continue;
            } else {
                //create a new account
                $createUserObj = new CreateNewUser;

                $createUserObj->create([
                    'name' => $serviceProvider->name,
                    'email' => $serviceProvider->email,
                    'password' => $randomPassword,
                    'password_confirmation' => $randomPassword,
                    'terms' => true,
                    'newStaff' => false, // this is only for UNICEF staff
                ]);

                $userAccount = User::where('email', $serviceProvider->email)->first();
                $userAccount->email_verified_at = date("Y-m-d H:i:s");
                $userAccount->user_type = 'sp';
                $userAccount->status = 'active';
                $userAccount->save();
                $serviceProvider->user_id = $userAccount->id;
                $serviceProvider->is_authenticated = true;
                $serviceProvider->save();

                //TODO: Send Emails

                //To New Created account
                try {
                    $subject =  "Your account has been created as a Service Provider.";
                    $mail = new TemporaryPasswordNewAccountMail($serviceProvider->name, $randomPassword, $serviceProvider->email);
                    $staff = ServiceProvider::where('email', $userAccount->email)->first();
                    globalSendEmailServiceProvider($staff, $subject, $mail, null, false, null, [
                        "personal_id" => $staff->email,
                        "action" => env('APP_URL'),
                        "description" => "Your account has been created you can proceed with using the platform",
                        "submitted_by" => "Platform",
                        "title" => $subject,
                        "profile_photo_path" => null
                    ]);
                } catch (\Exception $ex) {
                    Log::error("Send Mail Error: " . $ex);
                }
            }


            //send email to account created with temporary password


        }
        return response()->json(['success' => 200, 'newStaff' => $newStaff]);
    }

    public function loadAddStaffDetails()
    {
        $sections = Section::query()->select('id', 'name')->get();
        $pillars = Pillar::query()->select('id', 'name')->get();
        $organisationUnits = OrganisationUnit::query()->select('id', 'name')->get();
        $staff = StaffProfile::query()->select('id', 'name', 'position_id')->get();
        $categories = Category::query()->select('id', 'name')->get();
        $appointmentType = AppointmentType::query()->select('id', 'name')->get();
        $airtimeCategory = StaffCategory::query()->select('id', 'category', 'usd_allocations')->get();
        $dollar_rate = DollarRate::query()->where('currency', 'SSP')->value('rate') ?? 1561.427;

        return apiResponse([
            'sections' => $sections,
            'pillars' => $pillars,
            'organisationUnits' => $organisationUnits,
            'staff' => $staff,
            'categories' => $categories,
            'appointmentType' => $appointmentType,
            'airtime_category' => $airtimeCategory,
            'dollar_rate' => $dollar_rate,
        ]);
    }

    public function blacklistEmail(Request $request)
    {
        $ignoreEmails = json_decode($request->emails);
        foreach ($ignoreEmails as $email) {
            IgnoredEmail::create([
                'user_id' => auth()->user()->id,
                'email' => auth()->user()->email,
                'staff' => auth()->user()->name,
                'subject' => $email,
                'is_deactivated' => true
            ]);
        }
    }
    public function removeblacklistemail(Request $request)
    {
        $blackEmail = IgnoredEmail::where('id', $request->email)->first();
        $blackEmail->is_deactivated = false;
        $blackEmail->save();
    }

    public function editRole(Request $request){

        $roleToEdit = Role::where('id', $request->roleID)->first();
         $newRolePermissions = json_decode($request->rolePermisions);
        $roleToEdit->name = $request->roleName;
        $roleToEdit->description = $request->roleDescriptsion;
        $roleToEdit->save();

        //revoking all role permissions
        $rolePermisions = Role::findByName($roleToEdit->name,'web')->permissions;

        if( !is_null($rolePermisions) && count($rolePermisions) > 0){
            foreach ($rolePermisions as $rolePermision) {
                $roleToEdit->revokePermissionTo($rolePermision->name);
            }
        }
       

        //assigning role new permissions;
        if(!is_null($newRolePermissions) && count($newRolePermissions) > 0){
            foreach ($newRolePermissions as $newRolePermission) {
                if(is_string($newRolePermission)){
                    $roleToEdit->givePermissionTo($newRolePermission);
                }else{
                    $roleToEdit->givePermissionTo($newRolePermission->name);
                }
                
            }
        }
      

        return apiResponse('success');
    }
}
