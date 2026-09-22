<?php

namespace App\Domains\Auth\Actions;

use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LogoutUser
{
    /**
     * Blacklist the bearer token so it cannot be replayed.
     *
     * A request without a usable token has nothing to revoke, so JWT failures
     * are swallowed to keep logout idempotent.
     */
    public function execute(): void
    {
        try {
            Auth::guard('api')->logout();
        } catch (JWTException) {
            //
        }
    }
}
