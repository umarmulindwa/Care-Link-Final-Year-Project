<?php

namespace App\Actions\Fortify;

use App\Models\Config;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules,PasswordChanges;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
       try {
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => $this->passwordRules(),
        ], [
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ])->validateWithBag('updatePassword');

        $password = Hash::make($input['password']);

        self::logPassword($user,$password);

        $user->forceFill([
            'password' => $password,
            'password_expires_at' => Carbon::now()->addDays(Config::query()->value('password_expiry_days')),
        ])->save();
       } catch (\Throwable $th) {
        //throw $th;
        Log::error("User Reset Password Error: " . $th->getMessage(),['errors'=>$th->getTrace()]);

       }


    }

}
