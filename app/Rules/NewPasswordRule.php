<?php

namespace App\Rules;

use App\Models\Config;
use App\Models\PasswordChangeHistory;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use function Symfony\Component\Translation\t;

class NewPasswordRule implements ValidationRule
{
    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): array|string
    {
        return 'The password has been used before and cannot be reused.';
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $passwords_to_check = Config::query()->value('passwords_to_check');
        $loggedUser = Auth::id();

        if($loggedUser){
            $previous_passwords = PasswordChangeHistory::query()
                ->where('user_id',$loggedUser)
                ->latest()
                ->limit($passwords_to_check)
                ->pluck('previous_password')
                ->toArray();

            $used = false;

            foreach ($previous_passwords as $password) {
                if (Hash::check($value, $password)) {
                    $used = true;
                    break;
                }
            }

            if($used){
                $fail($this->message());
            }
        }

    }
}
