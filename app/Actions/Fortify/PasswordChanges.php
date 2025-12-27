<?php

namespace App\Actions\Fortify;

use App\Models\PasswordChangeHistory;
use App\Models\User;

/**
 *
 */
trait PasswordChanges
{
    /**
     * @param User $user
     * @param String $password
     * @return void
     */
    protected static function logPassword(User $user, String $password): void
    {
        $history = new PasswordChangeHistory([
            'previous_password' => $password,
        ]);

        $user->passwordHistories()->save($history);
    }
}
