<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\StaffProfile;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if (
            $input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
            ])->save();
        }

        //updating the staffprofile
        StaffProfile::updateOrCreate(["email" =>  $input['email'],], [
            "personal_id" => $input['personalId'],
            "index_number" => $input['indexNo'],
            "name" =>  $input['name'],
            "email" => $input['email'],
            "position_text" => $input['position'],
            "position_id" => $input['positionID'],
            "section" => $input['section'],
            "organisation_unit" => $input['organisationUnit'],
            "reports_to" => $input['reportTo'],
            "category" => $input['category'],
            "gender" => $input['gender'],
            "appointment_type" => $input['appointmentType'],
            "date_began_working_with_unicef" => $input['dateJoinedGlobal'],
            "date_began_working_with_unicef_country_level" => $input['dateJoinedCountry'],
            "date_contract_end" => $input['contractEndDate'],
            "marital_status" => $input['maritalStatus'],
            "phone" => $input['phoneNumber'],
            "phone_whatsapp" => $input['whatsappNo'],
            "address" => $input['address'],
            "pillar" => $input['pillar'],
        ]);
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        try {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'email_verified_at' => null,
            ])->save();
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $th) {
            //throw $th;
            Log::error("User Email Verification Password Error: " . $th->getMessage(),['errors'=>$th->getTrace()]);

        }
    }
}
