<?php

namespace App\Domains\Auth\Actions;

use App\Models\EmailOtp;
use App\Models\User;
use App\Notifications\EmailOtpNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RequestEmailOtp
{
    public function execute(string $email): bool
    {
        $user = User::query()->where('email', $email)->whereNull('email_verified_at')->first();

        if (! $user) {
            return false;
        }

        $code = (string) random_int(100000, 999999);

        DB::transaction(function () use ($user, $code): void {
            EmailOtp::query()->where('user_id', $user->id)->whereNull('used_at')->update(['used_at' => now()]);

            EmailOtp::create([
                'user_id' => $user->id,
                'token' => Hash::make($code),
                'expires_at' => now()->addMinutes(10),
            ]);

            DB::afterCommit(fn () => $user->notify(new EmailOtpNotification($code)));
        });

        return true;
    }
}
