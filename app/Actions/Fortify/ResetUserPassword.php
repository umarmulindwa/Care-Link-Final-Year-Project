<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    public function reset(User $user, array $input): void
    {
        try {
            Validator::make($input, [
                'password' => $this->passwordRules(),
            ])->validate();

            $user->forceFill([
                'password' => Hash::make($input['password']),
            ])->save();
        } catch (\Throwable $th) {
            //throw $th;
            Log::error("User Reset Password Error: " . $th->getMessage(),['errors'=>$th->getTrace()]);

        }

    }
}
