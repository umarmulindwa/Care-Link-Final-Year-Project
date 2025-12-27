<?php

namespace App\Actions\Fortify;

use App\Mail\AccountActivationMail;
use App\Models\ServiceProvider;
use App\Models\StaffProfile;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use phpDocumentor\Reflection\Types\Boolean;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public String $vendorNo;
    public  $isUnicefStaff;

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        try {

            $this->isUnicefStaff = array_key_exists('newStaff', $input) ? true : false;

            //for unicefstaff
            if ($this->isUnicefStaff == true) {


                Validator::make($input, [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'password' => array_merge($this->passwordRules(),['confirmed']),
                    'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
                ])->validate();
            } else {
                $this->vendorNo = $input['vendor_number'];

                //for service providers
                Validator::make($input, [
                    'name' => ['required', 'string', 'max:255'],
                    'vendor_number' => ['required', 'unique:service_providers'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'password' => $this->passwordRules(),
                    'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
                ])->validate();
            }


            return DB::transaction(function () use ($input) {
                return tap(User::create([
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'password' => Hash::make($input['password']),
                    //registered users will be service providers or new unicef staff
                    'user_type' => array_key_exists('newStaff', $input) ? 'newstaff' : 'sp',
                    //service provider accounts will not be active on creation
                    'status' => array_key_exists('newStaff', $input) ? 'active' : 'pending',
                    // active directory
                    'active_directory' => str_replace(" ", "", $input['name']),
                ]), function (User $user) {

                    //create service provider if the registering user is not a new unicef staff
                    if ($this->isUnicefStaff == false) {
                        $this->createVendor($user, $this->vendorNo);
                    }
                    $this->createTeam($user);
                });
            });
        } catch (\Throwable $th) {
            Log::error("User Creation Error: " . $th->getMessage(),['errors'=>$th->getTrace()]);
            throw $th;

        }
    }

    /**
     * create SERVICE PROVIDER
     *
     */

    protected function createVendor(User $user, String $vendor)
    {
        $sp = ServiceProvider::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'vendor_number' => $vendor,
            'is_authenticated' => false,
        ]);

        Log::debug('vendor01',['vendor'=>$sp]);

        if ($sp !== null) {

            Log::debug('vendor',['vendor'=>$sp]);
            //getting all super Admins
            $admins = User::select('email', 'name')->permission('s_admin')->get();

            // sending support request email to all  super admins
            if (count($admins) > 0) {

                foreach ($admins as $admin) {
                    //checking whether the staff is on Leave
                    $staff = StaffProfile::where('email', $admin->email)->first();
                    try {
                        $subject =  "New Service Provider Registered.";
                        $mail = new AccountActivationMail($staff->name, $sp);
                        globalSendEmail($staff, $subject, $mail, null, false, null, [
                            "personal_id" => $staff->email,
                            "action" => env('APP_URL') . "/staff_register",
                            "description" => "A new Service Provider has been registered, please activate their account.",
                            "submitted_by" => 'Platform',
                            "title" => $subject,
                            "profile_photo_path" => null
                        ]);
                    } catch (\Exception $ex) {
                        Log::error("Send Mail ==New Service Provider Registered== Error: " . $ex->getMessage(),['error'=>$ex->getTrace()]);
                    }
                }
            }
        }
    }

    /**
     * Create a personal team for the user.
     */
    protected function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0] . "'s Team",
            'personal_team' => true,
        ]));
    }


}
