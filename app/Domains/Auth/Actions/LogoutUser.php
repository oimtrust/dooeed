<?php

namespace App\Domains\Auth\Actions;

use App\Models\User;

class LogoutUser
{
    public function execute(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
